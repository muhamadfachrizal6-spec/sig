<?php

namespace App\Http\Livewire;

use App\Models\Transactions;
use Livewire\Component;

class PaymentDetail extends Component
{
    public $selectedPack;
    public $paymentStatus;
    public $selectedItemPack;
    public $priceTotal;

    protected $listeners = ['packSelected' => 'handlePackSelection'];

    public function handlePackSelection($packData)
    {
        $this->selectedPack = $packData;

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
            'priceTotal' => $this->priceTotal
        ]);
    }
}