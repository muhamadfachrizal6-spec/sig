@extends('layouts.navigation-admin')
@section('title', 'Dashboard')

@section('contents')
    <div id="loadingSpinner" style="display: none;">
        <div class="spinner">
            <!-- Spinner Icon -->
            <div class="lds-dual-ring"></div>
        </div>
    </div>
    <div class="mt-4">
        <!-- Bagian Atas: Dashboard + Tambah Data + Search -->
        <div class="card primary-color-text align-items-center mb-4 p-2 max-w-fit">
            <h4 class="fw-bold">Data Emiten</h4>
        </div>

        <!-- Header Statistik -->
        <div class="row mb-4">
            <div class="col-12 col-md-4 mb-3">
                <div class="card shadow-sm border-light p-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-currency-bitcoin fs-2 text-warning me-3"></i>
                        <div>
                            <h6 class="mb-0">Total Emiten</h6>
                            <h3 class="fw-bold">{{ $totalEmiten }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-3">
                <div class="card shadow-sm border-light p-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people fs-2 text-success me-3"></i>
                        <div>
                            <h6 class="mb-0">Total User</h6>
                            <h3 class="fw-bold">{{ $totalUser }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-3">
                <div class="card shadow-sm border-light p-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box fs-2 text-purple me-3"></i>
                        <div>
                            <h6 class="mb-0">Total Orders</h6>
                            <h3 class="fw-bold">{{ $orderCount }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Data Emiten -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-row flex-wrap gap-3 justify-content-between align-items-center mb-4 mt-2">
                    <div class="add-component d-inline-flex gap-1">
                        <a href="{{ route('admin_analyze.emiten.create') }}" class="btn btn-custom2">
                            <i class="fas fa-plus"></i> Tambah Data
                        </a>

                        <div class="sync-data">
                            <button type="button" id="syncButton" class="btn btn-custom2">
                                <i class="fas fa-sync-alt"></i> Sync Data
                            </button>
                        </div>
                    </div>

                    <div class="d-flex">
                        <form action="{{ route('admin_analyze.emiten.dashboard') }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search emiten..."
                                    aria-label="Search" value="{{ request()->get('search') }}">
                                <button type="submit" class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabel Data Emiten dengan class table-responsive -->
                @if ($companies->isEmpty())
                    <p class="text-center">No results found for "{{ request()->get('search') }}".</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light custom-th">
                                <tr class="text-center">
                                    <th>Name Emiten</th>
                                    <th>Ticker</th>
                                    <th>Category</th>
                                    <th>Market Cap</th>
                                    <th>Price</th>
                                    <th>Growth Net Profit (%)</th>
                                    <th>Action</th>
                                    <th>Key Ratio</th>
                                    <th>Key Statistics</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($companies as $company)
                                    <tr>
                                        <td>{{ $company->name }}</td>
                                        <td class="text-center">{{ $company->ticker }}</td>
                                        <td class="text-center align-items-center">
                                            <span class="badge bg-secondary">{{ $company->category }}</span>
                                        </td>
                                        @if ($company->marketShares->isNotEmpty())
                                            @php
                                                $marketShare = $company->marketShares->first();
                                            @endphp
                                            <td class="text-center">{{ $marketShare->market_cap }}</td>
                                            <td class="text-center">{{ $marketShare->price }}</td>
                                            <td class="text-center">{{ $marketShare->growth_net_profit }}%</td>
                                        @else
                                            <td class="text-center" colspan="3">No data available</td>
                                        @endif
                                        <td class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('admin_analyze.emiten.edit', $company->id) }}"
                                                class="btn btn-outline-success btn-sm">
                                                <i class="btn-hover text-success bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin_analyze.emiten.destroy', $company->id) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="confirmDeletion(event, this)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin_analyze.key_ratio.edit', ['companyId' => $company->id, 'id' => $company->id]) }}"
                                                class="btn btn-outline-success btn-sm">
                                                <i class="btn-hover text-success bi bi-pencil"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin_analyze.key_statistics.edit', ['companyId' => $company->id, 'id' => $company->id]) }}"
                                                class="btn btn-outline-success btn-sm">
                                                <i class="btn-hover text-success bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <!-- Tombol Previous -->
                            <li class="page-item {{ $companies->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $companies->previousPageUrl() }}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <!-- Logika Pagination -->
                            @for ($i = 1; $i <= $companies->lastPage(); $i++)
                                @if (
                                    $i == 1 ||
                                        $i == $companies->lastPage() ||
                                        ($i >= $companies->currentPage() - 1 && $i <= $companies->currentPage() + 1))
                                    <li class="page-item {{ $i == $companies->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $companies->url($i) }}">{{ $i }}</a>
                                    </li>
                                @elseif ($i == 2 && $companies->currentPage() > 4)
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                @elseif ($i == $companies->lastPage() - 1 && $companies->currentPage() < $companies->lastPage() - 3)
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                @endif
                            @endfor

                            <!-- Tombol Next -->
                            <li class="page-item {{ $companies->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $companies->nextPageUrl() }}" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('success_delete'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success_delete') }}',
                    showConfirmButton: true,
                    confirmButtonText: 'Confirm'
                });
            });
        </script>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <script>
        function confirmDeletion(event, element) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the data!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    element.closest("form").submit();
                }
            });
        }
    </script>

    <script>
        document.getElementById('syncButton').addEventListener('click', function() {
            document.getElementById('loadingSpinner').style.display = 'flex';

            fetch('{{ route('spreadsheet.index') }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to sync data: ' + error.message);
                })
                .finally(() => {
                    document.getElementById('loadingSpinner').style.display = 'none';
                });
        });
    </script>
@endsection
