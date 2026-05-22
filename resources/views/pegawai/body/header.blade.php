<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="index.html" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('backend/assets/images/logo-bps.png') }}" alt="logo-sm" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('backend/assets/images/logo-long.png') }}" alt="logo-dark" height="44">
                    </span>
                </a>

                <a href="index.html" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('backend/assets/images/logo-bps.png') }}" alt="logo-sm-light" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('backend/assets/images/logo-long.png') }}" alt="logo-light" height="44">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                <i class="ri-menu-2-line align-middle"></i>
            </button>
        </div>

        <div class="d-flex">
            <!-- Notification Dropdown -->
            <div class="dropdown d-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" id="notification-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="ri-notification-3-line"></i>
                    <span class="badge bg-danger">{{ session('unreadCount', 0) }}</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end notification-menu">
                    <h6 class="dropdown-header">Notifications</h6>
                    <div class="notification-list">
                        @forelse (session('notifications', []) as $notification)
                            <a class="dropdown-item" href="{{ route('permintaan.approve', ['id' => $notification->permintaan_id]) }}">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-primary rounded-circle text-white">
                                                <i class="ri-check-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="mt-0 mb-1">{{ $notification->message }}</h6>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <p class="text-center">Tidak ada notifikasi terbaru</p>
                        @endforelse
                    </div>
                    <a class="dropdown-item text-center" href="{{ route('notifications.viewAll') }}">Lihat Selengkapnya</a>
                </div>
            </div>

            <!-- Fullscreen Button -->
            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                    <i class="ri-fullscreen-line"></i>
                </button>
            </div>

            @php
            $id = Auth::user()->id;
            $adminData = App\Models\User::find($id);
            @endphp

            <div class="dropdown d-inline-block user-dropdown">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    
                    <!-- Conditional Styling for User Avatar -->
                    @if(Auth::user()->id == 1)
                        <img class="rounded-circle header-profile-user" 
                            style="object-fit: cover; object-position: center top 10%;" 
                            src="{{ !empty($adminData->foto) ? url($adminData->foto) : url('upload/no_image.jpg') }}"
                            alt="Header Avatar">
                    @else
                        <img class="rounded-circle header-profile-user" 
                            style="object-fit: cover; 
                            {{ Auth::user()->id == 1 ? 'object-position: top center; transform: translateY(-10px);' : 'object-position: top center;' }}" 
                            src="{{ !empty($adminData->foto) ? url($adminData->foto) : url('upload/no_image.jpg') }}"
                            alt="Header Avatar">
                    @endif
                    
                    <span class="d-none d-xl-inline-block ms-1">{{ $adminData->panggilan }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('pegawai.profile') }}"><i class="ri-user-line align-middle me-1"></i> Profile</a>
                    <a class="dropdown-item" href="{{ route('pegawai.change.password') }}"><i class="ri-wallet-2-line align-middle me-1"></i> Change Password</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="{{ route('admin.logout') }}"><i class="ri-shut-down-line align-middle me-1 text-danger"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Styles for Notification Dropdown -->
<style>
    .notification-menu {
        width: 500px; /* Atur lebar notifikasi menjadi 500px */
    }

    .notification-list a {
        display: flex;
        align-items: center;
        padding: 10px;
        text-decoration: none;
        color: inherit;
    }

    .notification-list a:hover {
        background-color: #f1f1f1;
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
    }

    .avatar-title {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
        font-size: 16px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notificationDropdown = document.getElementById('notification-dropdown');
        notificationDropdown.addEventListener('click', function () {
            fetch('{{ route('notifications.markAllRead') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
        });
    });
</script>
