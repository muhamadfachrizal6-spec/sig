<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Company;
use Livewire\Component;

class KeyRatio extends Component
{
    public $selectedCompany;
    public $profitData = [];
    public $priceData = [];

    public $profitabilityRatioData = [];
    public $relativeRatioData = [];
    public $liquidityRatioData = [];

    public $account = 'All';
    public $timeframe = [];
    public $periode = 'quarterly';

    public $modalPopupData = [];
    public $isTimeframeOpen = false;
    public $isYearsOpen = false;

    protected $listeners = ['companyChanged'];

    public $yearsAvailable = [];
    public $selectedYears = [];

    public $tempTimeframe;
    public $tempSelectedYears = [];

    public function mount()
    {
        $this->timeframe = '3 Years';
        $currentYear = now()->year;
        $yearsAgo = match ($this->timeframe) {
            '3 Years' => 3,
            '5 Years' => 5,
            '10 Years' => 10,
            default => 3,
        };
        $startYear = $currentYear - $yearsAgo + 1;
        $this->selectedYears = range($startYear, $currentYear);


        // Mengambil daftar tahun yang ada di database (marketShares, profitabilityRatios, dll)
        $this->yearsAvailable = Company::with(['profitabilityRatios', 'relativeRatios', 'liquidityRatios'])
        ->get()
            ->flatMap(function ($company) {
                return $company->profitabilityRatios->pluck('year')
                ->merge($company->relativeRatios->pluck('year'))
                ->merge($company->liquidityRatios->pluck('year'));
            })
            ->unique()
            ->filter(function ($year) use ($currentYear) {
                return $year > 0 && $year <= $currentYear;
            })
            ->sortDesc()
            ->values()
            ->toArray();

        // Muat data awal
        if (!empty($this->selectedCompany)) {
            $this->companyChanged($this->selectedCompany, $this->selectedYears);
        }

        $this->tempTimeframe = $this->timeframe;
        $this->tempSelectedYears = $this->selectedYears;
    }

    public function submitYears()
    {
        if (!empty($this->timeframe)) {
            // Reset timeframe jika tahun spesifik dipilih
            $this->timeframe = null;
            session()->flash('message', 'Timeframe telah direset karena Anda memilih filter per tahun.');
        }

        if (empty($this->selectedYears)) {
            session()->flash('message', 'Tidak ada tahun yang dipilih.');
            return;
        }

        // Panggil filter data berdasarkan tahun yang dipilih
        $this->companyChanged($this->selectedCompany, $this->selectedYears);
    }

    public function updatedAccount()
    {
        if ($this->selectedCompany) {
            $this->companyChanged($this->selectedCompany);
        }
    }

    public function updatedTimeframe($value)
    {
        // Kosongkan filter per tahun langsung ketika timeframe dipilih
        $this->selectedYears = [];

        $this->dispatchBrowserEvent('close-modal', ['modal' => 'timeframeModal']);

        // Update selectedYears berdasarkan timeframe yang dipilih
        $currentYear = now()->year;
        $yearsAgo = match ($value) {
            '3 Years' => 3,
            '5 Years' => 5,
            '10 Years' => 10,
            default => 3,
        };
        $startYear = $currentYear - $yearsAgo + 1;

        $this->selectedYears = range($startYear, $currentYear);

        $this->timeframe = $value;

        // Panggil logika filter data
        if ($this->selectedCompany) {
            $this->companyChanged($this->selectedCompany, $this->selectedYears);
        }

        // Flash message untuk feedback
        session()->flash('message', 'Filter per tahun telah direset karena Anda memilih timeframe.');
    }

    public function submitFilter()
    {
        if (!empty($this->timeframe)) {
            $this->updatedTimeframe($this->timeframe);
        } else {
            $this->companyChanged($this->selectedCompany, $this->selectedYears);
        }

        // Tutup modal setelah filter diterapkan
        $this->dispatchBrowserEvent('close-modal', ['modal' => 'timeframeModal']);

        // Flash message untuk memberikan feedback
        session()->flash('message', 'Filter telah diterapkan.');
    }

    public function updatedSelectedYears($value)
    {
        // Jika ada perubahan pada filter tahun, hapus timeframe
        if (!empty($value)) {
            $this->timeframe = null;

            // Panggil logika untuk memperbarui data dengan filter tahun spesifik
            if ($this->selectedCompany) {
                $this->companyChanged($this->selectedCompany, $this->selectedYears);
            }

            // Flash message untuk memberikan feedback
            session()->flash('message', 'Timeframe telah direset karena Anda memilih filter per tahun.');
        }
    }
    public function updatedPeriode()
    {
        if ($this->selectedCompany) {
            $this->companyChanged($this->selectedCompany);
        }
    }

