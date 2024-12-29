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
    <div class="collapse navbar-collapse" id="sidebarNav">
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
                <a class="nav-link" href="#" id="logoutLink">Logout<i class="text-white bi-box-arrow-right"></i></a>
            </li>
        </ul>
    </div>
</nav>
