@extends('layouts.bootstrap')
@section('content')
    <div class="d-flex flex-column flex-shrink-0 p-3 primary-color d-none d-lg-block"
        style="width: 250px; min-height: 100vh;">
        <div class="profile-admin w-100" style="min-height: 10vh">
            <a href="/" class="text-white text-decoration-none">
                <span class="fs-5">Dashboard Admin</span>
            </a>
        </div>
        <ul class="nav nav-pills flex-column gap-2">
            <li class="nav-item">
                <a href="{{ url('/admin_analyze/index') }}"
                    class="nav-link text-decoration-none {{ Request::is('admin_analyze/index') ? 'bg-white primary-color-text' : 'text-white' }}">
                    <i class="bi bi-grid"></i>
                    Data Emiten
                </a>
            </li>

            <!-- Data User -->
            <li class="nav-item">
                <a href="{{ url('/admin_analyze/user/index') }}"
                    class="nav-link {{ Request::is('admin_analyze/user/index') ? 'bg-white primary-color-text' : 'text-white' }}">
                    <i class="bi bi-person"></i>
                    Data User
                </a>
            </li>

            <!-- Data Orders -->
            <li class="nav-item">
                <a href="{{ url('/admin_analyze/data-order') }}"
                    class="nav-link {{ Request::is('admin_analyze/data-order') ? 'bg-white primary-color-text' : 'text-white' }}">
                    <i class="bi bi-cart"></i>
                    Data Orders
                </a>
            </li>

            <!-- Settings -->
            <li>
                <a href="{{ url('/admin_analyze/settings') }}"
                    class="nav-link {{ Request::is('admin_analyze/settings') ? 'bg-white primary-color-text' : 'text-white' }}">
                    <i class="bi bi-gear"></i>
                    Settings
                </a>
            </li>

            <li>
                <a href="#" id="logoutLinkz" class="nav-link text-white">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutLink = document.getElementById('logoutLinkz');
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
        });
    </script>
@endsection
