<nav class="navbar navbar-expand-lg navbar-expand-md bg-white d-none d-lg-flex d-md-flex justify-content-center">
    <div class="navbar-box d-flex flex-row justify-content-between w-90 align-items-center">
        <h2 class="primary-color-text">{{ $title }}</h2>
        <div class="navbar-profile" style="cursor: pointer">
            <div class="profile-wrapper border border-dark" style="border-radius:50%; cursor: pointer; width: 40px; height: 40px; display:flex; flex-direction: column; justify-content: center; align-items: center; flex-wrap: nowrap; padding: 2px">
                <img src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('assets/img/profile-image-blank.png') }}"
                alt="Profile Image" class="img-fluid"  style="width: 100%; height: 100%;">
            </div>
            <div id="profileDropdown" class="dropdown-menu position-absolute"
                style="display: none; top: 50px; right: 0;">
                <a class="dropdown-item" href="{{ route('profile-user') }}">View Profile</a>
                <a class="dropdown-item" href="#" id="logoutLinksss">Logout</a>
            </div>
        </div>
    </div>
</nav>

<script>
    document.querySelector('.navbar-profile').addEventListener('click', function(event) {
        event.stopPropagation();
        let dropdown = document.getElementById('profileDropdown');
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    });

    document.addEventListener('click', function(event) {
        let dropdown = document.getElementById('profileDropdown');
        if (!document.querySelector('.navbar-profile').contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoutLink = document.getElementById('logoutLinksss');
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
