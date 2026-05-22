@extends(auth()->user()->role === 'admin' ? 'admin.admin_master' : (auth()->user()->role === 'supervisor' ? 'supervisor.supervisor_master' : 'pegawai.pegawai_master'))

@section(auth()->user()->role === 'admin' ? 'admin' : (auth()->user()->role === 'supervisor' ? 'supervisor' : 'pegawai'))

<head>
    <title>Edit Permintaan | SIMUTAN</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<style>
    /* CSS MULTI-STEP WIZARD */
    .step-indicator {
        display: flex;
        width: 100%;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
        position: relative;
    }
    .step-indicator::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #e9ecef;
        z-index: 1;
    }
    .step {
        display: flex;
        align-items: center;
        position: relative;
        z-index: 2;
        background-color: #fff;
        padding-right: 15px;
    }
    .step:last-child {
        padding-right: 0;
    }
    .step .circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #495057;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        border: 2px solid #e9ecef;
        transition: background-color 0.3s, border-color 0.3s;
    }
    .step .label {
        margin-left: 10px;
        color: #6c757d;
        font-weight: 500;
        white-space: nowrap;
    }
    .step.active .circle {
        background-color: #043277;
        border-color: #043277;
        color: white;
    }
    .step.active .label {
        color: #043277;
        font-weight: bold;
    }

    /* CSS KATALOG & KERANJANG */
    .catalog-container {
        max-height: 600px;
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
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 text-info">Edit Permintaan Barang</h4>
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

                            <div class="step-indicator">
                                <div class="step active" data-step="1">
                                    <div class="circle">1</div>
                                    <div class="label">Informasi Permintaan</div>
                                </div>
                                <div class="step" data-step="2">
                                    <div class="circle">2</div>
                                    <div class="label">Detail Barang</div>
                                </div>
                            </div>

                            <div id="step1" class="step-content">
                                <hr class="mt-0">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nama Pengaju</label>
                                            <input type="text" class="form-control" id="name" value="{{ $pilihan->created_by }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="date" class="form-label">Tanggal Permintaan</label>
                                            <input class="form-control" name="date" type="date" id="date" value="{{ $pilihan->date }}">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="textarea" class="form-label">Catatan</label>
                                            <textarea id="textarea" class="form-control" maxlength="225" rows="3" placeholder="Jelaskan keperluan permintaan Anda..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div id="warning_message" class="alert alert-danger" style="display: none;">
                                    Semua kolom harus diisi. Harap isi tanggal dan catatan sebelum melanjutkan.
                                </div>
                                <div class="mt-4 d-flex justify-content-end">
                                    <button type="button" class="btn btn-info waves-effect waves-light" id="next_btn_step1">Lanjut <i class="mdi mdi-arrow-right ms-1"></i></button>
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
                                        <select class="form-select" id="filter_kategori" disabled>
                                            <option value="all">Semua Kategori</option>
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

                                <div class="row">
                                    <div class="col-lg-8">
                                        <h5 class="mb-3 text-secondary"><i class="mdi mdi-grid me-1"></i> Katalog Barang Persediaan</h5>
                                        <div class="catalog-container">
                                            <div class="row row-cols-1 row-cols-md-3 g-3" id="catalog-list">
                                                </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="card cart-sidebar p-3 shadow-sm">
                                            <h5 class="card-title border-bottom pb-2 mb-3 text-dark">
                                                <i class="mdi mdi-cart me-1 text-info"></i> Keranjang Pembaruan
                                            </h5>
                                            
                                            <div id="cart-items-wrapper" style="max-height: 350px; overflow-y: auto; padding-right: 5px;">
                                                <p class="text-muted text-center my-4" id="empty-cart-msg">Belum ada barang dipilih</p>
                                            </div>

                                            <div class="border-top pt-3 mt-3">
                                                <div class="d-flex justify-content-between mb-3 fw-bold">
                                                    <span>Total Macam Barang:</span>
                                                    <span id="total-jenis-barang" class="text-primary">0 Item</span>
                                                </div>
                                                
                                                <form id="mainForm" method="post" action="{{ route('pilihan.update', $pilihan->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="table_data" id="table_data">
                                                    <input type="hidden" id="hidden_date" name="hidden_date">
                                                    <input type="hidden" id="hidden_description" name="hidden_description">
                                                    
                                                    <div class="d-grid gap-2">
                                                        <button type="submit" class="btn btn-success waves-effect waves-light btn-lg" id="submit_btn" disabled>
                                                            <i class="mdi mdi-check-all me-1"></i> Simpan Perubahan
                                                        </button>
                                                        <button type="button" class="btn btn-light waves-effect" id="prev_btn">
                                                            <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Step 1
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    let masterBarang = [];
    let masterKategori = [];

    // KUNCI SINKRONISASI EDIT: Masukkan seluruh daftar item lama ke array javascript keranjang belanja
    let cart = [
        @if(isset($pilihanBarangLama))
            @foreach($pilihanBarangLama as $itemLama)
            {
                id: parseInt("{{ $itemLama->barang_id }}"),
                barang_nama: "{!! optional($barang->find($itemLama->barang_id))->nama ?? 'Barang Tanpa Nama' !!}",
                kelompok_nama: "{{ optional($barang->find($itemLama->barang_id))->kelompok->nama ?? 'Barang Konsumsi' }}",
                qty_req: parseInt("{{ $itemLama->req_qty }}"),
                barang_satuan: "{{ optional($barang->find($itemLama->barang_id))->satuan ?? 'Buah' }}"
            },
            @endforeach
        @endif
    ];

    // Mengembalikan catatan deskripsi lama ke textarea
    $('#textarea').val(`{{ $pilihan->description }}`);

    function renderCatalog(items) {
        let html = '';

        if (!items.length) {
            $('#catalog-list').html(
                `<div class="col-12 text-center text-muted py-5">
                    Barang tidak ditemukan
                </div>`
            );
            return;
        }

        items.forEach(item => {
            const stok = parseInt(item.qty_item) || 0;
            const foto = item.foto_barang ? '/' + item.foto_barang : '/backend/assets/images/barang/default_atk.png';

            html += `
            <div class="col">
                <div class="product-card h-100 shadow-sm d-flex flex-column justify-content-between">
                    <div>
                        <div class="product-img-wrapper">
                            <img src="${foto}" class="product-img" onerror="this.onerror=null; this.src='/backend/assets/images/barang/default_atk.png';">
                        </div>
                        <div class="p-3 pb-0">
                            <h6 class="fw-bold text-dark text-truncate mb-1" title="${item.nama}">${item.nama}</h6>
                            <p class="mb-2 text-muted" style="font-size: 0.85rem;">Stok: <b class="${stok > 0 ? 'text-success' : 'text-danger'}">${stok}</b> ${item.satuan ?? 'Buah'}</p>
                        </div>
                    </div>

                    <div class="p-3 pt-0">
                        ${stok > 0 ? `
                            <div class="input-group input-group-sm mb-2">
                                <span class="input-group-text">Qty</span>
                                <input type="number" min="1" max="${stok}" class="form-control" id="input-qty-${item.id}" value="1">
                            </div>
                            <button class="btn btn-info btn-sm w-100 add-to-cart-btn fw-bold" data-id="${item.id}">
                                <i class="mdi mdi-cart-plus me-1"></i> Tambah
                            </button>
                        ` : `
                            <button class="btn btn-danger btn-sm w-100" disabled>Stok Habis</button>
                        `}
                    </div>
                </div>
            </div>`;
        });

        $('#catalog-list').html(html);
    }

    function applyAllFilters() {
        let kelompokId = $('#filter_kelompok').val();
        let kategoriId = $('#filter_kategori').val();
        let searchKeyword = $('#search_katalog').val().toLowerCase().trim();

        if (!masterBarang || masterBarang.length === 0) return;

        let filtered = masterBarang.filter(product => {
            let matchKelompok = (kelompokId === 'all' || product.kelompok_id == kelompokId);
            let matchKategori = (kategoriId === 'all' || product.kategori_id == kategoriId);
            let matchText = product.nama ? product.nama.toLowerCase().includes(searchKeyword) : false;
            return matchKelompok && matchKategori && matchText;
        });

        renderCatalog(filtered);
    }

    function loadAllBarangs() {
        $('#catalog-list').html('<div class="col-12 text-center my-4 text-info fw-bold"><i class="mdi mdi-spin mdi-loading me-1"></i> Sinkronisasi gudang SIMUTAN...</div>');

        $.ajax({
            url: "{{ route('get-category') }}",
            type: "GET",
            success: function(data) {
                if (data) {
                    masterKategori = data.kategoris || [];
                    masterBarang = data.barang || [];
                    applyAllFilters();
                }
            },
            error: function(xhr, status, error) {
                $('#catalog-list').html('<div class="col-12 text-center my-4 text-danger fw-bold">Gagal mengambil data dari server.</div>');
            }
        });
    }

    function updateKategoriOptions(kelompokId) {
        let $kategoriSelect = $('#filter_kategori');
        $kategoriSelect.empty().append('<option value="all">Semua Kategori</option>');

        if (kelompokId === 'all' || !masterKategori) {
            $kategoriSelect.prop('disabled', true).val('all');
            return;
        }

        let filteredKategori = masterKategori.filter(k => k.kelompok_id == kelompokId);

        if (filteredKategori.length > 0) {
            filteredKategori.forEach(kat => {
                $kategoriSelect.append(`<option value="${kat.id}">${kat.nama}</option>`);
            });
            $kategoriSelect.prop('disabled', false); 
        } else {
            $kategoriSelect.prop('disabled', true);
        }
    }

    function updateCartUI() {
        if (cart.length === 0) {
            $('#empty-cart-msg').show();
            $('#cart-items-wrapper .cart-item').remove();
            $('#total-jenis-barang').text('0 Item');
            $('#submit_btn').prop('disabled', true);
            return;
        }

        $('#empty-cart-msg').hide();
        $('#cart-items-wrapper .cart-item').remove();

        let cartHtml = '';
        cart.forEach((item, index) => {
            cartHtml += `
                <div class="cart-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div style="max-width: 80%;">
                            <span class="fw-bold text-dark d-block text-truncate" style="font-size:0.85rem;">${item.barang_nama}</span>
                            <small class="text-muted" style="font-size:0.75rem;">${item.kelompok_nama}</small>
                            <div class="mt-1">
                                <span class="badge bg-success text-white font-size-11">${item.qty_req} ${item.barang_satuan}</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-cart-item-btn" data-index="${index}">
                            <i class="mdi mdi-trash-can-outline">X</i>
                        </button>
                    </div>
                </div>`;
        });

        $('#cart-items-wrapper').append(cartHtml);
        $('#total-jenis-barang').text(cart.length + ' Macam');
        $('#submit_btn').prop('disabled', false);
    }

    $(document).on('click', '.add-to-cart-btn', function() {
        const id = $(this).data('id');
        const qtyReq = parseInt($(`#input-qty-${id}`).val()) || 0;
        const product = masterBarang.find(p => p.id === id);

        if (!product) return;

        if (qtyReq <= 0 || qtyReq > parseInt(product.qty_item)) {
            alert('Kuantitas jumlah permintaan tidak valid atau melebihi sisa stok!');
            return;
        }

        let existingItem = cart.find(item => item.id === id);
        if (existingItem) {
            if ((existingItem.qty_req + qtyReq) > parseInt(product.qty_item)) {
                alert('Total akumulasi permintaan melebihi batas sisa stok!');
                return;
            }
            existingItem.qty_req += qtyReq;
        } else {
            let kelompokNama = "Barang Konsumsi";
            if(product.kelompok_id == 2) kelompokNama = "Barang Pemeliharaan";
            else if(product.kelompok_id == 3) kelompokNama = "Alat/Bahan Kegiatan Kantor Lainnya";

            cart.push({
                id: product.id,
                barang_nama: product.nama, 
                kelompok_nama: kelompokNama,
                qty_req: qtyReq,
                barang_satuan: product.satuan || 'Buah'
            });
        }

        updateCartUI();
    });

    $(document).on('click', '.remove-cart-item-btn', function() {
        const index = $(this).data('index');
        cart.splice(index, 1);
        updateCartUI();
    });

    $('#filter_kelompok').on('change', function() {
        let kelompokId = $(this).val();
        updateKategoriOptions(kelompokId);
        applyAllFilters();
    });
    $('#filter_kategori, #search_katalog').on('input change', function() {
        applyAllFilters();
    });

    // WIZARD ENGINE
    function validateStep1() {
        const date = $('#date').val();
        const description = $('#textarea').val() ? $('#textarea').val().trim() : "";
        const isValid = (date !== "") && (description !== "");
        
        if (isValid) {
            $('#next_btn_step1').removeAttr('disabled');
            $('#warning_message').hide();
        } else {
            $('#next_btn_step1').attr('disabled', 'disabled');
        }
        return isValid;
    }

    $('#date, #textarea').on('keyup input change', function() { validateStep1(); });

    function navigateToStep(step) {
        $('.step-content').hide();
        $('#step' + step).show();
        $('.step').removeClass('active');
        $(`.step[data-step="${step}"]`).addClass('active');
    }

    $('#next_btn_step1').on('click', function(e) { 
        e.preventDefault();
        if (validateStep1()) { navigateToStep(2); }
        else { $('#warning_message').show(); }
    });
    
    $('#prev_btn').on('click', () => navigateToStep(1));

    // Submit payload JSON ke backend controller
    $('#mainForm').on('submit', function(e) {
        e.preventDefault();
        if (cart.length === 0) return;

        $('#hidden_date').val($('#date').val());
        $('#hidden_description').val($('#textarea').val().trim());

        let tableDataPayload = cart.map(item => {
            return {
                kelompok_nama: item.kelompok_nama,
                barang_nama: item.barang_nama,
                qty_req: item.qty_req,
                barang_satuan: item.barang_satuan
            };
        });

        $('#table_data').val(JSON.stringify(tableDataPayload));
        $(this).off('submit').submit();
    });

    // Jalankan booting awal
    validateStep1();
    updateCartUI();
    loadAllBarangs();
});
</script>
@endsection