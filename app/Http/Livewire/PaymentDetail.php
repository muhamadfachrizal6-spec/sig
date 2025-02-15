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

    protected $listeners = ['packSelected' => 'handlePackSelection'];

    public function handlePackSelection($packData)
    {
        $this->selectedPack = $packData;

    }

    public function checkPaymentStatus()
    {
        $this->orderId = Transactions::where('user_id', auth()->user()->id)->where('pack_id', $this->selectedPack->id)->first()->order_id;
        $status = \Midtrans\Transaction::status($this->orderId);

        $this->checkPaymentStatus = $status->transaction_status;
        if ($this->checkPaymentStatus == 'settlement' && $this->selectedPack->name_pack == 'custom') {
            UserAnalyze::updateOrCreate(
                ['id' => auth()->user()->id],
                ['user_type' => 'custom']
            );

            Transactions::where('user_id', auth()->user()->id)->where('pack_id', $this->selectedPack->id)->update(['status' => 'Success']);
        } else {
            UserAnalyze::updateOrCreate(
                ['id' => auth()->user()->id],
                ['user_type' => 'bundle']
            );

            Transactions::where('user_id', auth()->user()->id)->where('pack_id', $this->selectedPack->id)->update(['status' => 'Success']);
        }
    }

    public function render()
    {

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
        ]);
    }
}