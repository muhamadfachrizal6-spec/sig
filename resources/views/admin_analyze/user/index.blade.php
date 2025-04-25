@extends('layouts.navigation-admin')
@section('title', 'Dashboard')

@section('contents')
    <div class="container mt-4">
        <!-- Bagian Atas: Dashboard + Tambah Data + Search -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div class="d-flex w-100 w-md-auto">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search content.." aria-label="Search">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                </div>
            </div>
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
                                <th>Full Name</th>
                                <th>User Name</th>
                                <th>Profession</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Phone Number</th>
                                <th>User type</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td class="text-center">{{ $user->username }}</td>
                                    <td class="text-center">{{ $user->work }}</td>
                                    <td class="text-center">{{ $user->email }}</td>
                                    <td>{{ $user->address }}</td>
                                    <td class="text-center">{{ $user->phone }}</td>
                                    <td class="text-center">{{ $user->user_type }}</td>
                                    {{-- <td class="d-flex gap-2 justify-content-center">
                                        <a href="#" class="btn btn-warning btn-sm">
                                            <i class="text-white bi bi-pencil"></i>
                                        </a>
                                        <form action="#" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <!-- Tombol Previous -->
                        <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $users->previousPageUrl() }}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <!-- Logika Pagination -->
                        @for ($i = 1; $i <= $users->lastPage(); $i++)
                            @if ($i == 1 || $i == $users->lastPage() || ($i >= $users->currentPage() - 1 && $i <= $users->currentPage() + 1))
                                <li class="page-item {{ $i == $users->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $users->url($i) }}">{{ $i }}</a>
                                </li>
                            @elseif ($i == 2 && $users->currentPage() > 4)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @elseif ($i == $users->lastPage() - 1 && $users->currentPage() < $users->lastPage() - 3)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endfor

                        <!-- Tombol Next -->
                        <li class="page-item {{ $users->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $users->nextPageUrl() }}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endsection
