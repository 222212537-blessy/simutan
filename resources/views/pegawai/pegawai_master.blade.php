<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    {{-- <title>SIMUTAN</title> --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesdesign" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.20.0/tabler-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

    <!-- DataTables CSS -->
    <link href="{{ asset('backend/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    {{-- <link rel="stylesheet" href="{{ asset('css/buttons.dataTables.min.css') }}"> --}}

    <!-- Bootstrap CSS -->
    <link href="{{ asset('backend/assets/css/bootstrap.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons CSS -->
    <link href="{{ asset('backend/assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
    <!-- App CSS -->
    <link href="{{ asset('backend/assets/css/app.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    <link rel="stylesheet" href="{{ asset('vendor/libs/bs-stepper/bs-stepper.css') }}" />

    <style>
        .table-wrapper {
            overflow-x: auto;
        }
        table.dataTable {
            border-collapse: collapse !important;
            width: 100%;
        }
        
        /* Spinner Styles */
        #loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2); /* Semi-transparent background */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999; /* Ensure spinner is above all content */
        }

        .spinner {
            animation: spin 1s linear infinite;
        }

        .spinner img {
            width: 100px; /* Adjust size as needed */
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .pulsing-dot {
        width: 50px;
        height: 50px;
        /* background: #3498db; */
        border-radius: 50%;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.5); opacity: 0.5; }
        100% { transform: scale(1); opacity: 1; }
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

.card {
    opacity: 0; transition: opacity 0.5s ease-in-out;
}
    
    </style>
</head>

<body data-topbar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('pegawai.body.header')

        <!-- Left Sidebar -->
        @include('pegawai.body.sidebar')
        <!-- End Left Sidebar -->

        <!-- Main Content -->
        <div class="main-content">
            @yield('pegawai')
            @include('pegawai.body.footer')
        </div>
        <!-- End Main Content -->

    </div>
    <!-- End layout-wrapper -->

    <!-- Right Sidebar -->
    <!-- /Right-bar -->

    <!-- Right bar overlay -->
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('backend/assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/node-waves/waves.min.js') }}"></script>

    {{-- <script src="{{ asset('backend/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    
    <!-- Responsive examples -->
    <script src="{{ asset('backend/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

         <!-- Required datatable js -->
         <script src="{{ asset('backend/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
         <script src="{{ asset('backend/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    
         <!-- Datatable init js -->
         <script src="{{ asset('backend/assets/js/pages/datatables.init.js') }}"></script> --}}

    <!-- ApexCharts -->
    <script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- jQuery Vector Map -->
    <script src="{{ asset('backend/assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/admin-resources/jquery.vectormap/maps/jquery-jvectormap-us-merc-en.js') }}"></script>

    <!-- DataTables JS -->
    <script src="{{ asset('backend/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script src="{{ asset('backend/assets/js/pages/dashboard.init.js') }}"></script>
    <script src="{{ asset('backend/assets/js/app.js') }}"></script>

    <!-- Handle Bars JS -->
    <script src="{{ asset('backend/assets/js/handlebars.js') }}"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        @if(Session::has('message'))
        var type = "{{ Session::get('alert-type', 'info') }}";
        switch(type){
            case 'info':
                toastr.info("{{ Session::get('message') }}");
                break;
            case 'success':
                toastr.success("{{ Session::get('message') }}");
                break;
            case 'warning':
                toastr.warning("{{ Session::get('message') }}");
                break;
            case 'error':
                toastr.error("{{ Session::get('message') }}");
                break; 
        }
        @endif
    </script>

    <!-- Notify JS (Optional) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/notify.js/2.0.0/notify.min.js" integrity="sha512-iy8/ErLJUuqWbu30yUSCxXtE3FCDZi3y5op0Duqdp7vtpeh1E6ZyAPnRS+OrJHddh4uP30oYpwNt7TXPbmP5lQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setelah 2 detik, sembunyikan spinner dan tampilkan konten utama
            setTimeout(function() {
                document.getElementById('loading').style.opacity = '0'; // Mulai transisi menghilang
                setTimeout(function() {
                    document.getElementById('loading').style.display = 'none'; // Sembunyikan setelah transisi selesai
                    document.querySelector('#layout-wrapper').style.display = 'block'; // Tampilkan konten utama
                }, 500); // Durasi transisi fade-out, bisa disesuaikan jika diperlukan
            }, 2000); // Tampilkan spinner selama 2 detik
        });
        
            </script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    const cards = document.querySelectorAll('.card');

    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = 1;
        }, index * 100);
    });
});

</script>

</body>

</html>
