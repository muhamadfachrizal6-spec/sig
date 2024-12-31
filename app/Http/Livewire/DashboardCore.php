<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Company;

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
        if (auth()->check() && auth()->user()->user_type == 'free') {
            $companies = Company::whereIn('ticker', ['ASII', 'TLKM', 'ACES'])->get();
        } else {
            $companies = Company::all();
        }

        return view('livewire.dashboard-core', [
            'companies' => $companies,
            'companyData' => Company::where('ticker', $this->selectedCompany)->firstOrFail()
        ]);
    }
}