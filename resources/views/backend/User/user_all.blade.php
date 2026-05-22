@extends('admin.admin_master')
@section('admin')

<head>
    <title>
        Semua Pengguna | SIMUTAN
    </title>
</head>

<style>
/* Mengatur elemen pencarian dan dropdown filter */
#datatable_filter {
    display: flex;
    align-items: center;
    gap: 10px;
}

.dataTables_filter {
    margin-left: 10px;
    display: flex;
    align-items: center;
    justify-content: end;
}

#roleFilter {
    width: 20%; /* Atur lebar dropdown sesuai kebutuhan */
}

/* Atur kotak pencarian agar berada di sebelah kanan dropdown filter */
.dataTables_filter input {
    margin-left: 10px; /* Memberi jarak antara dropdown dan kotak pencarian */
}

/* Styling untuk tombol ekspor */
.dt-button-collection .dropdown-toggle {
    display: block !important;
    width: 100% !important;
    padding: .47rem 1.75rem .47rem .75rem !important;
    -moz-padding-start: calc(.75rem - 3px) !important;
    font-size: .9rem !important;
    font-weight: 500 !important;
    line-height: 1.5 !important;
    color: #505d69 !important;
    background-color: #fff !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%230a1832' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") !important;
    background-repeat: no-repeat !important;
    background-position: right .75rem center !important;
    background-size: 16px 12px !important;
    border: 1px solid #ced4da !important;
    border-radius: .25rem !important;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out !important;
    appearance: none !important;
}

.dt-button-collection .dropdown-menu {
    border: 1px solid #ced4da !important;
    border-radius: .25rem !important;
    box-shadow: 0 0 0 .15rem rgba(15,156,243,.25) !important;
    background-color: #fff !important;
}

.dt-button-collection .dropdown-item {
    color: #505d69 !important;
    padding: .5rem 1rem !important;
    text-decoration: none !important;
}

.dt-button-collection .dropdown-item:hover {
    background-color: #f8f9fa !important;
}

.dt-button-collection .dropdown-item:focus {
    outline: none !important;
    background-color: #e9ecef !important;
}

.filter-buttons {
        display: flex;
        align-items: center;
    }
    
    .filter-buttons .form-select-sm {
        min-width: 180px;
        margin-right: 10px; /* Adjust as needed */
    }
    
    .datatable-search {
        flex-grow: 1; /* Ensure it takes the remaining space */
    }
    
    a[data-tooltip] {
        position: relative;
    }
    
    a[data-tooltip]::before {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%; /* Tooltip berada di atas elemen */
        left: 50%;
        transform: translateX(-50%);
        background-color: #333;
        color: #fff;
        padding: 5px 10px;
        border-radius: 5px;
        white-space: nowrap;
        opacity: 0;
        transition: opacity 0.1s ease-in-out; /* Percepat transisi menjadi 0.1s */
        pointer-events: none;
        font-size: 12px;
        z-index: 999;
    }
    
    a[data-tooltip]:hover::before {
        opacity: 1;
    }
    
    .hover\:bg-primary:hover {
        background-color: #e3f0fb!important; /* Sesuaikan dengan warna bg-primary */
        color: #007bff !important;
    }
    
    .hover\:bg-success:hover {
        background-color: #e2f6e7 !important; /* Sesuaikan dengan warna bg-success */
        color: #28a745 !important;
    }
    
    .hover\:bg-danger:hover {
        background-color: #feeaea !important; /* Sesuaikan dengan warna bg-danger */
        color: #dc3545 !important;
    }

    .hover\:bg-warning:hover {
        --bs-bg-opacity:0.15;
        background-color:rgba(var(--bs-warning-rgb),var(--bs-bg-opacity))!important
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 text-info">List Pengguna</h4>
                
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Pengguna</a></li>
                            <li class="breadcrumb-item active">Seluruh Pengguna</li>
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
                            <h3 class="card-title mb-0">Seluruh Pengguna</h3>
                            <a href="{{ route('user.add') }}" class="btn btn-info waves-effect waves-light ml-3">
                                <i class="mdi mdi-plus-circle"></i> Tambah Pengguna
                            </a>
                        </div>
                                               
                        <table id="datatable" class="table table-bordered yajra-datatable" 
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Nama Pengguna</th>
                                    <th>Role</th>
                                    <th>Username</th>
                                    <th width="1%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan diisi oleh DataTables via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript libraries for DataTables -->
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
                url: "{{ route('user.all') }}",
                data: function (d) {
                    d.role = $('#roleFilter').val(); // Mengirimkan data filter ke server
                }
            },
            columns: [
                {data: 'name', name: 'name'},
                {
                    data: 'role',
                    name: 'role',
                    render: function(data, type, row) {
                        // Mengubah format role menjadi huruf kapital di bagian depan
                        return data.charAt(0).toUpperCase() + data.slice(1);
                    }
                },
                {data: 'username', name: 'username'},
                {
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false,
                    className: 'no-export', // Tambahkan kelas khusus pada kolom aksi
                    render: function(data, type, row) {
                        var editUrl = "{{ route('user.edit', ':id') }}".replace(':id', row.id);
                        var deleteUrl = "{{ route('user.delete', ':id') }}".replace(':id', row.id);
    
                        return `
                        <div class="table-actions d-flex" style="text-align: center; vertical-align: middle;">
                            <a href="${editUrl}" class="btn btn-sm hover:bg-warning" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #e1a017; padding: 15px;" data-tooltip="Edit Pegawai">
                                <i class="ti ti-edit font-size-20 align-middle"></i>
                            </a>
                            <a href="${deleteUrl}" class="btn btn-sm text-danger hover:bg-danger" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: red; padding: 15px;" data-tooltip="Hapus Pegawai">
                                <i class="ti ti-trash font-size-20 align-middle text-danger"></i>
                            </a>
                        </div>
                        `;
                    }
                },
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
                var select = $('<select id="roleFilter" class="form-select-sm" style="width: 24%; border: 1px solid #1156bf; color:#043277; font-weight: 500"><option value="">Semua Role</option></select>')
                    .appendTo($('#datatable_filter').css('display', 'flex').css('align-items', 'center').css('gap', '10px')) // Tambahkan dropdown ke sebelah search box
                    .on('change', function() {
                        table.draw();
                    });
    
                @foreach($roles as $role)
                    select.append('<option value="{{ $role->role }}">{{ ucfirst($role->role) }}</option>');
                @endforeach
    
                $('.form-select').each(function() {
                    $(this).css({
                        'display': 'block',
                        // 'width': '100%',
                        'padding': '.47rem 1.75rem .47rem .75rem',
                        '-moz-padding-start': 'calc(.75rem - 3px)',
                        'font-size': '.9rem',
                        'font-weight': '500',
                        'line-height': '1.5',
                        'color': '#505d69',
                        'background-color': '#fff',
                        'background-image': 'url("data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 16 16\'%3e%3cpath fill=\'none\' stroke=\'%230a1832\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M2 5l6 6 6-6\'/%3e%3c/svg%3e")',
                        'background-repeat': 'no-repeat',
                        'background-position': 'right .75rem center',
                        'background-size': '16px 12px',
                        'border': '1px solid #ced4da',
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
                    'border-radius': '.25rem',
                    'border': '1px solid #1156bf',
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
        
        // Event listener for filtering
        $('#roleFilter').change(function () {
            table.draw();
        });
    });
</script>

    
@endsection
