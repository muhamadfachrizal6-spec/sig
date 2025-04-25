<nav class="navbar navbar-expand-lg navbar-dark primary-color">
    <div class="container-fluid">
        <!-- Bagian Kiri: Logo dan Teks -->
        <div class="d-flex align-items-center">
            <img src="{{ asset('assets/img/logo/sig-white.png') }}" alt="SIG Logo" width="70" height="30" class="me-2"> 
            <span class="text-white fw-bold">SIG Group</span>
        </div>

        <!-- Bagian Kanan: Profil User -->
        <div class="d-flex align-items-center ms-auto">
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Menu
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                    <li class="nav-item p-2">
                        <a href="{{ url('/admin_analyze/index') }}"
                            class="nav-link {{ Request::is('admin_analyze/index') ? 'bg-white' : 'primary-color-text' }}">
                            <i class="bi bi-grid"></i>
                            Data Emiten
                        </a>
                    </li>
                    <li class="nav-item p-2">
                        <a href="{{ url('/admin_analyze/user/index') }}"
                            class="nav-link {{ Request::is('admin_analyze/user/index') ? 'bg-white' : 'primary-color-text' }}">
                            <i class="bi bi-person"></i>
                            Data User
                        </a>
                    </li>

                    <li class="nav-item p-2">
                        <a href="{{ url('/admin_analyze/data-order') }}" 
                            class="nav-link {{ Request::is('admin_analyze/data-order') ? 'bg-white' : 'primary-color-text' }}">
                            <i class="bi bi-cart"></i>
                            Data Orders
                        </a>
                    </li>

                    <li class="nav-item p-2">
                        <a href="{{ url('/admin_analyze/settings') }}" class="nav-link {{ Request::is('admin_analyze/settings') ? 'bg-white' : 'primary-color-text' }}">
                            <i class="bi bi-gear"></i>
                            Settings
                        </a>
                    </li>

                    <li class="nav-item p-2">
                        <a href="#" id="logoutLink" class="nav-link primary-color-text">
                            <i class="bi bi-box-arrow-right"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>


<script>
    document.getElementById('logoutLink').addEventListener('click', function(event) {
        event.preventDefault();

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
</script>