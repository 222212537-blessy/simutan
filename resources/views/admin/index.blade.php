@extends(auth()->user()->role === 'admin' ? 'admin.admin_master' : 
         (auth()->user()->role === 'supervisor' ? 'supervisor.supervisor_master' : 
         'pegawai.pegawai_master'))

@section(auth()->user()->role === 'admin' ? 'admin' : 
         (auth()->user()->role === 'supervisor' ? 'supervisor' : 'pegawai'))

<head>
    <title>
        Dashboard | SIMUTAN
    </title>
</head>

{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"> --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/handlebars@4.7.7/dist/handlebars.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .tooltip-text {
            display: none;
            position: absolute;
            bottom: 0;
            left: 0;
            transform: translateY(100%);
            white-space: nowrap;
            z-index: 6;
        }
    
        .position-relative:hover .tooltip-text {
            display: block;
        }
    
        .position-relative {
            position: relative;
        }
    
        .quarter-circle {
            position: absolute;
            border-radius: 50% 0 50% 50%;
            background-color: rgba(0, 123, 255, 0.3);
            z-index: 5;
        }
    
        .large-circle {
            width: 40px;
            height: 40px;
            top: 0;
            right: 0;
        }
    
        .small-circle {
            width: 30px;
            height: 30px;
            background-color: rgba(0, 123, 255, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.25rem;
            font-weight: bold;
            top: 0;
            right: 0;
            margin: 0;
        }
    
        .tooltip-text {
            top: 0;
            right: 0;
            transform: translate(0, 0);
        }
    
        .gradient-background::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 123, 255, 0.5), rgba(0, 123, 255, 0) 80%);
            z-index: 1;
            pointer-events: none;
            border-radius: 0.375rem;
        }
    
        .img-fluid {
            position: relative;
            z-index: 2;
        }
    
        .leaderboard-container {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 20px;
        }
    
        .leaderboard-item {
            /* text-align: center; */
        }
    
        .leaderboard-item:nth-child(1) {
            order: 2;
            transform: translateY(-10%);
        }
    
        .leaderboard-item:nth-child(2) {
            order: 1;
        }
    
        .leaderboard-item:nth-child(3) {
            order: 3;
            transform: translateY(5%);
        }

        #datatable_filter {
    justify-content: end;
}

        .card, .gradient-background {
    border-radius: 0.375rem; /* Sesuaikan dengan kebutuhan */
    overflow: hidden; /* Supaya elemen di dalamnya tidak keluar dari border-radius */
}

.overlay-label {
        bottom: 10px;
        left: 0;
        background-color: rgba(0, 123, 255, 0.7);

        /* backgorund-color: rgba(104, 185, 46, 0.7); */
        color: #fff;
        width: 75%; /* 3/4 dari lebar gambar */
        padding: 5px;
        border-radius: 0 15px 15px 0;
        /* text-align: center; */
        transform: translateX(-100%);
        transition: transform 1s ease-in-out;
        z-index: 5;
    }

    .position-relative:hover .overlay-label {
        transform: translateX(0);
    }

    /* Animasi untuk gambar naik dari bawah */
    @keyframes slide-up {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .animate-up {
        animation: slide-up 1s ease-out;
    }
    .greeting-section {
    background: #ffffff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.greeting-section:hover {
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    transform: scale(1.02);
}

.greeting-header h2 {
    font-size: 2.5rem;
    color: #343a40;
    margin-bottom: 1rem;
    transition: color 0.3s ease;
}

.greeting-header h2:hover {
    color: #007bff;
}

.greeting-header p {
    font-size: 1.125rem;
    color: #6c757d;
    line-height: 1.6;
}

.marquee {
    overflow: hidden;
    white-space: nowrap;
    box-sizing: border-box;
    margin-top: -10px; /* Mengatur posisi tulisan lebih ke atas */
    height: 2rem;
}

.marquee p {
    display: inline-block;
    width: 200%;
    animation: marquee 20s linear infinite;
    width: max-content;
}

@keyframes marquee {
    0% {
        transform: translateX(100%);
    }
    100% {
        transform: translateX(-100%);
    }
}

.greeting-actions {
    margin-top: 1.5rem;
}

.greeting-actions .btn {
    margin-right: 10px;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.greeting-actions .btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

#loading {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #eff3f6; /* Semi-transparent background */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999; /* Ensure spinner is above all content */
    opacity: 0; /* Mulai dengan opasitas 0 */
    transition: opacity 1s ease-in-out; /* Tambahkan transisi fade-in */
}

#loading.fade-in {
    opacity: 1; /* Opasitas 1 saat efek fade-in terjadi */
}
.coin {
  width: 100px;
  height: 100px;
  /* background-color: gold; */
  border-radius: 50%;
  position: relative;
  transform-style: preserve-3d;
  animation: spin-coin 2s infinite linear;
}

