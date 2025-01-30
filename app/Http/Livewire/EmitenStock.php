<?php
namespace App\Http\Livewire;

use App\Models\Company;
use App\Models\Pack;
use App\Models\Transactions;
use App\Models\UserAnalyze;
use Illuminate\Support\Facades\Log;
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
    protected $listeners = [
        'paymentSuccess' => 'handlePaymentSuccess',
        'paymentPending' => 'handlePaymentPending',
        'paymentError' => 'handlePaymentError',
        'paymentClosed' => 'handlePaymentClosed'
    ];

    public function handlePaymentSuccess($result)
    {
        // Update status transaksi menjadi success
        Transactions::where('id', $this->getOrderIdFromResult($result))
            ->update(['status' => 'success']);

        $userAnalyze = UserAnalyze::updateOrCreate(
            ['id' => auth()->id()],
            ['user_type' => 'custom']
        );

        Log::info('Updated or created UserAnalyze: ' . $userAnalyze->id);

        $this->submitMessage = 'Pembayaran berhasil!';
        $this->selectedEmiten = [];
        $this->calculateTotalPrice();
    }

    public function handlePaymentPending($result)
    {
        // Update status transaksi menjadi pending
        Transactions::where('id', $this->getOrderIdFromResult($result))
            ->update(['status' => 'pending']);

        $this->submitMessage = 'Pembayaran dalam proses';
    }


    public function handlePaymentError($result)
    {
        // Update status transaksi menjadi failed
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
        // Extract ID from order_id (ORDER-123 -> 123)
        return substr($result['order_id'], 6);
    }

    public function mount()
    {
        $this->emitenList = Company::all(['id', 'ticker', 'name'])->sortBy('ticker');
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

        try {
            $transaction = Transactions::create([
                'company_id' => auth()->id(),
                'pack_id' => 1,
                'total_price' => $this->totalPrice,
                'status' => 'pending',
            ]);

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
                    'order_id' => 'ORDER-' . $transaction->id,
                    'gross_amount' => $this->totalPrice,
                ],
                'item_details' => $items,
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'phone' => auth()->user()->phone_number ?? '',
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