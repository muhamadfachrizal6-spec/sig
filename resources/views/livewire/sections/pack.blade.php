<div class="container d-flex justify-content-center flex-wrap gap-4 w-100 m-auto">
    @foreach ($packs as $pack)
        <div class="card border-0 flex-fill bg-pack-card" style="width: 18rem;">
            <div class="card-header border-0 secondary-color bg-transparent text-center p-5">
                <h4><b>{{ ucfirst($pack->name_pack) }}</b></h4>
                <h1><b>Rp{{ number_format($pack->price, 0, ',', '.') }}</b></h1>
            </div>
            <div class="card-body pb-5 text-center">
                @if ($pack->description)
                    @foreach (explode('$', $pack->description) as $item)
                        <p><i class="fas fa-check-circle"></i> {{ $item }}</p>
                    @endforeach
                @else
                    <p>No description available</p>
                @endif
            </div>
            <div class="card-footer border-0 bg-transparent p-4 w-100 d-flex justify-content-center">
                @if($pack->name_pack === 'bundle' && !($paymentStatus === 'Success' && (empty($itemSuccessType))))
                    <button class="border-1 w-50 p-2 rounded" wire:click="selectPack({{ $pack->id }})"
                        wire:loading.attr="disabled" wire:target="selectPack">
                        <span wire:loading.remove wire:target="selectPack({{ $pack->id }})">
                            Purchase
                        </span>
                        <span wire:loading wire:target="selectPack({{ $pack->id }})">
                            Processing...
                        </span>
                    </button>
                @elseif($pack->name_pack === 'custom' && !($paymentStatus === 'Success' && (empty($itemSuccessType))))
                    <button class="border-1 w-50 p-2 rounded" wire:click="selectPack({{ $pack->id }})"
                        wire:loading.attr="disabled" wire:target="selectPack">
                        <span wire:loading.remove wire:target="selectPack({{ $pack->id }})">
                            Purchase
                        </span>
                        <span wire:loading wire:target="selectPack({{ $pack->id }})">
                            Processing...
                        </span>
                    </button>
                @elseif ($userType)
                    <button class="border-1 w-50 p-2 rounded" wire:click="selectPack({{ $pack->id }})"disabled>
                        Purchase
                    </button>
                @endif
            </div>
        </div>
    @endforeach

    @if (session()->has('error'))
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto">Tidak bisa melanjutkan</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('error') }}
                </div>
            </div>
        </div>
    @endif

    @if ($submitMessage)
        <div class="alert alert-success mt-4">
            {{ $submitMessage }}
        </div>
    @endif
</div>

@if ($activeLink === 'emiten')
    @livewire('emiten')
@endif

@if ($activeLink === 'paymentDetail')
    @livewire('payment-detail')
@endif

<script>
    document.addEventListener('livewire:load', function() {
        // Cek apakah Snap.js sudah dimuat sebelumnya
        if (typeof snap === "undefined") {
            let snapScript = document.createElement("script");
            snapScript.src = "https://app.sandbox.midtrans.com/snap/snap.js";
            snapScript.setAttribute("data-client-key", "{{ config('midtrans.client_key') }}");

            snapScript.onload = () => {
                console.log("Snap.js berhasil dimuat.");
                window.snapLoaded = true; // Tandai bahwa Snap.js sudah siap
                window.dispatchEvent(new CustomEvent('snap-loaded'));
            };

            document.body.appendChild(snapScript);
        } else {
            console.log("Snap.js sudah tersedia.");
            window.snapLoaded = true;
        }

        window.addEventListener("show-payment", (event) => {
            if (!window.snapLoaded) {
                console.log("Menunggu Snap.js selesai dimuat...");
                window.addEventListener('snap-loaded', () => {
                    console.log("Snap.js siap, memulai pembayaran.");
                    startSnapPayment(event.detail.snapToken);
                });
                return;
            }

            startSnapPayment(event.detail.snapToken);
        });

        let snapPaymentInProgress = false;

        function startSnapPayment(snapToken) {
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    window.livewire.emit('paymentSuccess', result.order_id);

                    Swal.fire({
                        title: 'Payment Success',
                        text: 'Your payment is Success!',
                        icon: 'info',
                        confirmButtonText: 'Okay',
                    });
                },
                onPending: function(result) {
                    window.livewire.emit('paymentPending', result.order_id);
                    window.livewire.emit('updateActiveLink', 'paymentDetail');

                    Swal.fire({
                        title: 'Payment Pending',
                        text: 'Your payment is still being processed. Please wait.',
                        icon: 'info',
                        confirmButtonText: 'Okay',
                    });
                },
                onError: function(result) {
                    console.log("Error", result);
                    window.dispatchEvent(new CustomEvent('payment-error', {
                        detail: result
                    }));
                },
                onClose: function() {
                    console.log("Closed");
                    window.dispatchEvent(new CustomEvent('payment-closed'));
                },
            });
        }
    });
</script>
