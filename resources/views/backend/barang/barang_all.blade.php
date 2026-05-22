@extends(auth()->user()->role === 'admin' ? 'admin.admin_master' : (auth()->user()->role === 'supervisor' ? 'supervisor.supervisor_master' : 'pegawai.pegawai_master'))
@section(auth()->user()->role === 'admin' ? 'admin' : (auth()->user()->role === 'supervisor' ? 'supervisor' :
    'pegawai'))

    <head>
        <title>
            Persediaan Barang | SIMUTAN
        </title>
    </head>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .table-actions {
            display: inline-flex;
            gap: 5px;
            justify-content: center;
            align-items: center;
        }

        #datatable_filter {
            justify-content: end;
        }

        .dropdown-menu {
            background-color: white;
            border: 1px solid #ccc;
            padding: 10px;
            z-index: 1000;
            position: absolute;
        }

        .form-control {
            display: block;
            width: 100%;
        }
    </style>

    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 text-info">List Persediaan Barang</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Barang</a></li>
                                <li class="breadcrumb-item active">Persediaan Barang</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h3 class="card-title mb-0">Persediaan Barang</h3>
                                @if (Auth::user()->role == 'admin')
                                    <a href="{{ route('barang.add') }}" class="btn btn-info waves-effect waves-light ml-3">
                                        <i class="mdi mdi-plus-circle"></i> Tambah Barang
                                    </a>
                                @endif
                            </div>

                            <table id="datatable" class="table table-bordered yajra-datatable"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th width="10%" class="text-center">Kode</th>
                                        <th width="20%">Kelompok Barang</th>
                                        <th width="20%">Kategori Barang</th>
                                        <th>Nama Barang</th>
                                        <th width="1%" class="text-center">Stok</th>
                                        <th width="1%" class="text-center">Satuan</th>
                                        <th width="1%" class="text-center">Harga Total</th>

                                        @if (Auth::user()->role === 'admin')
                                            <th width="1%" class="text-center">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStockModalLabel">Tambah Stok Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addStockForm">
                        <input type="hidden" name="barang_id" id="modal_barang_id">
                        <input type="hidden" name="harga_total_stok_baru" id="harga_total_stok_baru_hidden">
                        <div class="mb-3">
                            <label for="harga_total_lama_display" class="form-label">Harga Total Lama</label>
                            <input type="text" class="form-control" id="harga_total_lama_display" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="stok_qty" class="form-label">Tambah Stok</label>
                            <input type="number" class="form-control" id="stok_qty" min="1" step="1"
                                value="1">
                        </div>
                        <div class="mb-3">
                            <label for="harga_per_unit_display" class="form-label">Harga Beli Per Unit</label>
                            <input type="text" class="form-control" name="harga_per_unit_display"
                                id="harga_per_unit_display" value="0">
                        </div>
                        <div class="mb-3">
                            <label for="harga_total_akhir_display" class="form-label">Harga Total Akhir</label>
                            <input type="text" class="form-control" name="harga_total_akhir_display"
                                id="harga_total_akhir_display" disabled>
                        </div>
                        <button type="submit" class="btn btn-info">Tambah Stok</button>
                    </form>
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
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        var userRole = "{{ auth()->user()->role }}";
    </script>

    <script>
        $(document).ready(function() {
            // =================================================================
            // 1. FUNGSI GLOBAL DAN VARIABEL
            // =================================================================
            let totalHargaLama = 0;

            function formatNumber(angka) {
                if (angka === null || isNaN(angka)) return '0';
                let numberString = (String(angka)).replace(/\D/g, '');
                if (numberString.length === 0) return '0';
                return numberString.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function cleanNumber(value) {
                return parseInt(String(value).replace(/[.,]/g, '')) || 0;
            }

            function hitungHargaTotal() {
                const qtyBaru = cleanNumber($('#stok_qty').val());
                const hargaUnitBersih = cleanNumber($('#harga_per_unit_display').val());
                const hargaTotalBaru = qtyBaru * hargaUnitBersih;
                const hargaTotalAkhir = totalHargaLama + hargaTotalBaru;
                $('#harga_total_akhir_display').val(formatNumber(hargaTotalAkhir));
                $('#harga_total_stok_baru_hidden').val(hargaTotalBaru);
                const currentUnitValue = $('#harga_per_unit_display').val();
                $('#harga_per_unit_display').val(formatNumber(cleanNumber(currentUnitValue)));
            }

            // =================================================================
            // 2. LOGIKA DATATABLES
            // =================================================================
            var columns = [{
                    data: 'kode',
                    name: 'kode',
                    className: 'text-center'
                },
                {
                    data: 'kelompok_barang',
                    name: 'kelompok_barang'
                },
                {
                    data: 'kategori_barang',
                    name: 'kategori_barang'
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'qty_item',
                    name: 'qty_item',
                    className: 'text-center'
                },
                {
                    data: 'satuan',
                    name: 'satuan',
                    className: 'text-center'
                },
                {
                    data: 'harga_total',
                    name: 'harga_total',
                    className: 'text-center'
                },
            ];

            if (userRole === 'admin') {
                columns.push({
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                });
            }

            var table = $('.yajra-datatable').DataTable({
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('barang.allAct') }}",
                    data: function(d) {
                        d.kelompok_id = $('#kelompok_filter').val();
                    }
                },
                columns: columns,
                dom: '<"d-flex justify-content-between align-items-center"<"#exportDropdownContainer">f>rtip',
                initComplete: function() {
                    var kelompokSelect = $(
                            '<select id="kelompok_filter" class="form-select form-select-sm" style="width: 250px;"><option value="">Semua Kelompok Barang</option></select>'
                            )
                        .appendTo($('#datatable_filter').css('display', 'flex').css('align-items',
                            'center').css('gap', '10px'))
                        .on('change', function() {
                            table.draw();
                        });

                    @foreach ($kelompokFilt as $kelompok)
                        kelompokSelect.append(
                            '<option value="{{ $kelompok->id }}">{{ $kelompok->nama }}</option>');
                    @endforeach

                    if (userRole === 'admin') {
                        $('#exportDropdownContainer').html(`
                            <div class="d-flex justify-content-between">
                                <div class="me-2">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            BA Stock Opname <i class="ti ti-download font-size-14"></i>
                                        </button>
                                        <div class="dropdown-menu p-3" aria-labelledby="dropdownMenuButton">
                                            <form action="{{ route('barang.export') }}" method="GET">
                                                <div class="mb-3"><label for="stockOpnameDate" class="form-label" style="font-size: .7875rem">Pilih Tanggal:</label><input type="date" class="form-control form-control-sm" name="tanggal" required></div>
                                                <button type="submit" class="btn btn-sm btn-info">Export</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                            Laporan Rincian Persediaan <i class="ti ti-download font-size-14"></i>
                                        </button>
                                        <div class="dropdown-menu p-3" aria-labelledby="dropdownMenuButton2">
                                            <form action="{{ route('barang.pemasukan.export') }}" method="GET">
                                                <div class="mb-3"><label for="startDate" class="form-label" style="font-size: .7875rem">Tanggal Mulai:</label><input type="date" class="form-control form-control-sm" id="startDate" name="start_date" required></div>
                                                <div class="mb-3"><label for="endDate" class="form-label" style="font-size: .7875rem">Tanggal Akhir:</label><input type="date" class="form-control form-control-sm" id="endDate" name="end_date" required></div>
                                                <button type="submit" class="btn btn-sm btn-info">Export</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);

                        $('#startDate').on('change', function() {
                            $('#endDate').attr('min', $(this).val());
                        });
                    }
                }
            });

            // =================================================================
            // 3. EVENT HANDLERS
            // =================================================================

            $('#datatable').on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data barang ini akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire('Dihapus!', response.message, 'success');
                                table.ajax.reload();
                            },
                            error: function() {
                                Swal.fire('Error!', 'Data barang gagal dihapus.',
                                    'error');
                            }
                        });
                    }
                });
            });

            $('#datatable').on('click', '.add-stock-btn', function() {
                var barangId = $(this).data('id');
                var barangNama = $(this).data('nama');
                totalHargaLama = $(this).data('harga-total');

                $('#modal_barang_id').val(barangId);
                $('#addStockModalLabel').text('Tambah Stok Barang: ' + barangNama);
                $('#harga_total_lama_display').val(formatNumber(totalHargaLama));

                hitungHargaTotal();
            });

            $('#stok_qty, #harga_per_unit_display').on('input', hitungHargaTotal);

            $('#addStockForm').on('submit', function(e) {
                e.preventDefault();
                var formData = {
                    _token: '{{ csrf_token() }}',
                    barang_id: $('#modal_barang_id').val(),
                    qty: $('#stok_qty').val(),
                    harga_total_stok_baru: $('#harga_total_stok_baru_hidden').val()
                };

                $.ajax({
                    url: '{{ route('barang.addStock') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#addStockModal').modal('hide');
                        toastr.success(response.success, 'Berhasil');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            toastr.error('Data yang dimasukkan tidak valid.', 'Error Validasi');
                        } else {
                            toastr.error('Gagal menambahkan stok. Coba lagi.', 'Error Sistem');
                        }
                    }
                });
            });

            $('#addStockModal').on('hidden.bs.modal', function() {
                $('#addStockForm')[0].reset();
                $('#harga_per_unit_display').val('0');
                $('#stok_qty').val('1');
                $('#harga_total_akhir_display').val('');
                $('#harga_total_lama_display').val('');
            });
        });
    </script>
@endsection