.coin:before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 80px;
  height: 80px;
  /* background-color: #e6b800; */
  border-radius: 50%;
}

@keyframes spin-coin {
  0% {
    transform: rotateY(0deg);
  }
  100% {
    transform: rotateY(360deg);
  }
}

.pulsing-dot {
        width: 80px;
        height: 80px;
        /* background: #dff0ff; */
        border-radius: 50%;
        animation: pulse 1.5s infinite;
        display: flex;
    justify-content: center; /* Memusatkan gambar di dalam elemen pulsing-dot */
    align-items: center;
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.5); opacity: 0.5; }
        100% { transform: scale(1); opacity: 1; }
    }


</style>



<div class="page-content">
<div class="container-fluid">

{{-- <!-- start page title -->
<div class="row">
<div class="col-12">
<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0 text-info">Dashboard</h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">SIMUTAN</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </div>

</div>
</div>
</div>
<!-- end page title --> --}}
{{-- <h2 class="mb-4 fw-bold">Selamat Datang di SIMUTAN! </h2> --}}
{{-- <p class="mb-4">Sistem Mutasi Persediaan (SIMUTAN) adalah sistem yang digunakan untuk mengetahui jumlah dan mutasi barang serta menilai dari selisih kelebihan atau kekurangan barang.</p> --}}

<div class="card">
    <div class="card-body">
        <h3 class="text-info mb-3" style="font-weight: 700">Halo {{ $user->panggilan }}, Selamat Datang di SIMUTAN!</h3>
        <div>
            <p style="font-size: 16px; margin-bottom:0.5rem;">Sistem Mutasi Persediaan (SIMUTAN) adalah sistem yang digunakan untuk mengetahui jumlah dan mutasi barang serta menilai dari selisih kelebihan atau kekurangan barang</p>
        </div>
        {{-- <p class="text-muted fs-5">
            Selamat datang di <strong>SIMUTAN</strong>, sistem mutasi persediaan yang dirancang untuk memberikan visibilitas lengkap terhadap jumlah dan perubahan barang Anda. Dengan fitur mutasi barang yang terperinci, SIMUTAN memudahkan Anda untuk memantau kelebihan dan kekurangan persediaan, serta memastikan efisiensi dalam manajemen stok. Jelajahi fitur-fitur kami dan tingkatkan kontrol Anda terhadap persediaan dengan lebih mudah!
        </p> --}}
    </div>
    {{-- <div class="greeting-actions">
        <button class="btn btn-primary" onclick="showMoreInfo()">Pelajari Lebih Lanjut</button>
        <button class="btn btn-outline-secondary" onclick="viewStats()">Lihat Statistik</button>
    </div> --}}
</div>

<div class="row gx-4">
<div class="col-xl-3 col-md-6">
<div class="card">
    <div class="card-body">
        <div class="d-flex">
            <div class="flex-grow-1">
                <h5 class="font-size-14 mb-1">Total Permintaan</h5>
                <h4 class="font-size-20 mb-1 text-info"><strong>Diajukan</strong></h4>
                <h6 class="mb-0 text-muted small">Bulan Ini</h6>
                <h4 class="mb-0 mt-2">{{ $totalPermintaanBulanIni ?? 'Tidak ada data' }}</h4>
            </div>
            <div class="avatar-sm">
                <span class="avatar-title bg-primary text-primary rounded-3">
                    <i class="mdi mdi-clipboard-text-multiple-outline font-size-24"></i>  
                </span>
            </div>
        </div>                                            
    </div><!-- end cardbody -->
