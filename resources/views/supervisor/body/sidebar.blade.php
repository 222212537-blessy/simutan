<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!-- User details -->
    

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">

            <li class="menu-title">Menu</li>
                <li>
                    <a href="{{ route('dashboard') }}" class="waves-effect">
                        <i class="ri-dashboard-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>     
                
                <li>
                    <a href="{{ route('barang.all') }}" class="waves-effect">
                        <i class="ti ti-package font-size-20" style="margin-left: -1.5px"></i>
                        <span>Stok Barang</span>
                    </a>
                </li>
                            
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-mail-send-line"></i>
                        <span>Permintaan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('pilihan.add') }}">Ajukan Permintaan</a></li>
                        <li><a href="{{ route('permintaan.saya') }}">Permintaan Saya</a></li>
                        <li><a href="{{ route('permintaan.all')}}">Semua Permintaan</a></li>
                    </ul>
                </li>

        </div>
    </div>
</div>