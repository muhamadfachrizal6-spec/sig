<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Company;
use Livewire\Component;

class KeyStatics extends Component
{
    public $profitData = [];
    public $priceData = [];
    
    public $incomeStatementData = [];
    public $financialPositionData = [];
    public $dividendData = [];

    public $account = 'All';
    public $timeframe = '3 Years';

    public $modalPopupData = [];

    protected $listeners = ['companyChanged'];

    public function updatedAccount()
    {
        // Menggunakan data perusahaan terakhir yang tersedia
        $company = Company::with(['revenues', 'financialPositions', 'dividends'])->first();

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
            $company = Company::with(['revenues', 'financialPositions', 'dividends'])
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
        $this->incomeStatementData = [];
        $this->financialPositionData = [];
        $this->dividendData = [];
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
        $filteredRevenues = $company->revenues
        ->filter(function ($revenue) use ($startYear, $currentYear) {
            return $revenue->year >= $startYear && $revenue->year <= $currentYear;
        })
        ->sortBy([
            ['year', 'asc'], // Mengurutkan berdasarkan tahun secara menaik
            ['quarter', 'asc'] // Mengurutkan berdasarkan kuartal secara menaik
        ]);

        $filteredFinancialPositions = $company->financialPositions
        ->filter(function ($position) use ($startYear, $currentYear) {
            return $position->year >= $startYear && $position->year <= $currentYear;
        })
            ->sortBy([
                ['year', 'asc'],
                ['quarter', 'asc']
            ]);

        $filteredDividends = $company->dividends
        ->filter(function ($dividend) use ($startYear, $currentYear) {
            return $dividend->year >= $startYear && $dividend->year <= $currentYear;
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
        if ($this->account === 'IncomeStatement' || $this->account === 'All') {
            $this->incomeStatementData = [
                'categories' => $filteredRevenues->map(function ($revenue) {
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
                        'name' => 'Revenue',
                        'data' => $filteredRevenues->pluck('revenue')->map(function ($value) {
                            return floatval(str_replace(' B', '', $value));
                        })->toArray(),
                    ],
                    [
                        'name' => 'Gross Profit',
                        'data' => $filteredRevenues->pluck('gross_profit')->map(function ($value) {
                            return floatval(str_replace(' B', '', $value));
                        })->toArray(),
                    ],
                    [
                        'name' => 'Net Profit',
                        'data' => $filteredRevenues->pluck('net_profit')->map(function ($value) {
                            return floatval(str_replace(' B', '', $value));
                        })->toArray(),
                    ],
                ],
            ];
        }

        if ($this->account === 'FinancialPosition' || $this->account === 'All') {
            $this->financialPositionData = [
                'categories' => $filteredFinancialPositions->map(function ($revenue) {
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
                        'name' => 'Asset',
                        'data' => $filteredFinancialPositions->pluck('asset')->map(function ($value) {
                            return floatval(str_replace(' B', '', $value));
                        })->toArray(),
                    ],
                    [
                        'name' => 'Liability',
                        'data' => $filteredFinancialPositions->pluck('liability')->map(function ($value) {
                            return floatval(str_replace(' B', '', $value));
                        })->toArray(),
                    ],
                    [
                        'name' => 'Equality',
                        'data' => $filteredFinancialPositions->pluck('equality')->map(function ($value) {
                            return floatval(str_replace(' B', '', $value));
                        })->toArray(),
                    ],
                ],
            ];
        }

        if ($this->account === 'Dividend' || $this->account === 'All') {
            $this->dividendData = [
                'categories' => $filteredDividends->map(function ($revenue) {
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
                        'name' => 'Dividend Per Sheet',
                        'data' => $filteredDividends->pluck('dividend_per_sheet')->map(function ($value) {
                            return floatval(str_replace(' B', '', $value));
                        })->toArray(),
                    ],
                    [
                        'name' => 'Yield',
                        'data' => $filteredDividends->pluck('yield')->map(function ($value) {
                            return floatval(str_replace(' %', '', $value));
                        })->toArray(),
                    ],
                ],
            ];
        }

        $this->dispatchBrowserEvent('update-chart', [
            'incomeStatementData' => $this->incomeStatementData,
            'financialPositionData' => $this->financialPositionData,
            'dividendData' => $this->dividendData,
            'profitData' => $this->profitData,
            'modalPopupData' => $this->modalPopupData,
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard.key-statics');
    }
}