<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Pack;
use App\Models\Transactions;
use App\Models\UserAnalyze;
use Midtrans\Snap;

class NavigationPayment extends Component
{
    public $activeLink = 'pack';
    public $selectedEmiten;
    public $packs;
    public $selectedPack;
    public $snapToken;
    public $submitMessage = '';

    protected $listeners = [
        'emitenSelected',
        'paymentSuccess' => 'handlePaymentSuccess',
        'paymentPending' => 'handlePaymentPending',
        'paymentError' => 'handlePaymentError',
        'paymentClosed' => 'handlePaymentClosed'
    ];

    public function mount()
    {
        $this->packs = Pack::all();
    }

    public function selectPack($productId)
    {
        $this->selectedPack = Pack::find($productId);

        if ($this->selectedPack && strpos(strtolower($this->selectedPack->name_pack), 'custom') !== false) {
            $this->setActiveLink('emiten');
        } else {
            $this->initiatePayment();
        }
    }

    public function initiatePayment()
    {
        try {
            $transaction = Transactions::create([
                'company_id' => auth()->id(),
                'pack_id' => $this->selectedPack->id,
                'total_price' => $this->selectedPack->price,
                'status' => 'pending',
            ]);

            // Siapkan deskripsi items untuk Midtrans
            $itemsDescription = explode('$', $this->selectedPack->description);
            $itemsList = array_filter($itemsDescription); // Menghapus empty values

            $midtransData = [
                'transaction_details' => [
                    'order_id' => 'ORDER-' . $transaction->id,
                    'gross_amount' => $this->selectedPack->price,
                ],
                'item_details' => [
                    [
                        'id' => $this->selectedPack->id,
                        'price' => $this->selectedPack->price,
                        'quantity' => 1,
                        'name' => $this->selectedPack->name_pack,
                        'brand' => 'Bundle Package',
                        'category' => 'Subscription Package',
                        'merchant_name' => config('app.name'),
                    ]
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'phone' => auth()->user()->phone_number ?? '',
                ],
                'custom_field1' => json_encode($itemsList), // Menyimpan detail fitur bundle
            ];

            $this->snapToken = Snap::getSnapToken($midtransData);
            $this->dispatchBrowserEvent('show-payment', ['snapToken' => $this->snapToken]);
            $this->setActiveLink('paymentDetail');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat membuat transaksi: ' . $e->getMessage());
        }
    }

    public function handlePaymentSuccess($result)
    {
        $transactionId = $this->getOrderIdFromResult($result);

        Transactions::where('id', $transactionId)
            ->update(['status' => 'success']);

        $transaction = Transactions::find($transactionId);
        $pack = $transaction->pack;

        $userType = (strpos(strtolower($pack->name_pack), 'bundle') !== false) ? 'bundle' : 'free';

        // Perbarui user_type pada UserAnalyze
        UserAnalyze::updateOrCreate(
            ['id' => auth()->id()],
            ['user_type' => $userType]
        );

        $this->submitMessage = 'Pembayaran berhasil!';
        $this->resetPayment();
    }

    public function handlePaymentPending($result)
    {
        Transactions::where('id', $this->getOrderIdFromResult($result))
            ->update(['status' => 'pending']);
        $this->submitMessage = 'Pembayaran dalam proses';
    }

    public function handlePaymentError($result)
    {
        Transactions::where('id', $this->getOrderIdFromResult($result))
            ->update(['status' => 'failed']);
        $this->submitMessage = 'Pembayaran gagal';
    }

    public function handlePaymentClosed()
    {
        $this->submitMessage = 'Pembayaran dibatalkan';
    }

    private function getOrderIdFromResult($result)
    {
        return substr($result['order_id'], 6);
    }

    private function resetPayment()
    {
        $this->selectedPack = null;
        $this->snapToken = null;
        $this->setActiveLink('pack');
    }

    public function setActiveLink($link)
    {
        if ($link === 'emiten' && !$this->selectedPack) {
            // Tampilkan pesan atau beri tahu pengguna untuk memilih pack terlebih dahulu
            session()->flash('error', 'Pilih pack terlebih dahulu sebelum melanjutkan.');
            return;
        }

        if ($link === 'paymentDetail' && !$this->selectedEmiten) {
            // Tampilkan pesan atau beri tahu pengguna untuk memilih emiten terlebih dahulu
            session()->flash('error', 'Pilih emiten terlebih dahulu sebelum melanjutkan.');
            return;
        }

        $this->activeLink = $link;
    }

    public function emitenSelected($emiten)
    {
        $this->selectedEmiten = $emiten;
        $this->setActiveLink('paymentDetail');
    }

    public function render()
    {
        return view('livewire.navigation-payment');
    }
}