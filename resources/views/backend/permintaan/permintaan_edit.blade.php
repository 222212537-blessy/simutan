@extends(auth()->user()->role === 'admin' ? 'admin.admin_master' : (auth()->user()->role === 'supervisor' ? 'supervisor.supervisor_master' : 'pegawai.pegawai_master'))

@section(auth()->user()->role === 'admin' ? 'admin' : (auth()->user()->role === 'supervisor' ? 'supervisor' :
    'pegawai'))

    <head>
        <title>Edit Permintaan | SIMUTAN</title>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        
    </head>

    <style>
        /* Styling umum untuk search dan suggestions */
        #barang_search::placeholder {
            text-align: left;
            color: #6c757d;
        }

        #barang_suggestions {
            z-index: 1050;
            /* Pastikan suggestions tampil di atas elemen lain */
        }

        #barang_suggestions li {
            cursor: pointer;
        }

        #barang_suggestions li:hover {
            background-color: #f0f8ff;
        }

        #barang_search:disabled {
            background-color: #e9ecef;
            cursor: not-allowed;
        }

        /* Styling untuk Step Indicator (tidak berubah) */
        .step-indicator {
            display: flex;
            margin-bottom: 20px;
        }

        .step-indicator .step {
            display: flex;
            align-items: center;
            cursor: pointer;
            position: relative;
        }

        .step-indicator .step .circle {
            width: 40px;
            height: 40px;
            background-color: #e9ecef;
            border-radius: 0.25rem;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            color: #000;
        }

        .step-indicator .step.active .circle {
            background-color: #043277;
            color: white;
        }

        .step .label {
            font-size: 1.125rem;
            color: #6c757d;
        }

        .step.active .label {
            color: black;
        }

        .catalog-container {
            max-height: 640px;
            overflow-y: auto;
            padding: 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            background: #fff;
        }
        .product-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: #fff;
            overflow: hidden;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .product-img-wrapper {
            height: 140px;
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 10px;
            border-bottom: 1px solid #f1f5f9;
        }
        .product-img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }
        .cart-sidebar {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
            position: sticky;
            top: 20px;
        }
        .cart-item {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .cart-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }
    </style>

    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items: center justify-content-between">
                        <h4 class="mb-sm-0 text-info">Edit Permintaan</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Permintaan</a></li>
                                <li class="breadcrumb-item active">Edit Permintaan</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div id="wizard">
                                <div class="step-indicator mb-4">
                                    <div class="step active" data-step="1">
                                        <div class="circle">1</div>
                                        <div class="label ms-2 fw-bold">Informasi Permintaan</div>
                                    </div>
                                    <div class="mx-3 d-flex align-items-center">
                                        <i class="mdi mdi-chevron-right" style="font-size: 30px;"></i>
                                    </div>
                                    <div class="step" data-step="2">
                                        <div class="circle">2</div>
                                        <div class="label ms-2">Detail Barang Permintaan</div>
                                    </div>
                                </div>

                                <div id="step1" class="step-content">
                                    <hr class="mt-0">
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="name" class="form-label text-info">Nama Pegawai</label>
                                                <input type="text" class="form-control" id="name"
                                                    value="{{ $pilihan->first()->created_by ?? auth()->user()->name }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="date" class="form-label text-info">Tanggal
                                                    Permintaan</label>
                                                <input class="form-control" name="date" type="date" id="date"
                                                    value="{{ $pilihan->first()->date ?? \Carbon\Carbon::now()->format('Y-m-d') }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mb-3">
                                                <label for="textarea" class="form-label mb-1 text-info">Catatan</label>
                                                <textarea id="textarea" name="description" class="form-control" maxlength="225" rows="3" placeholder="Penjelasan. (Maksimal 225 Karakter)">{{ $pilihan->first()->description ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="warning_message" class="alert alert-danger" style="display: none;">
                                        Semua kolom harus diisi. Harap isi tanggal dan catatan sebelum melanjutkan.
                                    </div>
                                    <div class="mt-4 d-flex justify-content-end">
                                        <button type="button" class="btn btn-info" id="next_btn_step1">Lanjut <i
                                                class="mdi mdi-arrow-right ms-1"></i></button>
                                    </div>
                                </div>

                                <div id="step2" class="step-content" style="display: none;">
                                    <hr class="mt-0">
                                    <div class="row mb-4 g-3">
                                        <div class="col-md-3">
                                            <label for="filter_kelompok" class="form-label fw-bold">1. Kelompok Barang</label>
                                            <select class="form-select" id="filter_kelompok">
                                                <option value="all">Semua Kelompok</option>
                                                @foreach($kelompok as $kel)
                                                    <option value="{{ $kel->id }}">{{ $kel->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="filter_kategori" class="form-label fw-bold">2. Kategori Barang</label>
                                            <select class="form-select" id="filter_kategori">
                                                <option value="all">Semua Kategori</option>
                                                @foreach($kategori as $kat)
                                                    <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="search_katalog" class="form-label fw-bold">3. Cari Nama Barang</label>
                                            <div class="input-group">
                                                <input type="text" id="search_katalog" class="form-control" placeholder="Ketik kata kunci nama barang...">
                                                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <label class="form-label text-info"> Katalog Barang & Keranjang Permintaan</label>
                                    <div class="row">
                                        <div class="col-xl-8">
                                            <div class="catalog-container">
                                                <div class="row row-cols-1 row-cols-md-3 g-3" id="catalog-list">
                                                    @foreach ($barang as $item)
                                                        @php
                                                            $stok = (int) $item->qty_item;
                                                            $foto = $item->foto_barang ? '/' . $item->foto_barang : '/backend/assets/images/barang/default_atk.png';
                                                            $kelompokNama = optional($item->kelompok)->nama ?? 'N/A';
                                                            $kategoriNama = optional($item->kategori)->nama ?? '';
                                                        @endphp
                                                        <div class="col" data-kelompok-id="{{ $item->kelompok_id }}" data-kategori-id="{{ $item->kategori_id ?? '' }}" data-kategori-name="{{ $kategoriNama }}" data-nama="{{ strtolower($item->nama) }}">
                                                            <div class="product-card h-100 shadow-sm d-flex flex-column justify-content-between">
                                                                <div>
                                                                    <div class="product-img-wrapper">
                                                                        <img src="{{ $foto }}" class="product-img"
                                                                            onerror="this.onerror=null; this.src='/backend/assets/images/barang/default_atk.png';">
                                                                    </div>
                                                                    <div class="p-3 pb-0">
                                                                        <h6 class="fw-bold text-dark text-truncate mb-1" title="{{ $item->nama }}">{{ $item->nama }}</h6>
                                                                        <p class="mb-2 text-muted" style="font-size: 0.85rem;">{{ $kelompokNama }}</p>
                                                                        <p class="mb-2 text-muted" style="font-size: 0.85rem;">Stok: <b class="{{ $stok > 0 ? 'text-success' : 'text-danger' }}">{{ $stok }}</b> {{ $item->satuan ?? 'Buah' }}</p>
                                                                    </div>
                                                                </div>

                                                                <div class="p-3 pt-0">
                                                                    @if ($stok > 0)
                                                                        <div class="input-group input-group-sm mb-2">
                                                                            <span class="input-group-text">Qty</span>
                                                                            <input type="number" min="1" max="{{ $stok }}" class="form-control catalog-qty" id="input-qty-{{ $item->id }}" value="1">
                                                                        </div>
                                                                        <button type="button" class="btn btn-info btn-sm w-100 add-to-cart-btn fw-bold"
                                                                            data-id="{{ $item->id }}"
                                                                            data-nama="{{ $item->nama }}"
                                                                            data-kelompok-id="{{ $item->kelompok_id }}"
                                                                            data-kelompok-nama="{{ $kelompokNama }}"
                                                                            data-satuan="{{ $item->satuan ?? 'Buah' }}"
                                                                            data-qty="{{ $stok }}">
                                                                            <i class="mdi mdi-cart-plus me-1"></i> Tambah
                                                                        </button>
                                                                    @else
                                                                        <button class="btn btn-danger btn-sm w-100" disabled>Stok Habis</button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-4">
                                            <div class="card cart-sidebar p-3 shadow-sm">
                                                <h5 class="card-title border-bottom pb-2 mb-3 text-dark">
                                                    <i class="mdi mdi-cart me-1 text-info"></i> Keranjang Permintaan
                                                </h5>
                                                <div id="cart-items-wrapper" style="max-height: 360px; overflow-y: auto; padding-right: 5px;">
                                                    <p class="text-muted text-center my-4" id="empty-cart-msg">Belum ada barang di keranjang</p>
                                                </div>
                                                <div class="border-top pt-3 mt-3">
                                                    <div class="d-flex justify-content-between mb-3 fw-bold">
                                                        <span>Total Macam Barang:</span>
                                                        <span id="total-jenis-barang" class="text-primary">0 Item</span>
                                                    </div>
                                                    <div class="d-grid gap-2">
                                                        <button type="button" class="btn btn-success waves-effect waves-light" id="save_changes_btn">
                                                            <i class="mdi mdi-check-all me-1"></i> Simpan Perubahan
                                                        </button>
                                                        <button type="button" class="btn btn-light waves-effect cart-prev-btn"><i class="mdi mdi-arrow-left me-1"></i> Kembali</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Form tersembunyi untuk submit perubahan (data diambil dari keranjang JS) -->
                                    <form id="mainForm" method="POST" action="{{ route('permintaan.update', $id) }}">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" name="date" id="hidden_date">
                                        <input type="hidden" name="description" id="hidden_description">
                                        <input type="hidden" name="table_data" id="table_data">
                                        <input type="hidden" name="permintaan_id" id="permintaan_id" value="{{ $id }}">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Handlebars template removed; cart-driven UI used instead -->

    <script type="text/javascript">
        $(document).ready(function() {
            var availableQty = 0;
            var barangSatuan = '';

            // Initialize cart from server-side pilihan (existing items)
            let cart = {!! json_encode($pilihan->map(function($it){ return [
                'id' => $it->barang_id,
                'barang_id' => $it->barang_id,
                'kelompok_id' => optional($it->barang->kelompok)->id ?? '',
                'kelompok_nama' => optional($it->barang->kelompok)->nama ?? '',
                'barang_nama' => optional($it->barang)->nama ?? '',
                'qty_req' => $it->req_qty,
                'barang_satuan' => optional($it->barang)->satuan ?? ''
            ]; })->toArray()) !!};

            // === VALIDASI FORM & NAVIGASI WIZARD ===
            function validateStep1() {
                const date = $('#date').val();
                const description = $('#textarea').val();
                const isValid = date && description;
                $('#next_btn_step1').prop('disabled', !isValid);
                $('#warning_message').toggle(!isValid && (date || description));
                return isValid;
            }

            function navigateToStep(step) {
                $('.step-content').hide();
                $('#step' + step).show();
                $('.step').removeClass('active');
                $(`.step[data-step="${step}"]`).addClass('active');
            }

            $('#next_btn_step1').on('click', () => {
                if (validateStep1()) navigateToStep(2);
            });
            $('#prev_btn, .cart-prev-btn').on('click', () => navigateToStep(1));

            validateStep1(); // Inisialisasi validasi

            // (Search/autocomplete UI removed; catalog + cart used instead)

            // === FUNGSI TABEL & SUBMIT ===
            function checkIfTableIsEmpty() {
                return (cart && cart.length > 0);
            }

            function updateCartUI() {
                const $wrapper = $('#cart-items-wrapper');
                $wrapper.empty();

                if (!cart || cart.length === 0) {
                    $('#empty-cart-msg').show();
                    $('#total-jenis-barang').text('0 Item');
                    return;
                }

                $('#empty-cart-msg').hide();
                cart.forEach((item, index) => {
                    $wrapper.append(`
                        <div class="cart-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div style="max-width: 75%;">
                                    <span class="fw-bold text-dark d-block text-truncate" style="font-size:0.85rem;">${item.barang_nama}</span>
                                    <small class="text-muted" style="font-size:0.75rem;">${item.kelompok_nama}</small>
                                    <div class="mt-1">
                                        <span class="badge bg-info text-white font-size-11">${item.qty_req} ${item.barang_satuan}</span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-cart-item-btn" data-index="${index}">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                            </div>
                        </div>
                    `);
                });
                $('#total-jenis-barang').text(cart.length + ' Macam');
            }

            $(document).on('click', '.remove-cart-item-btn', function() {
                const index = $(this).data('index');
                if (typeof index !== 'undefined' && cart[index]) {
                    cart.splice(index, 1);
                }
                checkIfTableIsEmpty();
                updateCartUI();
            });

            $(document).on('click', '.add-to-cart-btn', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const kelompokId = $(this).data('kelompok-id');
                const kelompokNama = $(this).data('kelompok-nama');
                const satuan = $(this).data('satuan');
                const maxQty = parseInt($(this).data('qty'), 10) || 0;
                const qty = parseInt($(`#input-qty-${id}`).val(), 10) || 0;

                if (qty <= 0 || qty > maxQty) {
                    alert('Jumlah permintaan tidak valid atau melebihi stok.');
                    return;
                }

                // Update cart array
                const exist = cart.find(it => String(it.id) === String(id));
                if (exist) {
                    if ((exist.qty_req + qty) > maxQty) {
                        alert('Total permintaan melebihi stok.');
                        return;
                    }
                    exist.qty_req = Number(exist.qty_req) + Number(qty);
                } else {
                    cart.push({
                        id: id,
                        barang_id: id,
                        kelompok_id: kelompokId,
                        kelompok_nama: kelompokNama,
                        barang_nama: nama,
                        qty_req: qty,
                        barang_satuan: satuan
                    });
                }

                checkIfTableIsEmpty();
                updateCartUI();
            });

            // Catalog filtering (client-side) using data attributes on catalog items
            function applyCatalogFilters() {
                const kelompok = $('#filter_kelompok').val() || 'all';
                const kategori = $('#filter_kategori').val() || 'all';
                const keyword = ($('#search_katalog').val() || '').toLowerCase().trim();

                $('#catalog-list .col').each(function() {
                    const $col = $(this);
                    const itemKelompok = String($col.data('kelompok-id') || '');
                    const itemKategori = String($col.data('kategori-id') || '');
                    const name = String($col.data('nama') || '').toLowerCase();

                    const matchKelompok = (kelompok === 'all' || itemKelompok === String(kelompok));
                    const matchKategori = (kategori === 'all' || itemKategori === String(kategori));
                    const matchKeyword = (keyword === '' || name.indexOf(keyword) !== -1);

                    if (matchKelompok && matchKategori && matchKeyword) $col.show();
                    else $col.hide();
                });
            }

            // Rebuild kategori select based on selected kelompok
            function updateKategoriOptions(selectedKelompok) {
                const $kategori = $('#filter_kategori');
                const current = $kategori.val() || 'all';
                $kategori.empty();
                $kategori.append(`<option value="all">Semua Kategori</option>`);

                if (!selectedKelompok || selectedKelompok === 'all') {
                    // If 'all' kelompok, fetch all kategoris
                    $.getJSON("/pilihan/get-kategori/all", function(data) {
                        if (Array.isArray(data)) {
                            data.forEach(function(kat) {
                                $kategori.append(`<option value="${kat.id}">${kat.nama}</option>`);
                            });
                            $kategori.prop('disabled', data.length === 0);
                            if (current && (current === 'all' || data.find(d => String(d.id) === String(current)))) {
                                $kategori.val(current);
                            }
                        }
                    });
                    return;
                }

                // Fetch categories that belong to the selected kelompok via AJAX route
                $.getJSON(`/pilihan/get-kategori/${selectedKelompok}`, function(data) {
                    if (Array.isArray(data)) {
                        data.forEach(function(kat) {
                            $kategori.append(`<option value="${kat.id}">${kat.nama}</option>`);
                        });
                        $kategori.prop('disabled', data.length === 0);
                        if (current && (current === 'all' || data.find(d => String(d.id) === String(current)))) {
                            $kategori.val(current);
                        } else {
                            $kategori.val('all');
                        }
                    }
                }).fail(function() {
                    // On failure, keep select at 'all' and disable
                    $kategori.val('all');
                    $kategori.prop('disabled', true);
                });
            }

            $('#filter_kelompok').on('change', function() { updateKategoriOptions($(this).val()); applyCatalogFilters(); });
            $('#filter_kategori').on('change', function() { applyCatalogFilters(); });
            $('#search_katalog').on('input', function() { applyCatalogFilters(); });

            // kategori options are server-rendered from $kategori — now replaced on init by JS to reflect selected kelompok

            // legacy delete-button removed — cart removal handled in cart UI

            $('#mainForm').on('submit', function(e) {
                e.preventDefault();
                if (!cart || cart.length === 0) {
                    alert('Keranjang permintaan tidak boleh kosong.');
                    return;
                }

                $('#hidden_date').val($('#date').val());
                $('#hidden_description').val($('#textarea').val());

                const tableData = cart.map(item => ({
                    kelompok_id: item.kelompok_id || null,
                    barang_id: item.barang_id || item.id,
                    kelompok_nama: item.kelompok_nama || null,
                    barang_nama: item.barang_nama || null,
                    qty_req: item.qty_req,
                    barang_satuan: item.barang_satuan || null
                }));

                $('#table_data').val(JSON.stringify(tableData));
                $(this).off('submit').submit();
            });

            // Save changes button in cart triggers form submit
            $('#save_changes_btn').on('click', function() {
                $('#mainForm').submit();
            });

            // Inisialisasi awal
            updateKategoriOptions($('#filter_kelompok').val());
            applyCatalogFilters();
            checkIfTableIsEmpty();
            updateCartUI();
        });
    </script>
@endsection
