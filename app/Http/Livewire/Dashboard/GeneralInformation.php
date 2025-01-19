<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Company;
use Livewire\Component;

class GeneralInformation extends Component
{
    public $company;
    public $revenueData = [];
    public $grossProfitData = [];
    public $netProfitData = [];
    public $financialPositionData = [];
    public $dividendData = [];
    public $profitabilityRatioData = [];
    public $relativeRatioData = [];
    public $liquidityRatioData = [];
    public $marketShare = [];
    public $growthNetProfit = null;
    public $marketCap = null;
    public $price = null;
    public $years = [];

    protected $listeners = ['companyChanged' => 'updateCompany'];

    public function mount($company)
    {
        if (is_array($company)) {
            $this->company = Company::with(['revenues', 'financialPositions', 'dividends', 'profitabilityRatios', 'relativeRatios', 'liquidityRatios', 'marketShares'])->findOrFail($company['id']);
        } else {
            $this->company = $company;
        }

        $this->updateCompany($this->company);
        $this->emitSelf('refresh');
    }

    public function updateCompany($company)
    {
        $currentYear = now()->year;
        $currentQuarter = "Q" . intdiv(now()->month - 1, 3) + 1;
        // dd($currentQuarter);
        $threeYearsAgo = $currentYear - 2;
        if (is_array($company)) {
            $company = Company::with([
                'dividends' => function ($query) use ($threeYearsAgo) {
                    $query->where('year', '>=', $threeYearsAgo);
                },
                'revenues' => function ($query) use ($threeYearsAgo) {
                    $query->where('year', '>=', $threeYearsAgo);
                },
                'financialPositions' => function ($query) use ($threeYearsAgo) {
                    $query->where('year', '>=', $threeYearsAgo);
                },
            ])->findOrFail($company['id']);
        }
    
        $this->company = $company;

        $this->years = collect(range($threeYearsAgo, $currentYear))->values()->all();

        $this->revenueData = $company->revenues
            ->groupBy('quarter')
            ->map(function ($revenues) {
            return $revenues->pluck('revenue', 'year')->only($this->years)->sortKeys();
            });

        $this->grossProfitData = $company->revenues
            ->groupBy('quarter')
            ->map(function ($revenues) {
                return $revenues->pluck('gross_profit', 'year')->only($this->years);
            });

        $this->netProfitData = $company->revenues
            ->groupBy('quarter')
            ->map(function ($revenues) {
                return $revenues->pluck('net_profit', 'year')->only($this->years);
            });

        $this->financialPositionData = $company->financialPositions
            ->groupBy('quarter')
            ->map(function ($positions) {
                return [
                'asset' => $positions->pluck('asset', 'year')->only($this->years),
                'liability' => $positions->pluck('liability', 'year')->only($this->years),
                'equality' => $positions->pluck('equality', 'year')->only($this->years),
            ];
            });

        $this->dividendData = $company->dividends
            ->groupBy('quarter')
            ->map(function ($dividends) {
                return [
                    'dividend_per_sheet' => $dividends->pluck('dividend_per_sheet', 'year')->only($this->years),
                    'yield' => $dividends->pluck('yield', 'year')->only($this->years),
                ];
            });

        $this->profitabilityRatioData = $company->profitabilityRatios
            ->groupBy('quarter')
            ->map(function ($ratios) {
                return [
                'ROE' => $ratios->pluck('ROE', 'year')->only($this->years),
                'GPM' => $ratios->pluck('GPM', 'year')->only($this->years),
                'NPM' => $ratios->pluck('NPM', 'year')->only($this->years),
            ];
            });

        $this->relativeRatioData = $company->relativeRatios
            ->groupBy('quarter')
            ->map(function ($ratios) {
                return [
                'EPS' => $ratios->pluck('EPS', 'year')->only($this->years),
                'PER' => $ratios->pluck('PER', 'year')->only($this->years),
                'BVPS' => $ratios->pluck('BVPS', 'year')->only($this->years),
                'PBV' => $ratios->pluck('PBV', 'year')->only($this->years),
            ];
            });

        $this->liquidityRatioData = $company->liquidityRatios
            ->groupBy('quarter')
            ->map(function ($ratios) {
                return [
                'DAR' => $ratios->pluck('DAR', 'year')->only($this->years),
                'DER' => $ratios->pluck('DER', 'year')->only($this->years),
            ];
        });

        $this->marketShare = $company->marketShares
        ->where('year', $currentYear)
        ->where('quarter', $currentQuarter)
        ->first();

        $this->growthNetProfit = $this->marketShare ? $this->marketShare['growth_net_profit'] : null;
        $this->marketCap = $this->marketShare ? $this->marketShare['market_cap'] : null;
        $this->price = $this->marketShare ? $this->marketShare['price'] : null;
    }

    public function render()
    {
        return view('livewire.dashboard.general-information', [
            'company' => $this->company,
            'revenueData' => $this->revenueData,
            'grossProfitData' => $this->grossProfitData,
            'netProfitData' => $this->netProfitData,
            'financialPositionData' => $this->financialPositionData,
            'dividendData' => $this->dividendData,
            'profitabilityRatioData' => $this->profitabilityRatioData,
            'relativeRatioData' => $this->relativeRatioData,
            'liquidityRatioData' => $this->liquidityRatioData,
            'marketShare' => $this->marketShare,
            'growthNetProfit' => $this->growthNetProfit,
            'marketCap' => $this->marketCap,
            'price' => $this->price,
            'years' => $this->years,
        ]);
    }
}