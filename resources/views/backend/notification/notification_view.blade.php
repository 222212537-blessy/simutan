@extends(auth()->user()->role === 'admin' ? 'admin.admin_master' : 
         (auth()->user()->role === 'supervisor' ? 'supervisor.supervisor_master' : 
         'pegawai.pegawai_master'))

@section(auth()->user()->role === 'admin' ? 'admin' : 
         (auth()->user()->role === 'supervisor' ? 'supervisor' : 'pegawai'))

<style>
    .notification-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #ddd;
        margin-bottom: 5px;
        text-decoration: none;
        color: inherit;
    }
    .notification-item:last-child {
        border-bottom: none;
    }
    .notification-message {
        flex-grow: 1;
    }
    .notification-actions {
        display: inline-flex;
        gap: 10px;
    }
    .notification-item.unread {
        background-color: #f5f5f5; /* Warna background untuk notifikasi yang belum dibaca */
    }
    .notification-item.read {
        background-color: #ffffff; /* Warna background untuk notifikasi yang sudah dibaca */
    }
    .text-muted {
        font-size: 12px;
    }
    .btn-mark-read {
        text-decoration: none;
        color: #28a745;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 text-info">List Notifikasi</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">List Notifikasi</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h4 class="card-title mb-0">Notifikasi</h4>
                        </div>

                        @if($notifications->isEmpty())
                            <p>Tidak ada notifikasi.</p>
                        @else
                            <div class="notification-list">
                                @php
                                    $todayNotifications = $notifications->filter(fn($n) => $n->created_at->isToday());
                                    $yesterdayNotifications = $notifications->filter(fn($n) => $n->created_at->isYesterday());
                                    $weekNotifications = $notifications->filter(fn($n) => $n->created_at->diffInDays() <= 7 && !$n->created_at->isToday() && !$n->created_at->isYesterday());
                                    $monthNotifications = $notifications->filter(fn($n) => $n->created_at->diffInWeeks() <= 4 && $n->created_at->diffInDays() > 7);
                                    $olderNotifications = $notifications->filter(fn($n) => $n->created_at->diffInMonths() > 1);
                                @endphp

                                @foreach(['Hari ini' => $todayNotifications, 'Kemarin' => $yesterdayNotifications, 'Minggu ini' => $weekNotifications, 'Bulan ini' => $monthNotifications, 'Dulu' => $olderNotifications] as $period => $groupedNotifications)
                                    @if($groupedNotifications->isNotEmpty())
                                        <h5>{{ $period }}</h5>
                                        @foreach($groupedNotifications as $notification)
                                            @php
                                                $url = route('permintaan.view', ['id' => $notification->permintaan_id]);
                                                if (str_contains($notification->message, 'Terdapat permintaan baru')) {
                                                    $url = route('permintaan.approve', ['id' => $notification->permintaan_id]);
                                                }
                                            @endphp
                                            <div class="notification-item {{ $notification->is_read ? 'read' : 'unread' }}">
                                                <div class="notification-message">
                                                    <a href="{{ $url }}">
                                                        <strong>{{ $notification->message }}</strong><br>
                                                        <small class="text-muted">&nbsp;{{ $notification->created_at->diffForHumans() }}</small>
                                                    </a>
                                                </div>
                                                <div class="notification-actions">
                                                    <a href="{{ route('notifications.markAsRead', $notification->id) }}" class="btn-mark-read" style="color: {{ $notification->is_read ? '#28a745' : '#e1a017' }};">
                                                        <i class="{{ $notification->is_read ? 'ti ti-check text-success' : 'ti ti-check' }}"></i> {{ $notification->is_read ? 'Dibaca' : 'Tandai dibaca' }}
                                                    </a>
                                                </div>
                                                
                                            </div>
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

