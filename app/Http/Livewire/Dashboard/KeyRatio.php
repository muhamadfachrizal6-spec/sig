<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Company;
use Livewire\Component;

class KeyRatio extends Component
{
    public $profitData = [];
    public $priceData = [];

    public $profitabilityRatioData = [];
    public $relativeRatioData = [];
    public $liquidityRatioData = [];

    public $account = 'All';
    public $timeframe = '3 Years';
    public $periode = 'quarterly';

    public $modalPopupData = [];

    protected $listeners = ['companyChanged'];

    public function updatedAccount()
    {
        // Menggunakan data perusahaan terakhir yang tersedia
        $company = Company::with(['profitabilityRatios', 'relativeRatios', 'liquidityRatios'])->first();

        // Panggil companyChanged untuk memperbarui data
        $this->companyChanged($company);
    }

    public function updatedTimeframe()
    {
        $this->updatedAccount();
    }

    public function updatedGraphic()
    {
        $this->updatedAccount();
    }

    public function companyChanged($company)
    {
        if (is_array($company)) {
            $company = Company::with(['profitabilityRatios', 'relativeRatios', 'liquidityRatios'])
                ->findOrFail($company['id']);
        }

        $currentYear = now()->year;
        $yearsAgo = 0;

        if ($this->timeframe === '3 Years') {
            $yearsAgo = 3;
        } elseif ($this->timeframe === '5 Years') {
            $yearsAgo = 5;
        } elseif ($this->timeframe === '10 Years') {
            $yearsAgo = 10;
        }

        $startYear = $currentYear - $yearsAgo;

        // Reset data sebelum mengisi ulang sesuai filter
        $this->profitabilityRatioData = [];
        $this->relativeRatioData = [];
        $this->liquidityRatioData = [];
        $this->profitData = [];
        $this->priceData = [];
        $this->modalPopupData = [];

        $filteredMarketShares = $company->marketShares
            ->sortByDesc('year')
            ->filter(function ($marketShare) use ($startYear, $currentYear) {
                // Ambil data antara $startYear dan currentYear
                return $marketShare->year >= $startYear && $marketShare->year <= $currentYear;
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

        // Filter dan sorting berdasarkan tahun terbaru
        $filteredprofitabilityRatio = $company->profitabilityRatios
        ->filter(function ($profitabilityRatio) use ($startYear, $currentYear) {
            return $profitabilityRatio->year >= $startYear && $profitabilityRatio->year <= $currentYear;
        })
        ->sortBy([
            ['year', 'asc'], // Mengurutkan berdasarkan tahun secara menaik
            ['quarter', 'asc'] // Mengurutkan berdasarkan kuartal secara menaik
        ]);

        $filteredrelativeRatio = $company->relativeRatios
        ->filter(function ($relativeRatios) use ($startYear, $currentYear) {
            return $relativeRatios->year >= $startYear && $relativeRatios->year <= $currentYear;
        })
            ->sortBy([
                ['year', 'asc'],
                ['quarter', 'asc']
            ]);

        $filteredliquidityRatio = $company->liquidityRatios
        ->filter(function ($liquidityRatios) use ($startYear, $currentYear) {
            return $liquidityRatios->year >= $startYear && $liquidityRatios->year <= $currentYear;
        })
            ->sortBy([
                ['year', 'asc'],
                ['quarter', 'asc']
            ]);

        $this->modalPopupData = [
            'categories' => $filteredMarketShares->map(function ($marketShare) {
                return [
                    'year' => $marketShare->year,
                    'quarter' => $marketShare->quarter
                ];
            })->sortBy([
                ['year', 'asc'],
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
                    'data' => $filteredMarketShares->pluck('price')->map(function ($value) {
                        return floatval($value);
                    })->toArray(),
                ],
            ],
        ];

        // Kondisi untuk setiap account type dan update data untuk chart terkait
        if ($this->account === 'profitabilityRatioData' || $this->account === 'All') {
            $this->profitabilityRatioData = [
                'categories' => $filteredprofitabilityRatio->map(function ($revenue) {
                    return [
                        'year' => $revenue->year,
                        'quarter' => $revenue->quarter
                    ];
                })->sortBy([
                    ['year', 'asc'],
                    ['quarter', 'asc']
                ])->map(function ($item) {
                    return $item['year'] . ' - ' . $item['quarter'];
                })->values()->toArray(),
                'series' => [
                    [
                        'name' => 'ROE',
                        'data' => $filteredprofitabilityRatio->pluck('ROE')->map(function ($value) {
                            return floatval(str_replace(' B', '', $value));
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
                'categories' => $filteredrelativeRatio->map(function ($revenue) {
                    return [
                        'year' => $revenue->year,
                        'quarter' => $revenue->quarter
                    ];
                })->sortBy([
                    ['year', 'asc'],
                    ['quarter', 'asc']
                ])->map(function ($item) {
                    return $item['year'] . ' - ' . $item['quarter'];
                })->values()->toArray(),
                'series' => [
                    [
                        'name' => 'EPS',
                        'data' => $filteredrelativeRatio->pluck('EPS')->map(function ($value) {
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
                        'name' => 'BPVS',
                        'data' => $filteredrelativeRatio->pluck('BPVS')->map(function ($value) {
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
                'categories' => $filteredliquidityRatio->map(function ($revenue) {
                    return [
                        'year' => $revenue->year,
                        'quarter' => $revenue->quarter
                    ];
                })->sortBy([
                    ['year', 'asc'],
                    ['quarter', 'asc']
                ])->map(function ($item) {
                    return $item['year'] . ' - ' . $item['quarter'];
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