</div><!-- end card -->
</div><!-- end col -->
<div class="col-xl-3 col-md-6">
<div class="card">
    <div class="card-body">
        <div class="d-flex">
            <div class="flex-grow-1">
                <h5 class="font-size-14 mb-1">Total Permintaan</h5>
                <h4 class="font-size-20 mb-1 text-info"><strong>Selesai</strong></h4>
                <h6 class="mb-0 text-muted small">Bulan Ini</h6>
                <h4 class="mb-0 mt-2">{{ $totalPermintaanSelesai ?? 'Tidak ada data' }}</h4>
            </div>
            <div class="avatar-sm">
                <span class="avatar-title bg-success text-success rounded-3">
                    <i class="mdi mdi-clipboard-check-outline font-size-24"></i>  
                </span>
            </div>
        </div>                                              
    </div><!-- end cardbody -->
</div><!-- end card -->
</div><!-- end col -->
<div class="col-xl-3 col-md-6">
<div class="card">
    <div class="card-body">
        <div class="d-flex">
            <div class="flex-grow-1">
                <h5 class="font-size-14 mb-1">Total Permintaan</h5>
                <h4 class="font-size-20 mb-1 text-info"><strong>Pending</strong></h4>
                <h6 class="mb-0 text-muted small">Bulan Ini</h6>
                <h4 class="mb-0 mt-2">{{ $totalPermintaanPending ?? 'Tidak ada data' }}</h4>
            </div>
            <div class="avatar-sm">
                <span class="avatar-title bg-warning text-warning rounded-3">
                    <i class="mdi mdi-clipboard-clock-outline font-size-24"></i>  
                </span>
            </div>
        </div>                                              
    </div><!-- end cardbody -->
</div><!-- end card -->
</div><!-- end col -->
<div class="col-xl-3 col-md-6">
<div class="card">
    <div class="card-body">
        <div class="d-flex">
            <div class="flex-grow-1">
                <h5 class="font-size-14 mb-1">Total Permintaan</h5>
                <h4 class="font-size-20 mb-1 text-info"><strong>Ditolak</strong></h4>
                <h6 class="mb-0 text-muted small">Bulan Ini</h6>
                <h4 class="mb-0 mt-2">{{ $totalPermintaanRejected ?? 'Tidak ada data' }}</h4>
            </div>
            <div class="avatar-sm">
                <span class="avatar-title bg-danger text-danger rounded-3">
                    <i class="mdi mdi-clipboard-remove-outline font-size-24"></i>  
                </span>
            </div>
        </div>                                              
    </div><!-- end cardbody -->
</div><!-- end card -->
</div><!-- end col -->
</div><!-- end row -->

