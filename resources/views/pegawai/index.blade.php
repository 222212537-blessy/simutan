@extends(auth()->user()->role === 'admin' ? 'admin.admin_master' : 
         (auth()->user()->role === 'supervisor' ? 'supervisor.supervisor_master' : 
         'pegawai.pegawai_master'))

@section(auth()->user()->role === 'admin' ? 'admin' : 
         (auth()->user()->role === 'supervisor' ? 'supervisor' : 'pegawai'))

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/handlebars@4.7.7/dist/handlebars.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<head>
    <title>
        Dashboard | SIMUTAN
    </title>
</head>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<style>
    .swiper-horizontal .swiper-pagination-bullets,
.swiper-pagination-bullets.swiper-pagination-horizontal,
.swiper-pagination-custom,
.swiper-pagination-fraction {
  bottom: -15px; /* Ganti dengan nilai yang diinginkan */
}

    .swiper-button-next, .swiper-button-prev {
    color: #043277; /* Customize navigation button color */
    font-weight:bold;
    --swiper-navigation-size: 20px;
    top: 50%; /* Center vertically */
    transform: translateY(-50%);
    z-index: 10; /* Ensure buttons are above cards */
    width: 30px; /* Adjust button width */
    height: 30px; /* Adjust button height */
    background-color: rgba(255, 255, 255, 0.8); /* Add semi-transparent background */
    border-radius: 50%; /* Make buttons round */
    display: flex;
    justify-content: center;
    align-items: center;
    position: absolute; /* Position them absolutely */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Add shadow */
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

.swiper-pagination{
    position: relative;
}

.swiper-button-next {
    right: 10px; /* Adjust right button distance */
}

.swiper-button-prev {
    left: 10px; /* Adjust left button distance */
}

.swiper-container {
    width: calc(100% - 7rem);
    height: fit-content; /* Adjust slider height */
    position: relative;
    overflow: hidden; /* Ensure no elements overflow */
    margin: 0 auto; /* Center the container */
    padding-bottom: 20px;

}

.swiper-slide {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 10px;
}

.card-slider {
    width: 180px; /* Adjust card width */
    height: 240px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Add shadow */
    border-radius: 8px; /* Rounded corners */
    overflow: hidden;
    background: #fff; /* Background color */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    z-index: 5;
}

.card-slider:hover {
    transform: scale(1.05); /* Zoom effect on hover */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
}

.card-slider-body {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-align: center;
    height: 105px;
}

.card-slider-img-top {
    width: 100%;
    height: 95px;
    /* height: 120px; */
    object-fit: cover;
    /* padding: 15px 15px 0 15px; */
    border: 1px solid black;
}



.card-slider-title {
    font-size: 14px;
    margin: 10px 0;
}

.card-slider-text {
    font-size: 12px;
    margin-bottom: 0px;
}

.swiper-pagination-bullet {
    background: white; /* Customize pagination bullet color */
}

.swiper-pagination-bullet-active {
    background: #fff; /* Active bullet color */
}

.swiper-container-wrapper {
        position: relative;
        display: none; /* Hide all Swiper containers by default */
        z-index: 10;
    }
    .swiper-container-wrapper.active {
        position: relative;
        display: block; /* Show the active Swiper container */
    }

    .kelompok-button {
        cursor: pointer;
        padding: 10px 20px;
        margin: 0 5px 0 0 ;
        background-color: rgba(4, 127, 209, 0.3);
        color: #fff;
        font-weight: bold;
        border: none;
        border-radius: 5px 5px 0 0;
        font-size: 16px;
    }

    .kelompok-button.active {
        background-color: #043277;
    }

    .col-md-6 {
        margin-bottom: 1.5rem; /* Space between columns */
    }

    canvas {
        width: 100% !important; /* Ensure canvas fits its container */
        height: auto !important; /* Adjust height based on width */
    }

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

    .kelompok-button {
    opacity: 0;
    transition: opacity 0.5s ease-in-out; /* Waktu transisi 0.5s, bisa disesuaikan */
}

.kelompok-button.show {
    opacity: 1;
}


    .large-circle2 {
        width: 180px;
        height: 180px;
        top: 0;
        right: 0;
    }

    .small-circle2 {
        width: 160px;
        height: 160px;
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

    .swiper-button-prev:after, .swiper-rtl .swiper-button-next:after {
        font-size: 14px;
        font-weight: 600;
    }

    .swiper-button-next:after, .swiper-rtl .swiper-button-prev:after {
        font-size: 14px;
        font-weight: 600;
    }

    .card, .gradient-background2{
        border-radius: 0.375rem;
        overflow:hidden;
    }
    .gradient-background::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0, 123, 255, 1), rgba(0, 123, 255, 0) 100%);
        z-index: 1;
        pointer-events: none;
        border-radius: 0.375rem;
    }

    .gradient-background2::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: conic-gradient(from 90deg at bottom right, rgba(4, 127, 209, 0.6), #043277);
        z-index: 1;
        pointer-events: none;
        border-radius: 0.375rem;
    }

    #datatable_filter {
    justify-content: end;
}

    .gradient-background3::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(65,130,255,1) 0%, rgba(181,212,248,1) 100%);
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

    .card, .gradient-background, .gradient-background2, .gradient-background3 {
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

.swiper-wrapper {
    z-index: 10;
}

.swiper-container-wrapper,
    .kelompok-definisi {
        display: none;
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
        position: relative; /* Tambahkan untuk menghindari pergeseran vertikal */
    }

    .swiper-container-wrapper.active,
    .kelompok-definisi.active {
        display: block;
        opacity: 1;
    }

    .table-actions {
        display: inline-flex;
        gap: 5px; /* Jarak antar tombol */
        justify-content: center;
        align-items: center;
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

        <!-- start page title -->
        {{-- <div class="row">
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
        </div> --}}
        <!-- end page title -->

        <div class="card">
            <div class="card-body">
                <h3 class="text-info mb-3" style="font-weight: 700">Halo {{ $user->panggilan }}, Selamat Datang di SIMUTAN!</h3>
                <div>
                    <p style="font-size: 16px; margin-bottom:0.5rem;">Sistem Mutasi Persediaan (SIMUTAN) adalah sistem yang digunakan untuk mengetahui jumlah dan mutasi barang serta menilai dari selisih kelebihan atau kekurangan barang</p>
                </div>
            </div>
        </div>

        <div>
            @foreach($kelompoks as $index => $kelompok)
            <button class="kelompok-button {{ $kelompok->id === $kelompokWithMostBarangs->id ? 'active' : '' }} fw-bold" data-target="#swiper-container-{{ $index }}" data-definisi="#definisi-{{ $index }}">
                {{ $kelompok->nama }}
            </button>
            @endforeach
        </div>
        <div class="card gradient-background2 mb-4" style="border-radius: 0 0.25rem 0.25rem 0.25rem; overflow: hidden; margin: 0; padding: 0; position: relative;">
            <!-- SVG Wave -->
            <div class="svg-wave" style="position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: 1;">
                <svg viewBox="0 0 500 200" preserveAspectRatio="none">
                    <path d="M0,80 C120,130 180,30 280,90 C400,160 500,50 500,50 L500,0 L0,0 Z" fill="#043277" opacity="0.7"></path> <!-- Warna Ungu -->
                    <path d="M0,100 C150,150 250,20 350,110 C450,200 500,60 500,60 L500,0 L0,0 Z" fill="#043277" opacity="0.7"></path> <!-- Warna Biru -->
                    <path d="M0,120 C180,170 300,50 400,130 C480,200 500,70 500,70 L500,0 L0,0 Z" fill="#043277" opacity="0.5"></path> <!-- Warna Biru Muda -->
                </svg> 
            </div>
        
            <div class="row gx-4" style="position: relative; z-index: 2;">
                <!-- Chart for Top Score Barang Diminta (Kolom Kiri) -->
                <div style="flex:0 0 auto; width:40%">
                    <div class="card mb-0" style="background-color: rgba(4, 50, 119, 0); box-shadow:none">
                        <div class="card-body">
                            @foreach($kelompoks as $index => $kelompok)
    <div class="kelompok-definisi {{ $kelompok->id === $kelompokWithMostBarangs->id ? 'active' : '' }}" id="definisi-{{ $index }}" style="display: {{ $kelompok->id === $kelompokWithMostBarangs->id ? 'block' : 'none' }};">
        <h4 class="mb-2" style="color: white;">{{ $kelompok->nama }}</h4>
        <p style="color: white; font-size: 18px; font-weight:400">
            {{ $kelompok->deskripsi ?? 'Definisi untuk kelompok ini belum tersedia.' }}
        </p>
    </div>
@endforeach

                        </div>
                    </div>
                </div>
        
                <!-- Sliders for Each Kelompok Barang (Kolom Kanan) -->
                <div style="flex:0 0 auto; width:60%">
                    <div class="card mb-0" style="background-color: rgba(4, 50, 119, 0); box-shadow:none">
                        <div class="card-body">
                            @foreach($kelompoks as $index => $kelompok)
                                <div class="swiper-container-wrapper {{ $kelompok->id === $kelompokWithMostBarangs->id ? 'active' : '' }}" id="swiper-container-{{ $index }}">
                                    <div class="swiper-container">
                                        <div class="swiper-wrapper">
                                            @foreach($kelompok->barangs as $item)
                                                <div class="swiper-slide">
                                                    <div class="card-slider" style="padding: 15px">
                                                        @if($item->foto_barang)
                                                        <img src="{{asset($item->foto_barang) }}" class="card-slider-img-top" alt="Gambar Barang">
                                                        @else
                                                        <img src="http://127.0.0.1:8000/upload/no_image.jpg" class="card-slider-img-top" alt="No Image">
                                                    @endif
                                                        <div class="card-slider-body">
                                                            <h5 class="card-slider-title">{{ $item->nama }}</h5>
                                                            <p class="card-slider-text">Stok: {{ $item->qty_item }} {{ $item->satuan ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
        
                                        <!-- Add Pagination -->
                                        <div class="swiper-pagination"></div>
                                        
                                    </div>
                                    <!-- Add Navigation -->
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>
                                    
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gx-4">
                <div class="col-xl-3 col-md-6 mb-0">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">Total Permintaan<br>Diajukan</h5>
                                    <h6 class="mb-0 text-muted small">Bulan Ini</h6>
                                    <h4 class="mb-2 mt-2">{{ $totalPermintaanBulanIni ?? 'Tidak ada data' }}</h4>
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
                <div class="col-xl-3 col-md-6 mb-0">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">Total Permintaan<br>Selesai</h5>
                                    <h6 class="mb-0 text-muted small">Bulan Ini</h6>
                                    <h4 class="mb-2 mt-2">{{ $totalPermintaanSelesai ?? 'Tidak ada data' }}</h4>
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
                <div class="col-xl-3 col-md-6 mb-0">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">Total Permintaan<br>Pending</h5>
                                    <h6 class="mb-0 text-muted small">Bulan Ini</h6>
                                    <h4 class="mb-2 mt-2">{{ $totalPermintaanPending ?? 'Tidak ada data' }}</h4>
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
                <div class="col-xl-3 col-md-6 mb-0">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">Total Permintaan<br>Ditolak</h5>
                                    <h6 class="mb-0 text-muted small">Bulan Ini</h6>
                                    <h4 class="mb-2 mt-2">{{ $totalPermintaanRejected ?? 'Tidak ada data' }}</h4>
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

            <!-- Chart Container -->
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
                                                {{ $user->requests }} requests
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
            
    </div>
</div>

 <!-- Pastikan jQuery sudah di-load -->
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
    document.addEventListener("DOMContentLoaded", function() {
    const buttons = document.querySelectorAll('.kelompok-button');

    buttons.forEach((button, index) => {
        setTimeout(() => {
            button.classList.add('show');
        }, index * 200); // Sesuaikan delay untuk transisi bertahap
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
    // Simpan semua Swiper instances
    var swipers = {};
    
    document.querySelectorAll('.swiper-container-wrapper').forEach(function(wrapper) {
        var container = wrapper.querySelector('.swiper-container');
        var swiperInstance = new Swiper(container, {
            loop: true,
            slidesPerView: 3,
            spaceBetween: 10,
            pagination: {
                el: wrapper.querySelector('.swiper-pagination'),
                dynamicBullets: true,
            },
            navigation: {
                nextEl: wrapper.querySelector('.swiper-button-next'),
                prevEl: wrapper.querySelector('.swiper-button-prev'),
            },
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            breakpoints: {
                640: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
            }
        });
        
        // Simpan instance dalam objek
        swipers[wrapper.id] = swiperInstance;
    });

    // Handle button clicks
    document.querySelectorAll('.kelompok-button').forEach(function(button) {
        button.addEventListener('click', function() {
            var target = this.getAttribute('data-target');
            var definisiTarget = this.getAttribute('data-definisi');

            // Fade out all Swiper containers and definisi
            document.querySelectorAll('.swiper-container-wrapper.active, .kelompok-definisi.active').forEach(function(el) {
                el.classList.remove('active');
                el.style.display = 'none';
            });

            // Show the targeted Swiper container and definisi
            document.querySelector(target).style.display = 'block';
            document.querySelector(target).classList.add('active');
            document.querySelector(definisiTarget).style.display = 'block';
            document.querySelector(definisiTarget).classList.add('active');

            // Mark the clicked button as active
            document.querySelectorAll('.kelompok-button').forEach(function(btn) {
                btn.classList.remove('active');
            });
            this.classList.add('active');
        });
    });

    // Show the Swiper container and definisi with the most barangs by default
    var defaultButton = document.querySelector('.kelompok-button.active');
    if (defaultButton) {
        defaultButton.click();
    }
});

</script>


@endsection