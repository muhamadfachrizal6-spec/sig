<?php

namespace App\Http\Livewire;

use App\Models\Transactions;
use App\Models\UserAnalyze;
use Livewire\Component;

class PaymentDetail extends Component
{
    public $selectedPack;
    public $paymentStatus;
    public $selectedItemPack;
    public $priceTotal;
    public $orderId;
    public $checkPaymentStatus;
    public $paymentType;
    public $paymentStore;
    public $paymentCodeStore;
    public $paymentVirtualAccount;
    public $paymentBank;

    protected $listeners = ['packSelected' => 'handlePackSelection'];

    public function handlePackSelection($packData)
    {
        $this->selectedPack = $packData;

    }

    public function checkPaymentStatus()
    {
        $this->orderId = Transactions::where('user_id', auth()->user()->id)->where('pack_id', $this->selectedPack->id)->first()->order_id;
        $status = \Midtrans\Transaction::status($this->orderId);

        $trxStatus = $this->checkPaymentStatus = $status->transaction_status;
        $trxId = $status->transaction_id;
        $trxPrice = $status->gross_amount;
        $trxPaymentType = $status->payment_type;
        $trxTime = $status->transaction_time;
        $trxTimeFormatted = \Carbon\Carbon::parse($trxTime)->format('d M Y H:i:s');
        $trxMessage = '';

        if ($trxStatus == 'settlement' && $this->selectedPack->name_pack == 'custom') {
            UserAnalyze::updateOrCreate(
                ['id' => auth()->user()->id],
                ['user_type' => 'custom']
            );

            Transactions::where('user_id', auth()->user()->id)->where('pack_id', $this->selectedPack->id)->update(['status' => 'Success']);
            $trxMessage = "Selamat, pembayaran Anda sudah sukses!<br>Waktu Transaksi: $trxTimeFormatted<br>ID Transaksi: $trxId<br>Total Pembayaran: $trxPrice<br>Metode Pembayaran: $trxPaymentType";
        } else if ($trxStatus === 'settlement' && $this->selectedPack->name_pack == 'bundle') {
            UserAnalyze::updateOrCreate(
                ['id' => auth()->user()->id],
                ['user_type' => 'bundle']
            );

            Transactions::where('user_id', auth()->user()->id)->where('pack_id', $this->selectedPack->id)->update(['status' => 'Success']);
            $trxMessage = "Selamat, pembayaran Anda sudah sukses!<br>Waktu Transaksi: $trxTimeFormatted<br>ID Transaksi: $trxId<br>Total Pembayaran: $trxPrice<br>Metode Pembayaran: $trxPaymentType";

            $this->dispatchBrowserEvent('swal:success', [
                'title' => 'Payment Success!',
                'text' => "Transaction Time: $trxTimeFormatted\nTransaction ID: $trxId\nTotal Payment: Rp $trxPrice\nPayment Method: " . ucwords(str_replace('_', ' ', $trxPaymentType)),
                'icon' => 'success',
            ]);
        } else {
            $this->dispatchBrowserEvent('swal:info', [
                'title' => 'Payment Pending!',
                'text' => "Transaction Time: $trxTimeFormatted\nTransaction ID: $trxId\nTotal Payment: Rp $trxPrice\nPayment Method: " . ucwords(str_replace('_', ' ', $trxPaymentType)),
                'icon' => 'info',
            ]);
        }

        $this->emit('paymentStatusChecked', $trxMessage);
    }

    public function render()
    {
        $this->orderId = Transactions::where('user_id', auth()->user()->id)->where('pack_id', $this->selectedPack->id)->first()->order_id;
        $status = \Midtrans\Transaction::status($this->orderId);
        $trxPaymentType = $this->paymentType = $status->payment_type;
        // dd($status);

        if ($trxPaymentType == 'cstore') {
            $this->paymentStore = $status->store;
            $this->paymentCodeStore = $status->payment_code;
        } else if ($trxPaymentType == 'bank_transfer') {
            $this->paymentVirtualAccount = $status->va_numbers[0]->va_number !== null ? $status->va_numbers[0]->va_number : $status->permata_va_number;
            $this->paymentBank = $status->va_numbers[0]->bank;
        } else if ($trxPaymentType == 'echannel') {
            $this->paymentVirtualAccount = $status->biller_key;
            $this->paymentBank = 'Bank Mandiri';
        }

        if (!$this->paymentStatus) {
            $this->paymentStatus = Transactions::where('user_id', auth()->user()->id)
                ->where('pack_id', $this->selectedPack->id)
                ->first()
                ->status ?? null;
        }

        $this->selectedItemPack = Transactions::where('user_id', auth()->user()->id)
            ->where('pack_id', $this->selectedPack->id)
            ->first()
            ->selected_emiten ?? null;

        $selectedItemPackArray = explode(', ', $this->selectedItemPack);
        $this->priceTotal = count($selectedItemPackArray) * $this->selectedPack->price;

        return view('livewire.sections.payment-detail', [
            'selectedPack' => $this->selectedPack,
            'paymentStatus' => $this->paymentStatus,
            'selectedItemPack' => $this->selectedItemPack,
            'priceTotal' => $this->priceTotal,
            'checkPaymentStatus' => $this->checkPaymentStatus,
            'paymentType' => $this->paymentType,
            'paymentStore' => $this->paymentStore,
            'paymentCodeStore' => $this->paymentCodeStore,
            'paymentVirtualAccount' => $this->paymentVirtualAccount,
            'paymentBank' => $this->paymentBank,
        ]);
    }
}