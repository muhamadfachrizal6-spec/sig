@extends('layouts.navigation')
@section('title', 'Payment and Package')
@section('contents')

    <div class="mt-5">
        <h2 class="text-center mb-4">Payment Details</h2>
        @if($order)
            <div class="card shadow-sm p-4">
                <div class="d-flex justify-content-between mb-3">
                    <h4 class="card-title">Selected Package</h4>
                </div>
                <div class="list-group">
                    <div class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <strong>Package Name:</strong>
                        <span>{{ ucfirst($namePack) }}</span>
                    </div>
                    @if($order->selected_emiten !== "")
                        <div class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                            <strong>Item List :</strong>
                            <span>{{ $order->selected_emiten }}</span>
                        </div>
                    @endif
                    <div class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <strong>Price:</strong>
                        <span>Rp.{{ number_format($order->total_price ?? 0, 2) }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <strong>Payment Status :</strong>
                        @if($order->status === 'Paid')
                            <span class="text-white bg-success p-2 rounded"><strong>{{ (strtoupper($order->status)) }}</strong></span>
                        @elseif($order->status === 'Pending')
                            <span class="text-white bg-warning p-2 rounded"><strong>{{ (strtoupper($order->status)) }}</strong></span>
                        @else
                            <span class="text-white bg-danger p-2 rounded"><strong>{{ (strtoupper($order->status)) }}</strong></span>
                        @endif
                    </div>
                    @if($trxDetails['paymentType'] === 'bank_transfer' || $trxDetails['paymentType'] === 'cstore' || $trxDetails['paymentType'] === 'echannel')
                        <div class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                            <strong>Transaction Type :</strong>
                            @if($trxDetails['paymentType'] === 'bank_transfer')
                                <span class="text-white primary-color p-2 rounded">
                                    <strong>{{ strtoupper(str_replace('_', ' ', $trxDetails['paymentType'])) ?? '' }}</strong>
                                </span>
                            @elseif($trxDetails['paymentType'] === 'cstore')
                                <span class="text-white primary-color p-2 rounded">
                                    <strong>{{ strtoupper(str_replace('_', ' ', $trxDetails['trxStore'])) ?? '' }}</strong>
                                </span>
                            @elseif($trxDetails['paymentType'] === 'echannel')
                                <span class="text-white primary-color p-2 rounded">
                                    <strong>{{ strtoupper(str_replace('_', ' ', 'BANK TRANSFER')) ?? '' }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                            <strong>Virtual account number :</strong>
                            @if($trxDetails['paymentType'] === 'bank_transfer')
                                <span class="text-dark border-primary-color border-2 border p-2 rounded"><strong>{{ $trxDetails['trxVANumber'] ?? '' }}</strong></span>
                            @elseif($trxDetails['paymentType'] === 'cstore')
                                <span class="text-dark outline border-primary-color p-2 rounded"><strong>{{ $trxDetails['trxVANumberCStore'] ?? '' }}</strong></span>
                            @elseif($trxDetails['paymentType'] === 'echannel')
                                <span class="text-dark outline border-primary-color p-2 rounded"><strong>{{ $trxDetails['trxCompanyCode'] ?? '' }}</strong> <strong>{{ $trxDetails['trxVANumber'] ?? '' }}</strong></span>
                            @endif
                        </div>
                    @elseif($trxDetails['paymentType'] === 'qris')
                        <div class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                            <strong>Transaction Type :</strong>
                            @if($trxDetails['paymentType'] === 'qris')
                                <span class="text-white primary-color p-2 rounded">
                                    <strong>{{ strtoupper(str_replace('_', ' ', $trxDetails['paymentType'])) ?? '' }}</strong>
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                @if($order->status === 'Paid')
                    <div class="text-center mt-4">
                        <a href="/dashboard-core/" class="btn btn-custom2 btn-lg">Continue to Dashboard</a>
                    </div>
                @else
                    <div class="d-flex flex-row flex-wrap justify-content-center align-center gap-2">
                        <div class="text-center mt-2">
                            <a href="/payment" class="btn btn-custom2 btn-md">Re-order</a>
                        </div>
                        <div class="text-center mt-2">
                            <form action="{{ route('myOrderIndex') }}" method="GET">
                                @csrf
                                <button typp="submit" class="btn btn-warning btn-md text-white">Check Payment Status</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