    public function companyChanged($company, $years = null)
    {
        if (is_array($company)) {
            $company = Company::with(['profitabilityRatios', 'relativeRatios', 'liquidityRatios'])
                ->findOrFail($company['id']);
        }

        $this->selectedCompany = $company;
        $currentYear = now()->year;
        $yearsFilter = $years ?: $this->selectedYears; // Prioritaskan filter per tahun
        if (empty($yearsFilter)) {
            // Jika tidak ada selectedYears, gunakan timeframe
            $yearsAgo = match ($this->timeframe) {
                '3 Years' => 3,
                '5 Years' => 5,
                '10 Years' => 10,
                default => 3,
            };
            $startYear = $currentYear - $yearsAgo + 1;
            $yearsFilter = range($startYear, $currentYear);
        }

        // Reset data sebelum mengisi ulang sesuai filter
        $this->profitabilityRatioData = [];
        $this->relativeRatioData = [];
        $this->liquidityRatioData = [];
        $this->profitData = [];
        $this->priceData = [];
        $this->modalPopupData = [];

        $filteredMarketShares = $company->marketShares
            ->sortByDesc('year')
            ->filter(function ($marketShare) use ($yearsFilter) {
                return in_array($marketShare->year, $yearsFilter);
            });

        $this->profitData = $filteredMarketShares
            ->where('year', $currentYear)
            ->groupBy('year')
            ->map(function ($yearData) {
                return $yearData->groupBy('quarter')->map(function ($quarterData) {
                    return $quarterData->map(function ($data) {
                        return [
                        'quarter' => $data->quarter,
                        'value' => $data->growth_net_profit,
                    ];
                    });
                });
            });

        $this->priceData = $filteredMarketShares
            ->where('year', $currentYear)
            ->groupBy('year')
            ->map(function ($yearData) {
                return $yearData->groupBy('quarter')->map(function ($quarterData) {
                    return $quarterData->map(function ($data) {
                        return [
                            'quarter' => $data->quarter,
                            'value' => $data->price,
                        ];
                    });
                });
            });

        $yearsFilter = $this->selectedYears ?: range($startYear, $currentYear);

        $filteredprofitabilityRatio = $company->profitabilityRatios
            ->whereIn('year', $yearsFilter)
            ->sortByDesc('year');

        $filteredrelativeRatio = $company->relativeRatios
            ->whereIn('year', $yearsFilter)
            ->sortByDesc('year');

        $filteredliquidityRatio = $company->liquidityRatios
            ->whereIn('year', $yearsFilter)
            ->sortByDesc('year');

        if ($this->periode === 'annual') {
            $filteredprofitabilityRatio = $filteredprofitabilityRatio
                ->groupBy('year')
                ->map(function ($yearData) {
                $sorted = $yearData->sortByDesc('quarter');

                return $sorted
                    ->filter(function ($item) {
                        return $item->ROE != 0 || $item->GPM != 0 || $item->NPM != 0;
                    })
                    ->first() ?? $sorted->first();
            })
                ->filter();

            $filteredrelativeRatio = $filteredrelativeRatio
                ->groupBy('year')
                ->map(function ($yearData) {
                if ($this->periode === 'annual') {
                    return [
                        'year' => $yearData->first()->year,
                        'EPS' => $yearData->sum('EPS'),
                        'PER' => optional($yearData->filter(function ($item) {
                            return !is_null($item->PER) && $item->PER !== 0.0;
                        })->sortByDesc('quarter')->first())->PER,
                        'BVPS' => optional($yearData->filter(function ($item) {
                            return !is_null($item->BVPS) && $item->BVPS !== 0.0;
                        })->sortByDesc('quarter')->first())->BVPS,
                        'PBV' => optional($yearData->filter(function ($item) {
                            return !is_null($item->PBV) && $item->PBV !== 0.0;
                        })->sortByDesc('quarter')->first())->PBV,
                    ];
                }

                // Jika periode adalah quarterly, ambil data dari kuartal pertama
                $sorted = $yearData->sortByDesc('quarter');
                return $sorted
                    ->filter(function ($item) {
                        return $item->EPS != 0 || $item->PER != 0 || $item->BVPS != 0 || $item->PBV != 0;
                    })
                    ->first() ?? $sorted->first();
                })
                ->filter();
            

            $filteredliquidityRatio = $filteredliquidityRatio
                ->groupBy('year')
                ->map(function ($yearData) {
                $sorted = $yearData->sortByDesc('quarter');

                return $sorted
                    ->filter(function ($item) {
                        return $item->DAR != 0 || $item->DER != 0;
                    })
                    ->first() ?? $sorted->first();
                })
                ->filter();
        }

        // Sorting data
        $filteredprofitabilityRatio = $filteredprofitabilityRatio->sortBy([
            ['year', 'asc'],
            ['quarter', 'asc']
        ]);

        $filteredrelativeRatio = $filteredrelativeRatio->sortBy([
            ['year', 'asc'],
            ['quarter', 'asc']
        ]);

        $filteredliquidityRatio = $filteredliquidityRatio->sortBy([
            ['year', 'asc'],
            ['quarter', 'asc']
        ]);

        $this->modalPopupData = [
            'categories' => $filteredMarketShares->map(function ($marketShare) {
                return [
                    'year' => $marketShare->year,
                    'quarter' => $marketShare->quarter
                ];
            })->sortBy([['year', 'desc'],
                ['quarter', 'asc']
            ])->map(function ($item) {
                return $item['year'] . ' - ' . $item['quarter'];
            })->values()->toArray(),
            'series' => [
                [
                    'name' => 'Growth Net Profit',
                    'data' => $filteredMarketShares->pluck('growth_net_profit')->map(function ($value) {
                        return floatval(str_replace(' %', '', $value));
                    })->toArray(),
                ],
                [
                    'name' => 'Price',
                    'data' => $filteredMarketShares->pluck('price_popup')->map(function ($value) {
                        return floatval(str_replace(' %', '', $value));
                    })->toArray(),
                ],
            ],
        ];

        if ($this->account === 'profitabilityRatioData' || $this->account === 'All') {
            $this->profitabilityRatioData = [
                'categories' => $this->periode === 'annual'
                ? $filteredprofitabilityRatio->map(function ($ratio) use ($company) {
                    return $ratio->year;
                })->values()->toArray()
                    : $filteredprofitabilityRatio->map(function ($ratio) {
                        return $ratio->year . ' - ' . $ratio->quarter;
                    })->values()->toArray(),
                'series' => [
                    [
                        'name' => 'ROE',
                        'data' => $filteredprofitabilityRatio->pluck('ROE')->map(function ($value) {
                            return $value;
                        })->toArray(),
                    ],
                    [
                        'name' => 'GPM',
                        'data' => $filteredprofitabilityRatio->pluck('GPM')->map(function ($value) {
                            return floatval(str_replace(' B', '', $value));
                        })->toArray(),
                    ],
                    [
                        'name' => 'NPM',
                        'data' => $filteredprofitabilityRatio->pluck('NPM')->map(function ($value) {
                            return floatval(str_replace(' B', '', $value));
                        })->toArray(),
                    ],
                ],
            ];
        }

        if ($this->account === 'relativeRatioData' || $this->account === 'All') {
            $this->relativeRatioData = [
                'categories' =>
                $this->periode === 'annual'
                ?
                    $filteredrelativeRatio->map(function ($ratio) {
                        return $ratio['year'];
                    })
                ->filter()
                ->values()
                    ->toArray()
                    :
                    $filteredrelativeRatio->map(function ($ratio) {
                        return $ratio->year . ' - ' . $ratio->quarter;
                    })->values()->toArray(),
                'series' => [
                    [
                        'name' => 'EPS',
                        'data' => $this->periode === 'annual'
                            ? $filteredrelativeRatio->pluck('EPS')->toArray()
                            : $filteredrelativeRatio->pluck('EPS')->map(function ($value) {
                                return floatval(str_replace(' B', '', $value));
                            })->toArray(),
                    ],
                    [
                        'name' => 'PER',
                        'data' => $filteredrelativeRatio->pluck('PER')->map(function ($value) {
                            return floatval(str_replace(' B', '', $value));
                        })->toArray(),
                    ],
                    [
                        'name' => 'BVPS',
                        'data' => $filteredrelativeRatio->pluck('BVPS')->map(function ($value) {
                            return floatval(str_replace(' B', '', $value));
                        })->toArray(),
                    ],
                    [
                        'name' => 'PBV',
                        'data' => $filteredrelativeRatio->pluck('PBV')->map(function ($value) {
                            return floatval(str_replace(' B', '', $value));
                        })->toArray(),
                    ],
                ],
            ];
        }

        if ($this->account === 'liquidityRatioData' || $this->account === 'All') {
            $this->liquidityRatioData = [
                'categories' => $this->periode === 'annual'
                ? $filteredliquidityRatio->map(function ($ratio) {
                    return $ratio->year;
                })->values()->toArray()
                    : $filteredliquidityRatio->map(function ($ratio) {
                        return $ratio->year . ' - ' . $ratio->quarter;
                    })->values()->toArray(),
                'series' => [
                    [
                        'name' => 'DAR',
                        'data' => $filteredliquidityRatio->pluck('DAR')->map(function ($value) {
                            return floatval(str_replace(' B', '', $value));
                        })->toArray(),
                    ],
                    [
                        'name' => 'DER',
                        'data' => $filteredliquidityRatio->pluck('DER')->map(function ($value) {
                            return floatval(str_replace(' %', '', $value));
                        })->toArray(),
                    ],
                ],
            ];
        }

        $this->dispatchBrowserEvent('update-chart', [
            'profitabilityRatioData' => $this->profitabilityRatioData,
            'relativeRatioData' => $this->relativeRatioData,
            'liquidityRatioData' => $this->liquidityRatioData,
            'profitData' => $this->profitData,
            'modalPopupData' => $this->modalPopupData,
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard.key-ratio');
    }
}