<div class="row gx-4">
    <!-- Chart for Top Score Barang Diminta -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3 text-info">Top 5 Barang dengan Permintaan Tertinggi</h4>
                <canvas id="myChartBarang" width="400" height="237" style="max-width: 100%;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3 text-info" style="padding-bottom: 25px;">Top 3 Pegawai dengan Permintaan Terbanyak</h4>
                <div class="leaderboard-container" style="position: relative;">
    
                    @for($i = 0; $i < 3; $i++)
                        @if(isset($topUsers[$i]) && $topUsers[$i]->requests > 0)
                            @php $user = $topUsers[$i]; @endphp
                            <div class="leaderboard-item" style="border-radius: 0.375rem; overflow: hidden; position: relative; background: linear-gradient(to top right, #3671ac 30%, rgba(54, 113, 172, 0.608) 100%); aspect-ratio: 9/16;">
                                <div class="svg-wave" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 2;">
                                    <svg viewBox="0 0 500 150" preserveAspectRatio="none" style="width: 100%; height: 100%;">
                                        <!-- Wave Paths -->
                                        <path d="M 0 70 C 150 40 350 60 500 40 L 500 0 L 0 0 Z" fill="#043277" opacity="0.4"></path>
                                        <path d="M 0 60 C 150 30 350 50 500 30 L 500 0 L 0 0 Z" fill="#043277" opacity="0.6"></path>
                                        <path d="M 0 50 C 150 20 350 40 500 20 L 500 0 L 0 0 Z" fill="#043277" opacity="0.1"></path>
                                        <path d="M 0 40 C 150 10 350 30 500 10 L 500 0 L 0 0 Z" fill="#043277"></path>
                                    </svg>
                                </div>
                                <div class="position-relative" style="overflow: hidden; margin: 0; padding: 0; position: relative; width: 100%; height: 100%;">
                                    <img src="{{ asset($user->foto) }}" class="img-fluid rounded animate-up" alt="{{ $user->name }}" style="width: 350px; height: auto; object-fit: cover; object-position: center;">
                                    <div class="quarter-circle large-circle"></div>
                                    <div class="quarter-circle small-circle">{{ $i + 1 }}</div>
                                    <div class="overlay-label position-absolute">
                                        <strong>{{ $user->panggilan }}</strong><br>
                                        {{ $user->requests }} permintaan
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="leaderboard-item d-flex align-items-center justify-content-center" style="border-radius: 0.375rem; overflow: hidden; position: relative; background: linear-gradient(to top right, #3671ac 30%, rgba(54, 113, 172, 0.608) 100%); aspect-ratio: 9/16;">
                                <div class="svg-wave" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 2;">
                                    <svg viewBox="0 0 500 150" preserveAspectRatio="none" style="width: 100%; height: 100%;">
                                        <!-- Wave Paths -->
                                        <path d="M 0 70 C 150 40 350 60 500 40 L 500 0 L 0 0 Z" fill="#043277" opacity="0.4"></path>
                                        <path d="M 0 60 C 150 30 350 50 500 30 L 500 0 L 0 0 Z" fill="#043277" opacity="0.6"></path>
                                        <path d="M 0 50 C 150 20 350 40 500 20 L 500 0 L 0 0 Z" fill="#043277" opacity="0.1"></path>
                                        <path d="M 0 40 C 150 10 350 30 500 10 L 500 0 L 0 0 Z" fill="#043277"></path>
                                    </svg>
                                </div>
                                <div style="color: white; text-align: center; width: 350px; height: auto;">
                                    <div class="quarter-circle large-circle"></div>
                                    <div class="quarter-circle small-circle">{{ $i + 1 }}</div>
                                    <p style="color: white; margin: 0px; font-size:16px">Belum Ada<br>Top User #{{ $i + 1 }}</p>
                                </div>
                            </div>
                        @endif
                    @endfor
    
                </div>
            </div>
        </div>
    </div>
</div>

 

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="card-title mb-0">Permintaan Barang [Pending]</h4>
                    <a href="{{ route('pilihan.add') }}" class="btn btn-info waves-effect waves-light ml-3">
                        <i class="mdi mdi-plus-circle"></i> Ajukan Permintaan
                    </a>
                </div>

                <table id="datatable" class="table table-bordered yajra-datatable" 
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th class="align-content-center">Tanggal</th>
                            <th class="align-content-center">Nama Pegawai</th>
                            <th class="align-content-center">Catatan</th>
                            <th width="17%" class="align-content-center" style="white-space: nowrap;">Status</th>
                           
                            <th width="1%" class="text-center align-content-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <!-- DataTable akan mengisi baris di sini -->
                    </tbody>
                </table>
                <div class="d-flex justify-content-end mt-3">
                    <a href="{{ route('permintaan.all') }}" class="text-info custom-link">
                        Lihat Selengkapnya <i class="ti ti-arrow-right font-size-14"></i>
                    </a>
                </div>

            </div> <!-- end col -->
        </div> <!-- end row -->              
</div>

