@extends('layouts.navigation-admin')

@section('contents')
    <div>
        <!-- Bagian Atas: Dashboard + Tambah Data + Search -->
        <div class="card primary-color-text align-items-center mb-4 p-2 max-w-fit">
            <h4 class="fw-bold">Data Emitten</h4>
        </div>

        <!-- Header Statistik -->
        <div class="row mb-4">
            <!-- Total Emiten -->
            <div class="col-12 col-sm-6 col-md-4 mb-3">
                <div class="card shadow-sm border-light p-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-currency-bitcoin fs-2 text-warning me-3"></i>
                        <div>
                            <h6 class="mb-0">Total Emiten</h6>
                            <h3 class="fw-bold">{{$totalEmiten}}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total User -->
            <div class="col-12 col-sm-6 col-md-4 mb-3">
                <div class="card shadow-sm border-light p-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people fs-2 text-success me-3"></i>
                        <div>
                            <h6 class="mb-0">Total User</h6>
                            <h3 class="fw-bold">{{$totalUser}}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="col-12 col-sm-6 col-md-4 mb-3">
                <div class="card shadow-sm border-light p-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box fs-2 text-purple me-3"></i>
                        <div>
                            <h6 class="mb-0">Total Orders</h6>
                            <h3 class="fw-bold">{{$orderCount}}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Data User -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="data-user-header d-flex flex-row justify-content-between align-items-center">
                    <h4>Data User</h4>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>Order Id</th>
                                <th>User Name</th>
                                <th>Status</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataOrder as $order)
                                <tr>
                                    <td>{{ $order['order_id'] }}</td>
                                    <td class="text-center">{{ $order['username'] }}</td>
                                    <td class="text-center">{{ $order['status'] }}</td>
                                    <td class="text-center">Rp.{{ number_format($order['total_price'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <!-- Tombol Previous -->
                        <li class="page-item {{ $dataOrder->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $dataOrder->previousPageUrl() }}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                
                        <!-- Logika Pagination -->
                        @for ($i = 1; $i <= $dataOrder->lastPage(); $i++)
                            @if ($i == 1 || $i == $dataOrder->lastPage() || ($i >= $dataOrder->currentPage() - 1 && $i <= $dataOrder->currentPage() + 1))
                                <li class="page-item {{ $i == $dataOrder->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $dataOrder->url($i) }}">{{ $i }}</a>
                                </li>
                            @elseif ($i == 2 && $dataOrder->currentPage() > 4)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @elseif ($i == $dataOrder->lastPage() - 1 && $dataOrder->currentPage() < $dataOrder->lastPage() - 3)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endfor
                
                        <!-- Tombol Next -->
                        <li class="page-item {{ $dataOrder->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $dataOrder->nextPageUrl() }}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endsection
