<div class="container mt-5 border rounded w-80">
    <div class="text-center mb-4">
        <h2>Discover Company Stock</h2>
        <p>Choose your emiten for growth business</p>
    </div>

    <!-- Pencarian -->
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Search" wire:model="search">
    </div>

    <div class="list-group scrollable-list">
        @foreach ($filteredEmitens as $emiten)
            <div class="d-flex justify-content-between align-items-center list-group-item">
                <div>
                    <h5>{{ $emiten->ticker }}</h5>
                    <p>{{ $emiten->name }}</p>
                </div>
                <button class="btn btn-outline-success" wire:click="selectEmiten('{{ $emiten->ticker }}')"
                    wire:loading.attr="disabled" wire:target="selectEmiten">
                    <span wire:loading.remove wire:target="selectEmiten('{{ $emiten->ticker }}')">
                        {{ in_array($emiten->ticker, array_column($selectedEmiten, 'ticker')) ? 'Dipilih' : 'Pilih' }}
                    </span>
                    <span wire:loading wire:target="selectEmiten('{{ $emiten->ticker }}')">
                        Loading...
                    </span>
                </button>
            </div>
        @endforeach
    </div>

    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0">Emiten yang Dipilih</h4>
            </div>
            <div class="card-body p-0">
                @if (count($selectedEmiten) > 0)
                    <div class="list-group list-group-flush scrollable-list">
                        @foreach ($selectedEmiten as $emiten)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary me-2">{{ $emiten['ticker'] }}</span>
                                            <h6 class="mb-0">{{ $emiten['name'] }}</h6>
                                        </div>
                                        <div class="text-muted mt-1">
                                            <small>Harga: Rp{{ number_format($emiten['price'], 0, ',', '.') }}</small>
                                        </div>
                                    </div>
                                    <button class="btn btn-outline-danger btn-sm rounded-circle"
                                        wire:click="removeEmiten('{{ $emiten['ticker'] }}')"
                                        wire:loading.attr="disabled" title="Hapus">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">Belum ada emiten yang dipilih</p>
                    </div>
                @endif
            </div>
            @if (count($selectedEmiten) > 0)
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total Harga:</span>
                        <h4 class="text-primary fw-bold">
                            Rp{{ number_format($this->totalPrice, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="text-center m-4">
        <button class="border-1 w-25 p-2 rounded" wire:click="submit">
            <span wire:loading.remove wire:target="submit">Bayar Sekarang</span>
            <span wire:loading wire:target="submit">Processing...</span>
        </button>
    </div>

    @if (session()->has('error'))
        <div class="alert alert-danger mt-4">
            {{ session('error') }}
        </div>
    @endif

    @if ($submitMessage)
        <div class="alert alert-success mt-4">
            {{ $submitMessage }}
        </div>
    @endif
</div>


<script>
    window.addEventListener('clear-message', event => {
        setTimeout(() => {
            @this.submitMessage = '';
        }, event.detail.delay);
    });
</script>
<script>
    document.addEventListener("livewire:load", () => {
        // Cek apakah Snap.js sudah dimuat sebelumnya
        if (typeof snap === "undefined") {
            let snapScript = document.createElement("script");
            snapScript.src = "https://app.sandbox.midtrans.com/snap/snap.js";
            snapScript.setAttribute("data-client-key", "{{ config('midtrans.client_key') }}");

            snapScript.onload = () => {
                console.log("Snap.js berhasil dimuat.");
                window.snapLoaded = true; // Tandai bahwa Snap.js sudah siap
                // Memicu event untuk menandai Snap.js sudah siap
                window.dispatchEvent(new CustomEvent('snap-loaded'));
            };

            document.body.appendChild(snapScript);
        } else {
            console.log("Snap.js sudah tersedia.");
            window.snapLoaded = true;
        }
    });
</script>

<script>
    window.addEventListener("show-payment", (event) => {
        // Tunggu hingga Snap.js dimuat
        if (!window.snapLoaded) {
            console.log("Menunggu Snap.js selesai dimuat...");
            // Tunggu event `snap-loaded` sebelum memulai pembayaran
            window.addEventListener('snap-loaded', () => {
                console.log("Snap.js siap, memulai pembayaran.");
                startSnapPayment(event.detail.snapToken);
            });
            return;
        }

        // Jika Snap.js sudah siap, langsung mulai pembayaran
        startSnapPayment(event.detail.snapToken);
    });

    function startSnapPayment(snapToken) {
        snap.pay(snapToken, {
            onSuccess: function (result) {
                Livewire.emit("paymentSuccess", result);
            },
            onPending: function (result) {
                Livewire.emit("paymentPending", result);
            },
            onError: function (result) {
                Livewire.emit("paymentError", result);
            },
            onClose: function () {
                Livewire.emit("paymentClosed");
            },
        });
    }
</script>