</div>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-images"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script type="text/javascript">
                $(document).ready(function() {
                    var table = $('.yajra-datatable').DataTable({
                        // processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: {
                            url: "{{ route('permintaan.all') }}",
                            data: function(d) {
                                d.admin_approval = $('#admin_approval_filter').val() || 'pending';
                                d.supervisor_approval = $('#supervisor_approval_filter').val();
                            }
                        },
                        columns: [
                            { data: 'date', name: 'date', className: 'align-content-center' },
                            { data: 'created_by', name: 'created_by', className: 'align-content-center' },
                            { data: 'description', name: 'description', className: 'align-content-center' },
                            {
                                data: 'approval_status',
                                name: 'approval_status',
                                searchable: false,
                                className: 'text-center align-content-center'
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false,
                                className: 'text-center align-content-center no-export',
                                render: function(data, type, row) {
                                    var viewUrl = "{{ route('permintaan.view', ':id') }}".replace(':id', row.id);
                                    var approveUrl = "{{ route('permintaan.approve', ':id') }}".replace(':id', row.id);
                                    var printUrl = "{{ route('permintaan.print', ':id') }}".replace(':id', row.id);
                
                                    var viewButton = `
                                        <a href="${viewUrl}" class="btn btn-sm me-2 text-primary hover:bg-primary" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: blue; padding: 15px;" data-tooltip="Lihat Permintaan">
                                            <i class="ti ti-eye font-size-20 align-middle"></i>
                                        </a>
                                    `;
                
                                    var approveOrPrintButton = row.status === 'approved by supervisor' ? `
                                        <a href="${printUrl}" class="btn btn-sm text-danger hover:bg-danger" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: red; padding: 15px;" data-tooltip="Cetak Permintaan">
                                            <i class="ti ti-printer font-size-20 align-middle text-danger"></i>
                                        </a>
                                    ` : `
                                        <a href="${approveUrl}" class="btn btn-sm ${row.status === 'pending' ? 'hover:bg-success' : ''}" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; ${row.status === 'pending' ? 'color: green;' : 'color: gray; pointer-events: none; opacity: 0.5;'} padding: 15px;" data-tooltip="Setujui Permintaan">
                                            <i class="ti ti-clipboard-check font-size-20 align-middle"></i>
                                        </a>
                                    `;
                
                                    return `
                                        <div class="text-center d-flex justify-content-center align-items-center">
                                            ${viewButton}
                                            ${approveOrPrintButton}
                                        </div>
                                    `;
                                }
                            }
                        ],
                        // dom: 'Brftip',
                        // buttons: [
                        //     {
                        //         extend: 'collection',
                        //         text: 'Export &nbsp',
                        //         className: 'form-select',
                        //         buttons: [
                        //             {
                        //                 extend: 'excelHtml5',
                        //                 text: 'Export Excel',
                        //                 title: 'Data Export',
                        //                 exportOptions: {
                        //                     columns: ':not(.no-export)' // Eksklusi kolom dengan kelas 'no-export'
                        //                 },
                        //             },
                        //             {
                        //                 extend: 'copy',
                        //                 text: 'Copy',
                        //                 exportOptions: {
                        //                     columns: ':not(.no-export)' // Eksklusi kolom dengan kelas 'no-export'
                        //                 },
                        //             },
                        //             {
                        //                 extend: 'csv',
                        //                 text: 'CSV',
                        //                 exportOptions: {
                        //                     columns: ':not(.no-export)' // Eksklusi kolom dengan kelas 'no-export'
                        //                 },
                        //             },
                        //             {
                        //                 extend: 'pdf',
                        //                 text: 'PDF',
                        //                 exportOptions: {
                        //                     columns: ':not(.no-export)' // Eksklusi kolom dengan kelas 'no-export'
                        //                 },
                        //             },
                        //             {
                        //                 extend: 'print',
                        //                 text: 'Print',
                        //                 exportOptions: {
                        //                     columns: ':not(.no-export)' // Eksklusi kolom dengan kelas 'no-export'
                        //                 },
                        //             }
                        //         ]
                        //     }
                        // ],
                        initComplete: function() {
                            // Filter untuk admin approval
                            // Filter untuk approval admin
                            var adminSelect = $('<select id="admin_approval_filter" class="form-select-sm" style="width: 20%; border: 1px solid #1156bf; color:#043277; font-weight: 500;"><option value="pending" selected>Admin Pending</option></select>')
        .appendTo($('#datatable_filter').css('display', 'flex').css('align-items', 'center').css('gap', '10px')).css('justify-content', 'end')
        .on('change', function() {
            table.draw();
        });
        adminSelect.val('pending'); // Set default value ke "pending"

// // Filter untuk approval supervisor
// var supervisorSelect = $('<select id="supervisor_approval_filter" class="form-select-sm" style="width: 24%; border: 1px solid #1156bf; color:#043277; font-weight: 500;"><option value="">Semua Status Supervisor</option></select>')
//     .appendTo($('#datatable_filter').css('display', 'flex').css('align-items', 'center').css('gap', '10px'))
//     .on('change', function() {
//         table.draw();
//     });

// // Menambahkan opsi untuk approval supervisor
// supervisorSelect.append('<option value="pending">Supervisor Pending</option>');
// supervisorSelect.append('<option value="approved by supervisor">Supervisor Approved</option>');
// supervisorSelect.append('<option value="rejected by supervisor">Supervisor Rejected</option>');

                
                            // Styling untuk select
                            // Styling untuk select
                $('.form-select').each(function() {
                                $(this).css({
                                    'display': 'block',
                                    'padding': '.47rem 1.75rem .47rem .75rem',
                                    '-moz-padding-start': 'calc(.75rem - 3px)',
                                    'font-size': '.9rem',
                                    'font-weight': '500',
                                    'line-height': '1.5',
                                    'color': '#043277',
                                    'background-color': '#e2f3fe',
                                    'background-image': 'url("data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 16 16\'%3e%3cpath fill=\'none\' stroke=\'%230a1832\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M2 5l6 6 6-6\'/%3e%3c/svg%3e")',
                                    'background-repeat': 'no-repeat',
                                    'background-position': 'right .75rem center',
                                    'background-size': '16px 12px',
                                    'border': '1px solid #1156bf',
                                    'border-radius': '.25rem',
                                    'transition': 'border-color .15s ease-in-out, box-shadow .15s ease-in-out',
                                    'appearance': 'none'
                                });
                            
                });

                $('.form-control').each(function() {
                    $(this).css({
                        'margin-bottom': '0px',
                        'height': '1.67rem',
                        'border': '1px solid #1156bf'
                    });
                });

                $('label').each(function() {
                    $(this).css({
                        'margin-bottom': '0px',
                        'height': '1.67rem',
                        'font-weight': '600',
                        'color': '#043277'
                    });
                });

                $('select[name="datatable_length"]').css({
                    'font-size': '.875rem', // Misalnya, menambahkan ukuran font jika diperlukan
                    'height': '1.67rem',
                    'border': '1px solid #043277',
                    'border-radius': '.25rem',
                });
                
                            var observer = new MutationObserver(function(mutations) {
                                mutations.forEach(function(mutation) {
                                    $('.dt-button-background').remove(); // Hapus elemen dengan class .dt-button-background
                                });
                            });
                
                            // Memulai observer pada elemen yang mengandung tombol
                            observer.observe(document.body, {
                                childList: true,
                                subtree: true
                            });
                        }
                    });
                    $(document).ready(function() {
            $('#datatable_wrapper .row').first().children().eq(0).removeClass('col-md-6').addClass('col-md-3');
            $('#datatable_wrapper .row').first().children().eq(1).removeClass('col-md-6').addClass('col-md-9');
            $('.dt-button').removeClass('dt-button buttons-collection');
            $('.dt-button-background').remove(); // Hapus semua elemen dengan class .dt-button-background
            $('.dt-button-down-arrow').remove(); // Hapus semua elemen dengan class .dt-button-down-arrow
            $('.form-control').removeClass('form-control-sm');
            $('select[name="datatable_length"]').removeClass('form-control p-0');
            $('.custom-select').removeClass('custom-select-sm');
        });
                });
                </script>

