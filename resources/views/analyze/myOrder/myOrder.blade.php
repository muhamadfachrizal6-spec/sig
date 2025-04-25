@extends('layouts.navigation')
@section('title', 'Payment and Package')
@section('contents')

    <style>
        .floating-label {
            position: absolute;
            top: -18px;
            left: 50%;
            transform: translateX(-50%);
            background-color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 999px;
            font-weight: 600;
            z-index: 1;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 2px solid #fff;
        }

        .card-wrapper {
            position: relative;
            padding-top: 2.5rem;
        }
    </style>

    <div class="mt-5">
        <h2 class="text-center mb-5">Payment Details</h2>

        @if ($order)
            <div class="card shadow-sm p-4 rounded-4 card-wrapper">
                <div class="floating-label text-white bg-success fs-6">
                    Selected Package
                </div>
                <div class="list-group mt-4 border rounded-3 overflow-hidden">
                    <div class="list-group-item d-flex justify-content-between flex-column flex-sm-row py-3">
                        <strong>Package Name</strong>
                        <span>{{ ucfirst($namePack) }}</span>
                    </div>

                    @if (!empty($order->selected_emiten))
                        <div class="list-group-item d-flex justify-content-between flex-column flex-sm-row py-3">
                            <strong>Your Emiten List</strong>
                            <span>{{ $order->selected_emiten }}</span>
                        </div>
                    @endif

                    <div class="list-group-item d-flex justify-content-between flex-column flex-sm-row py-3">
                        <strong>Price</strong>
                        <span>Rp.{{ number_format($order->total_price ?? 0, 2) }}</span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between flex-column flex-sm-row py-3">
                        <strong>Payment Status</strong>
                        <span
                            class="badge 
                        @if ($order->status === 'Success') bg-success 
                        @elseif($order->status === 'Pending') bg-warning 
                        @else bg-danger @endif px-3 py-2 fs-6">
                            {{ strtoupper($order->status) }}
                        </span>
                    </div>

                    @php
                        $paymentType = $trxDetails['paymentType'] ?? null;
                    @endphp

                    @if (in_array($paymentType, ['bank_transfer', 'cstore', 'echannel', 'qris']))
                        <div class="list-group-item d-flex justify-content-between flex-column flex-sm-row py-3">
                            <strong>Transaction Type</strong>
                            <span class="text-white text-center bg-primary px-3 py-1 rounded">
                                <strong>
                                    @if ($paymentType === 'cstore')
                                        {{ strtoupper($trxDetails['trxStore'] ?? 'CSTORE') }}
                                    @elseif($paymentType === 'echannel')
                                        BANK TRANSFER
                                    @else
                                        {{ strtoupper(str_replace('_', ' ', $paymentType)) }}
                                    @endif
                                </strong>
                            </span>
                        </div>

                        <div
                            class="list-group-item text-center d-flex justify-content-between flex-column flex-sm-row py-3">
                            <strong>Virtual account number</strong>
                            <span class="border border-2 border-primary px-3 py-1 rounded text-dark">
                                <strong>
                                    @if ($paymentType === 'bank_transfer')
                                        {{ $trxDetails['trxVANumber'] ?? '' }}
                                    @elseif($paymentType === 'cstore')
                                        {{ $trxDetails['trxVANumberCStore'] ?? '' }}
                                    @elseif($paymentType === 'echannel')
                                        {{ $trxDetails['trxCompanyCode'] ?? '' }} {{ $trxDetails['trxVANumber'] ?? '' }}
                                    @endif
                                </strong>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="text-center mt-4">
                    @if ($order->status === 'Success')
                        <a href="/dashboard-core/" class="btn btn-success btn-lg px-4">Continue to Dashboard</a>
                    @else
                        <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                            <a href="/payment" class="btn btn-secondary btn-md">Re-order</a>
                            <form action="{{ route('myOrderIndex') }}" method="GET">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-md text-white">Check Payment
                                    Status</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="card shadow-sm p-4 rounded-4 text-center">
                <h4 class="mb-3 text-danger">Oops! Order not found.</h4>
                <p class="mb-4">We couldn’t find your payment details. Please make sure you have placed an order.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="/payment" class="btn btn-success">Get it Order</a>
                    <a href="/dashboard-core" class="btn btn-outline-secondary">Back to Dashboard</a>
                </div>
            </div>
        @endif
    </div>

@endsection
