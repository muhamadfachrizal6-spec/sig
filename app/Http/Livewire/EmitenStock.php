<?php
namespace App\Http\Livewire;

use App\Models\Company;
use App\Models\Pack;
use App\Models\Transactions;
use Livewire\Component;
use Midtrans\Snap;

class EmitenStock extends Component
{
    public $search = '';
    public $emitenList = [];
    public $selectedEmiten = [];
    public $submitMessage = '';
    public $totalPrice = 0;
    public $snapToken;
    public $selectedPack;

    protected $listeners = ['packSelected' => 'handlePackSelection'];

    public function mount()
    {
        $this->emitenList = Company::all(['id', 'ticker', 'name'])->sortBy('ticker');
    }

    public function handlePackSelection($selectedPack)
    {
        $this->selectedPack = $selectedPack;
    }

    public function selectEmiten($ticker)
    {
        $emiten = Company::with(['transactions' => function ($query) {
            $query->whereHas('pack', function ($q) {
                $q->where('name_pack', 'custom');
            })->with('pack');
        }])->where('ticker', $ticker)->first();

        if (!in_array($ticker, array_column($this->selectedEmiten, 'ticker'))) {
            $latestCustomPrice = Pack::where('name_pack', 'custom')->first()?->price ?? 0;

            $this->selectedEmiten[] = [
                'id' => $emiten->id,
                'ticker' => $emiten->ticker,
                'name' => $emiten->name,
                'price' => $latestCustomPrice
            ];
        } else {
            $this->selectedEmiten = array_filter($this->selectedEmiten, function ($item) use ($ticker) {
                return $item['ticker'] !== $ticker;
            });
        }

        $this->selectedEmiten = array_values($this->selectedEmiten);
        $this->emit('selectedEmitenList', $this->selectedEmiten);
        $this->calculateTotalPrice();
    }

    public function removeEmiten($ticker)
    {
        $this->selectedEmiten = array_filter($this->selectedEmiten, function ($item) use ($ticker) {
            return $item['ticker'] !== $ticker;
        });

        $this->selectedEmiten = array_values($this->selectedEmiten);
        $this->calculateTotalPrice();
    }

    public function submit()
    {
        $this->validate([
            'selectedEmiten' => 'required|array|min:1',
        ]);

        $existTransaction = Transactions::where('user_id', auth()->user()->id)->where('pack_id', $this->selectedPack['id'])->first();
        $transactionPending = Transactions::where('user_id', auth()->user()->id)->where('status', 'pending')->first();
        if ($existTransaction && $transactionPending) {
            $existTransaction->delete();
        }

        try {
            $orderId = uniqid();
            $this->emit('orderIdCustomPack', $orderId);

            $items = [];
            foreach ($this->selectedEmiten as $emiten) {
                $items[] = [
                    'id' => $emiten['id'],
                    'price' => $emiten['price'],
                    'quantity' => 1,
                    'name' => $emiten['ticker'] . ' - ' . $emiten['name']
                ];
            }

            $midtransData = [
                'transaction_details' => [
                    'order_id' => 'ORDER-' . $orderId,
                    'gross_amount' => $this->totalPrice,
                ],
                'item_details' => $items,
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'phone' => auth()->user()->phone ?? '',
                ],
            ];

            $this->snapToken = Snap::getSnapToken($midtransData);

            $this->dispatchBrowserEvent('show-payment', ['snapToken' => $this->snapToken]);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat membuat transaksi: ' . $e->getMessage());
        }
    }


    public function calculateTotalPrice()
    {
        $this->totalPrice = collect($this->selectedEmiten)->sum('price');
    }

    public function render()
    {
        $filteredEmitens = collect($this->emitenList)->filter(function ($emiten) {
            return stripos($emiten['name'], $this->search) !== false ||
                stripos($emiten['ticker'], $this->search) !== false;
        })->values();

        return view('livewire.sections.emiten', [
            'filteredEmitens' => $filteredEmitens,
            'totalPrice' => $this->totalPrice,
        ]);
    }
}