<script>
    // Initialize Bootstrap tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (element) {
        new bootstrap.Tooltip(element);
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var barangs = @json($topBarangs);

    var labels = barangs.map(function(barang) {
        // Mengambil kata pertama dari nama barang
        return barang.nama.split(' ')[0];
    });

    var data = barangs.map(function(barang) {
        return barang.total_qty;
    });

    var ctxBarang = document.getElementById('myChartBarang').getContext('2d');

    // Membuat gradient horizontal untuk background
    var gradientBackground = ctxBarang.createLinearGradient(0, 0, 400, 0);
    gradientBackground.addColorStop(0, 'rgba(255, 255, 255, 0.5)');  // Biru dengan opacity 0.5
    gradientBackground.addColorStop(0.3, 'rgba(54, 113, 172, 0.5)');  // Ungu dengan opacity 0.5
    gradientBackground.addColorStop(1, 'rgba(4, 50, 119, 1)');

    // Membuat gradient horizontal untuk border dengan opacity 1
    var gradientBorder = ctxBarang.createLinearGradient(0, 0, 400, 0);
    gradientBorder.addColorStop(1, 'rgba(4, 50, 119, 0.5)');  // Biru dengan opacity 0.5
    gradientBorder.addColorStop(0.5, 'rgba(0, 123, 255, 0.5)');  // Ungu dengan opacity 0.5
    gradientBorder.addColorStop(0, 'rgba(0, 123, 255, 0.5)');

    var myChartBarang = new Chart(ctxBarang, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Barang Diminta',
                data: data,
                backgroundColor: gradientBackground,
                borderColor: gradientBorder,
                borderWidth: 1,
                borderRadius: 5,
                barThickness: 25, // Menentukan lebar bar
                borderSkipped: false
            }]
        },
        options: {
            indexAxis: 'y',
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
            plugins: {
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.dataset.label + ': ' + tooltipItem.formattedValue;
                        }
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutBounce'
            }
        }
    });
});

