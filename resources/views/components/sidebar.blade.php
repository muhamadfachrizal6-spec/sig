@extends('layouts.bootstrap')
@section('content')
    <div class="sidebarNav d-none d-lg-block d-md-block primary-color">
        <ul class="nav flex-column gap-5">
            <li class="nav-item rounded-circle border border-light"
                style="cursor: pointer; width: 40px; height: 40px; display:flex; flex-direction: column; justify-content: center; align-items: center; flex-wrap: nowrap; padding: 2px"
                onclick="window.location.href='{{ route('dashboard-core') }}'">
                <img src="{{ asset('assets/img/logo/sig-white.png') }}" alt="Profile Image" class="img-fluid"
                    style="width: 100%; height: auto;">
            </li>
            <li class="nav-item hover-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                <a class="nav-link" href="{{ route('dashboard-core') }}"><i class="hover-item bi-bar-chart-fill"></i></a>
                <p class=" small-text text-center">Dashboard</p>
            </li>
            <li class="nav-item hover-item" data-bs-toggle="tooltip" data-bs-placement="right" id="Order" title="Order">
                <a class="nav-link" href="/payment"><i class="hover-item bi-cart-plus"></i></a>
                <p class=" small-text text-center">Order</p>
            </li>
            <li class="nav-item hover-item" data-bs-toggle="tooltip" data-bs-placement="right" title="My Order">
                <a class="nav-link" href="/myOrder"><i class="hover-item bi-bag-heart"></i></a>
                <p class=" small-text text-center">My Order</p>
            </li>
            <li class="nav-item hover-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Logout">
                <a class="nav-link hover-item" href="#" id="logoutLinks"><i class="hover-item bi-box-arrow-right"></i></a>
                <p class=" small-text text-center">Logout</p>
            </li>
        </ul>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutLink = document.getElementById('logoutLinks');
            if (logoutLink) {
                logoutLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    console.log('Logout link clicked')
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#43654C',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, logout!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('logout') }}";
                        }
                    });
                });
            }

            // Inisialisasi tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                    var tooltip = new bootstrap.Tooltip(tooltipTriggerEl); // Initialize tooltip
                });
            })
        });
    </script>
@endsection
