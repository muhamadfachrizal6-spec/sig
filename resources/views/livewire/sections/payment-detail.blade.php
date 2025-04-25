<div class="mt-5">
    <h2 class="text-center mb-4">Payment Details</h2>
    @if ($selectedPack)
        <div class="card shadow-sm p-4">
            <div class="d-flex justify-content-between mb-3">
                <h4 class="card-title">Selected Package</h4>
            </div>
            <div class="list-group">
                <div class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                    <strong>Package Name:</strong>
                    <span>{{ $selectedPack['name_pack'] ?? 'N/A' }}</span>
                </div>
                @if($selectedPack['name_pack'] === 'custom')
                    <div class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <strong>Item List :</strong>
                        <span>{{ $selectedItemPack }}</span>
                    </div>
                @endif
                <div class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                    <strong>Price:</strong>
                    <span>Rp.{{ number_format($priceTotal ?? 0, 2) }}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                    <strong>Payment Status :</strong>
                    @if($paymentStatus === 'Success')
                        <span class="text-white bg-success p-2 rounded"><strong>{{ $paymentStatus ?? 'Failed Payment' }}</strong></span>
                    @elseif($paymentStatus === 'Pending')
                        <span class="text-white {{ $checkPaymentStatus === 'settlement' ? 'bg-success' : 'bg-warning' }} p-2 rounded"><strong>{{ $checkPaymentStatus === 'settlement' ? 'Success' : (strtoupper($paymentStatus) ?? 'Failed Payment') }}</strong></span>
                    @else
                        <span class="text-white {{ $checkPaymentStatus === 'settlement' ? 'bg-success' : 'bg-danger' }} p-2 rounded"><strong>{{ $checkPaymentStatus === 'settlement' ? 'Success' : (strtoupper($paymentStatus) ?? 'Failed Payment') }}</strong></span>
                    @endif
                </div>
                @if($paymentType === 'bank_transfer' || $paymentType === 'cstore')
                    <div class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <strong>Transaction Type :</strong>
                        @if($paymentType === 'bank_transfer')
                            <span class="text-white primary-color p-2 rounded">
                                <strong>{{ strtoupper(str_replace('_', ' ', $paymentType)) ?? '' }}</strong>
                            </span>
                        @elseif($paymentType === 'cstore')
                            <span class="text-white primary-color p-2 rounded">
                                <strong>{{ strtoupper(str_replace('_', ' ', $paymentStore)) ?? '' }}</strong>
                            </span>
                        @elseif($paymentType === 'echannel')
                            <span class="text-white primary-color p-2 rounded">
                                <strong>{{ strtoupper(str_replace('_', ' ', $paymentType)) ?? '' }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <strong>Virtual account number :</strong>
                        @if($paymentType === 'bank_transfer')
                            <span class="text-dark border-primary-color border-2 border p-2 rounded"><strong>{{ $paymentVirtualAccount ?? '' }}</strong></span>
                        @elseif($paymentType === 'cstore')
                            <span class="text-dark outline border-primary-color p-2 rounded"><strong>{{ $paymentCodeStore ?? '' }}</strong></span>
                        @endif
                    </div>
                @endif
            </div>

            @if($paymentStatus === 'Success' || $checkPaymentStatus === 'settlement')
                <div class="text-center mt-4">
                    <a href="/dashboard-core/" class="btn btn-custom2 btn-lg">Continue to Dashboard</a>
                </div>
            @elseif($checkPaymentStatus !== 'settlement')
                <div class="d-flex flex-row flex-wrap justify-content-center align-center gap-2">
                    <div class="text-center mt-2">
                        <a href="/payment" class="btn btn-custom2 btn-md">Cancel Payment</a>
                    </div>
                    <div class="text-center mt-2">
                        <button wire:click="checkPaymentStatus" class="btn btn-warning btn-md text-white">Check Payment Status</button>
                    </div>
                </div>
            @endif
        </div>
    @else
        <p class="text-center text-danger">No package selected.</p>
    @endif
</div>

<script>
    window.addEventListener('swal:success', event => {
        Swal.fire({
            title: event.detail.title,
            text: event.detail.text,
            icon: event.detail.icon,
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/dashboard-core/';
            }
        });
    });

    window.addEventListener('swal:info', event => {
        Swal.fire({
            title: event.detail.title,
            html: `
                <div class="text-left">
                    <p><strong>Transaction Time:</strong> ${event.detail.trxTime}</p>
                    <p><strong>Transaction ID:</strong> ${event.detail.trxId}</p>
                    <p><strong>Total Payment:</strong> Rp ${event.detail.trxPrice}</p>
                    <p><strong>Payment Method:</strong> ${event.detail.paymentType}</p>
                </div>
            `,
            icon: event.detail.icon,
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
        });
    });
</script>

