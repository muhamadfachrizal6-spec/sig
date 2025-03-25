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
    public $packs;
    public $selectedPack;
    public $selectedEmiten;
    public $snapToken;
    public $submitMessage = '';
    public $userType;
    public $paymentStatus;
    public $itemSuccessType;
    public $orderId;
    public $orderIdCustomPack;

    protected $listeners = [
        'emitenSelected',
        'selectedEmitenList' => 'handleSelectedEmiten',
        'updateActiveLink' => 'setActiveLink',
        'paymentPending' => 'handlePaymentPending',
        'paymentSuccess' => 'handlePaymentSuccess',
        'orderIdCustomPack' => 'handleOrderIdCustomPack',
    ];

    public function handleOrderIdCustomPack($orderId)
    {
        $this->orderIdCustomPack = $orderId;
    }

    public function mount()
    {
        $this->packs = Pack::all();
        $this->userType = auth()->user()->user_type;
        $this->paymentStatus = Transactions::where('user_id', auth()->user()->id)->first()->status ?? null;
        $this->itemSuccessType = Transactions::where('user_id', auth()->user()->id)->first()->selected_emiten ?? null;
    }

    public function handleSelectedEmiten($selectedEmiten)
    {
        $this->selectedEmiten = $selectedEmiten;
    }

    public function handlePaymentPending()
    {
        $companyList = collect($this->selectedEmiten)->pluck('ticker')->implode(', ');
        $companyCount = collect($this->selectedEmiten)->count();

        if ($this->selectedPack->name_pack === 'custom') {
            $totalPrice = $this->selectedPack->price * $companyCount;
        } else {
            $totalPrice = $this->selectedPack->price;
        }

        Transactions::create([
            'order_id' => 'ORDER-' . ($this->orderIdCustomPack ?  $this->orderIdCustomPack : $this->orderId),
            'company_id' => auth()->id(),
            'user_id' => auth()->user()->id,
            'selected_emiten' => $companyList,
            'pack_id' => $this->selectedPack->id,
            'total_price' => $totalPrice,
            'status' => 'Pending',
        ]);

        return redirect()->to('/myOrder');
    }

    public function handlePaymentSuccess()
    {
        $packIdName = Transactions::where('user_id', auth()->user()->id)
            ->where('pack_id', $this->selectedPack->id)
            ->first();
        if ($packIdName) {
            $packName = Pack::where('id', $packIdName->pack_id)->first()->name_pack;
        }
        $companyList = collect($this->selectedEmiten)->pluck('ticker')->implode(', ');
        $companyListclear = collect($this->selectedEmiten)->pluck('ticker')->implode(', ');
        $companyListMerge = $companyList = collect($this->selectedEmiten)->pluck('ticker');
        $existTransaction = Transactions::where('user_id', auth()->user()->id)->where('pack_id', $this->selectedPack->id)->first();
        $transactionSuccess = Transactions::where('user_id', auth()->user()->id)->where('status', 'Success')->first();

        if ($existTransaction && $transactionSuccess && $packName === 'custom') {

            $existingSelectedEmiten = $existTransaction->selected_emiten;
            $existingSelectedEmitenArray = $existingSelectedEmiten ? explode(', ', $existingSelectedEmiten) : [];

            $newSelectedEmitenArray = array_unique(array_merge($existingSelectedEmitenArray, $companyListMerge->toArray()));
            $newSelectedEmiten = implode(', ', $newSelectedEmitenArray);

            $existTransaction->update(['order_id' => 'ORDER-' . $this->orderIdCustomPack ?? $this->orderId,
                'company_id' => auth()->id(),
                'user_id' => auth()->user()->id,
                'selected_emiten' => $newSelectedEmiten,
                'pack_id' => $this->selectedPack->id,
                'total_price' => $this->selectedPack->price,
                'status' => 'Success',
            ]);
            UserAnalyze::updateOrCreate(
                ['id' => auth()->user()->id],
                ['user_type' => 'custom']
            );
        } else if ($this->selectedPack->name_pack === 'custom' && $transactionSuccess === null) {
            Transactions::create([
                'order_id' => 'ORDER-' . ($this->orderIdCustomPack ?  $this->orderIdCustomPack : $this->orderId),
                'company_id' => auth()->id(),
                'user_id' => auth()->user()->id,
                'selected_emiten' => $companyListclear,
                'pack_id' => $this->selectedPack->id,
                'total_price' => $this->selectedPack->price,
                'status' => 'Success',
            ]);
            UserAnalyze::updateOrCreate(
                ['id' => auth()->user()->id],
                ['user_type' => 'custom']
            );
        } else {
            Transactions::create(['order_id' => 'ORDER-' . ($this->orderIdCustomPack ?  $this->orderIdCustomPack : $this->orderId),
                'company_id' => auth()->id(),
                'user_id' => auth()->user()->id,
                'selected_emiten' => 'Bundle',
                'pack_id' => $this->selectedPack->id,
                'total_price' => $this->selectedPack->price,
                'status' => 'Success',
            ]);
            UserAnalyze::updateOrCreate(
                ['id' => auth()->user()->id],
                ['user_type' => 'bundle']
            );
        }
        return redirect()->to('/myOrder');
        $this->setActiveLink('paymentDetail');
    }

    public function selectPack($productId)
    {
        $this->selectedPack = Pack::find($productId);

        if ($this->selectedPack && strpos(strtolower($this->selectedPack->name_pack), 'custom') !== false) {
            $this->setActiveLink('emiten');
        } else {
            $this->initiatePayment();
        }

        $this->emit('packSelected', $this->selectedPack);
    }

    public function initiatePayment()
    {
        $existTransaction = Transactions::where('user_id', auth()->user()->id)->where('pack_id', $this->selectedPack->id)->first();
        $transactionPending = Transactions::where('user_id', auth()->user()->id)->where('status', 'Pending')->first();
        $transactionExpired = Transactions::where('user_id', auth()->user()->id)->where('status', 'Expired')->first();
        if ($existTransaction && $transactionPending || $existTransaction && $transactionExpired) {
            $existTransaction->delete();
        }

        try {
            $itemsDescription = explode('$', $this->selectedPack->description);
            $itemsList = array_filter($itemsDescription);
            $this->orderId = uniqid();
            
            $midtransData = [
                'transaction_details' => [
                    'order_id' => 'ORDER-' . ($this->orderIdCustomPack ?  $this->orderIdCustomPack : $this->orderId),
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
                'custom_field1' => json_encode($itemsList),
            ];

            $this->snapToken = Snap::getSnapToken($midtransData);
            $this->dispatchBrowserEvent('show-payment', ['snapToken' => $this->snapToken]);
            // $this->activeLink = 'paymentDetail';
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat membuat transaksi: ' . $e->getMessage());
        }
    }

    public function setActiveLink($link)
    {
        if ($link === 'emiten' && !$this->selectedPack) {
            session()->flash('error', 'Pilih pack terlebih dahulu sebelum melanjutkan.');
            return;
        }

        $this->activeLink = $link;
    }

    public function emitenSelected($emiten)
    {
        $this->selectedEmiten[] = $emiten;
    }

    public function render()
    {
        return view('livewire.navigation-payment');
    }
}