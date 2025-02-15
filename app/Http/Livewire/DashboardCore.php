<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Company;
use App\Models\Transactions;
use App\Models\UserAnalyze;

class DashboardCore extends Component
{
    public $activeTab = 'general-information';
    public $selectedCompany = 'ASII';

    public function mount()
    {
        $this->loadCompanyData();
    }

    public function updatedSelectedCompany()
    {
        $this->loadCompanyData();
    }

    public function loadCompanyData()
    {
        // Ambil data perusahaan berdasarkan ticker yang dipilih
        $company = Company::where('ticker', $this->selectedCompany)->firstOrFail();

        // Emit event untuk komponen lain
        $this->emit('companyChanged', $company);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        if ($tab === 'key-statics' || $tab === 'key-ratio') {
            $this->loadCompanyData();
        }
    }

    public function render()
    {
        $userType = auth()->user()->user_type;
        if (auth()->check() && $userType === 'custom' && Transactions::where('user_id', auth()->user()->id)->where('status', 'Success')->exists()) {
            $selectedEmiten = Transactions::where('user_id', auth()->user()->id)
                ->where('status', 'Success')
                ->pluck('selected_emiten')
                ->flatten();

            $selectedEmiten = $selectedEmiten->map(function ($emiten) {
                return explode(", ", $emiten);
            })->flatten()->unique();

            $additionalEmitens = ['ASII', 'TLKM', 'ACES'];
            $mergedEmitens = $selectedEmiten->merge($additionalEmitens)->unique();

            $companies = Company::whereIn('ticker', $mergedEmitens)->get();
        } else if (auth()->check() && $userType === 'bundle') {
            $companies = Company::all();
        } else {
            $companies = Company::whereIn('ticker', ['ASII', 'TLKM', 'ACES'])->get();
        }

        return view('livewire.dashboard-core', [
            'companies' => $companies,
            'companyData' => Company::where('ticker', $this->selectedCompany)->firstOrFail()
        ]);
    }
}