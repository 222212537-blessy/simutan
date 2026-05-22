@extends(auth()->user()->role === 'admin' ? 'admin.admin_master' : 'supervisor.supervisor_master')
@section(auth()->user()->role === 'admin' ? 'admin' : 'supervisor')

<head>
    <title>
        Semua Kelompok Barang | SIMUTAN
    </title>
</head>

<style>
    .table-actions {
        display: inline-flex;
        gap: 5px; /* Jarak antar tombol */
        justify-content: center;
        align-items: center;
    }

    </style>


<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
<div class="row">
    <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <h4 class="mb-sm-0 text-info">List Kelompok Barang</h4>
    
        <div class="page-title-right">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Barang</a></li>
                <li class="breadcrumb-item active">List Kelompok Barang</li>
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
                            <h4 class="card-title mb-0">Kelompok Barang</h4>
                            <a href="{{ route('kelompok.add') }}" class="btn btn-info waves-effect waves-light ml-3">
                                <i class="mdi mdi-plus-circle"></i> Tambah Kelompok Barang
                            </a>
                        </div>
                                               
                        <table id="datatable" class="table table-bordered yajra-datatable" 
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            
                        <thead>
                            <tr>
                                <th width="1%">ID</th>
                                <th>Nama</th>
                                <th>Deskripsi</th>
                                <th width="1%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelompoks as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->deskripsi }}</td>
                                    <td class="table-actions" style="text-align: center; vertical-align: middle;">
                                        <div class="table-actions">
                                            <!-- Tombol dengan link route ke halaman edit -->
                                            <a href="{{ route('kelompok.edit', $item->id) }}" class="btn btn-sm hover:bg-warning" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #e1a017; padding: 15px;" data-tooltip="Edit Permintaan">
                                                <i class="ti ti-edit font-size-20 align-middle"></i>
                                            </a>
                                            
                                            <!-- Tombol dengan link route ke halaman delete -->
                                            <a href="{{ route('kelompok.delete', $item->id) }}" class="btn btn-sm text-danger hover:bg-danger" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: red; padding: 15px;" data-tooltip="Hapus Permintaan">
                                                <i class="ti ti-trash font-size-20 align-middle text-danger"></i>
                                            </a>
                                        </div>
                                    </td>       
                                </tr>
                            @endforeach
                        </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function() {
    $('#datatable').DataTable({
        // processing: true,
        serverSide: true,
        ajax: "{{ route('kelompok.data') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'nama', name: 'nama' },
            { data: 'deskripsi', name: 'deskripsi' },
            {
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                className: 'no-export', // Tambahkan kelas khusus pada kolom aksi
                render: function(data, type, row) {
                    var editUrl = "{{ route('kelompok.edit', ':id') }}".replace(':id', row.id);
                    var deleteUrl = "{{ route('kelompok.delete', ':id') }}".replace(':id', row.id);

                    return `
                    <div class="table-actions d-flex" style="text-align: center; vertical-align: middle;">
                        <a href="${editUrl}" class="btn btn-sm hover:bg-warning" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #e1a017; padding: 15px;" data-tooltip="Edit Kelompok Barang">
                            <i class="ti ti-edit font-size-20 align-middle"></i>
                        </a>
                        <a href="${deleteUrl}" class="btn btn-sm text-danger hover:bg-danger" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: red; padding: 15px;" data-tooltip="Hapus Kelompok Barang">
                            <i class="ti ti-trash font-size-20 align-middle text-danger"></i>
                        </a>
                    </div>
                    `;
                }
            },
        ],
        initComplete: function() {
            // Styling untuk select
        

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
                        'gap': '10px',
                        'align-items': 'center',
                        'color': '#043277'
                    });
                });

                $('select[name="datatable_length"]').css({
                    'font-size': '.875rem', // Misalnya, menambahkan ukuran font jika diperlukan
                    'height': '1.67rem',
                    'border': '1px solid rgb(17, 86, 191)',
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
            $('#datatable_length').css('display', 'flex');
            $('#datatable_length').find('label').css('display', 'flex'); 
        });
    });
    </script>
@endsection