</script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    var topUsers = @json($topUsers); // Data pengguna yang dikirim dari Laravel
    
    var labels = topUsers.map(user => user.panggilan);
    var data = topUsers.map(user => user.requests);
    
    var ctxUser = document.getElementById('myChartUser').getContext('2d');
    
    var myChartUser = new Chart(ctxUser, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Permintaan',
                data: data,
                backgroundColor: 'rgba(0,0,0,0)' // Tidak digunakan
            }]
        },
        options: {
            indexAxis: 'x',
            scales: {
                x: {
                    display: true
                },
                y: {
                    beginAtZero: true
                }
            },
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ' - Permintaan: ' + context.raw;
                        }
                    }
                },
                afterDraw: function(chart) {
                    var ctx = chart.ctx;
                    var chartArea = chart.chartArea;
                    
                    topUsers.forEach((user, index) => {
                        var meta = chart.getDatasetMeta(0);
                        var dataPoint = meta.data[index];
                        var x = dataPoint.x - dataPoint.width / 2; // Posisi x gambar
                        var y = dataPoint.y - 50; // Adjust y to position image correctly above the bar
    
                        var img = new Image();
                        img.src = user.foto;
                        img.onload = function() {
                            var imgWidth = 50; // Lebar gambar
                            var imgHeight = 50; // Tinggi gambar
                            ctx.save();
                            ctx.drawImage(img, x, y, imgWidth, imgHeight);
                            ctx.restore();
                        };
                    });
                }
            }
        }
    });
});

</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data pengguna
        var topUsers = [
            { name: 'User 1', requests: 25, img: 'https://via.placeholder.com/1080x1920?text=User+1' },
            { name: 'User 2', requests: 30, img: 'https://via.placeholder.com/1080x1920?text=User+2' },
            { name: 'User 3', requests: 22, img: 'https://via.placeholder.com/1080x1920?text=User+3' }
        ];

        var labels = topUsers.map(user => user.name);
        var data = topUsers.map(user => user.requests);
        var images = topUsers.map(user => user.img);

        var ctxUser = document.getElementById('myChartUser').getContext('2d');

        var myChartUser = new Chart(ctxUser, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Permintaan',
                    data: data,
                    backgroundColor: 'rgba(0,0,0,0)' // Tidak digunakan
                }]
            },
            options: {
                indexAxis: 'x',
                scales: {
                    x: {
                        display: true
                        
                    },
                    y: {
                        beginAtZero: true
                         // Tampilkan label y
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ' - Permintaan: ' + context.raw;
                            }
                        }
                    }
                },
                animation: {
                    onComplete: function() {
                        var chart = this.chart;
                        var ctx = chart.ctx;
                        var chartArea = chart.chartArea;

                        // Draw images as bars
                        topUsers.forEach((user, index) => {
                            var x = chart.getDatasetMeta(0).data[index].x;
                            var y = chart.getDatasetMeta(0).data[index].y;
                            var width = chart.getDatasetMeta(0).data[index].width;
                            var height = chart.getDatasetMeta(0).data[index].height;

                            var img = new Image();
                            img.src = user.img;
                            img.onload = function() {
                                ctx.save();
                                ctx.translate(x - width / 2, y - height / 2); // Adjust y to center image
                                ctx.drawImage(img, 0, 0, width, height);
                                ctx.restore();
                            };
                        });
                    }
                }
            }
        });
    });
</script>
@endsection