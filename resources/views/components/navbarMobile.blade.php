<nav class="navbar navbar-expand-lg navbar-light bg-light d-lg-none d-md-none">
    <div class="detail-navbar w-100 d-flex flex-row justify-content-between align-items-center">
        <div class="logonav">
            <a class="navbar-brand primary-color-text" href="#">Dashboard</a>
        </div>
        <div class="humberger-nav">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarNav"
                aria-controls="sidebarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>
    <div class="p-3 collapse navbar-collapse" id="sidebarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('dashboard-core') }}">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/payment">Purchase Emitten</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('profile-user') }}">Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="logoutLinkss">Logout<i class="text-white bi-box-arrow-right"></i></a>
            </li>
        </ul>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoutLink = document.getElementById('logoutLinkss');
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
