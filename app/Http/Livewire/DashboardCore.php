<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Company;
use App\Models\Transactions;
use App\Models\UserAnalyze;
use App\Models\Pack;
use Midtrans\Transaction as MidtransTransaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardCore extends Component
{
    public $activeTab = 'general-information';
    public $selectedCompany = 'ASII';
    public $paymentStatusMessage;

    public function mount()
    {
        $this->loadCompanyData();
        $this->checkPaymentStatus();
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

    public function checkPaymentStatus()
    {
        $order = Transactions::where('user_id', Auth::id())->latest('created_at')->first();

        if (is_null($order) || is_null($order->order_id)) {
            $this->paymentStatusMessage = 'Order tidak ditemukan atau order_id tidak tersedia.';
            $this->emit('error', $this->paymentStatusMessage);
            return;
        }

        $orderId = $order->order_id;

        try {
            $status = MidtransTransaction::status($orderId);

            $trxStatus = $status->transaction_status;
            $trxTime = Carbon::parse($status->transaction_time)->format('d M Y H:i:s');
            $trxVANumber = $status->va_numbers[0]->va_number ?? 'Your payment method does not support virtual account';

            $trxMessage = "Status Pembayaran: $trxStatus<br>Order Id : $orderId<br>Waktu Transaksi: $trxTime<br>ID Transaksi: {$status->transaction_id}<br>Nomor Virtual Account: $trxVANumber<br>Total Pembayaran: {$status->gross_amount}<br>Metode Pembayaran: {$status->payment_type}";

            $this->updateTransactionStatus($order, $trxStatus);
            $this->paymentStatusMessage = $trxMessage;
            $this->emit('paymentStatusUpdated', $trxMessage);
        } catch (\Exception $e) {
            $this->paymentStatusMessage = 'Terjadi kesalahan saat mengecek status transaksi: ' . $e->getMessage();
            $this->emit('error', $this->paymentStatusMessage);
        }
    }

    private function updateTransactionStatus($order, $trxStatus)
    {
        $packIdTransaction = $order->pack_id;
        $packName = Pack::where('id', $packIdTransaction)->first()->name_pack;

        if (($packName === 'bundle' || $packName === 'custom') && $trxStatus === 'settlement') {
            UserAnalyze::updateOrCreate(
                ['id' => Auth::id()],
                ['user_type' => $packName]
            );
            Transactions::where('user_id', Auth::id())->update(['status' => 'Success']);
        } elseif (($packName === 'bundle' || $packName === 'custom') && $trxStatus === 'expire') {
            Transactions::where('user_id', Auth::id())->update(['status' => 'Expired']);
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