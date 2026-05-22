@extends(auth()->user()->role === 'admin' ? 'admin.admin_master' : (auth()->user()->role === 'supervisor' ? 'supervisor.supervisor_master' : 'pegawai.pegawai_master'))
@section(auth()->user()->role === 'admin' ? 'admin' : (auth()->user()->role === 'supervisor' ? 'supervisor' : 'pegawai'))

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 text-info">Penambahan Barang</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Barang</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Persediaan Barang</a></li>
                            <li class="breadcrumb-item active">Tambah Barang</li>
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

                        <h4 class="card-title">Tambah Barang</h4><br>

                        <form method="post" action="{{ route('barang.store') }}" id="myForm" enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-3">
                                <label for="kode_barang" class="col-sm-2 col-form-label">Kode Barang <span class="text-danger">*</span></label>
                                <div class="form-group col-sm-10">
                                    <input name="kode_barang" class="form-control" type="text" id="kode_barang" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="nama" class="col-sm-2 col-form-label">Nama Barang <span class="text-danger">*</span></label>
                                <div class="form-group col-sm-10">
                                    <input name="nama" class="form-control" type="text" id="nama" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="kelompok_barang" class="col-sm-2 col-form-label">Kelompok Barang <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select id="kelompok_select" name="kelompok_id" class="form-select" aria-label="Default select example" required>
                                        <option selected="" disabled>Pilih jenis kelompok barang</option>
                                        @foreach($kelompok as $kel)
                                        <option value="{{$kel->id}}">{{$kel->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="kategori_barang" class="col-sm-2 col-form-label">Kategori Barang <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select id="kategori_select" name="kategori_id" class="form-select" aria-label="Default select example" required>
                                        <option selected="" disabled>Pilih kategori barang</option>
                                        @foreach($kategori as $kat)
                                        <option value="{{$kat->id}}">{{$kat->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="qty_item" class="col-sm-2 col-form-label">Stok Barang <span class="text-danger">*</span></label>
                                <div class="form-group col-sm-10">
                                    <input name="qty_item" class="form-control" type="text" id="qty_item">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="satuan" class="col-sm-2 col-form-label">Satuan Barang <span class="text-danger">*</span></label>
                                <div class="form-group col-sm-10">
                                    <select name="satuan" id="satuan" class="form-select" required>
                                        <option selected disabled>Pilih satuan barang</option>
                                        @foreach($satuan as $satuan_item)
                                            <option value="{{ $satuan_item->satuan }}">{{ $satuan_item->satuan }}</option>
                                        @endforeach
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-7" id="satuanBaruContainer" style="display: none;">
                                    <div class="d-flex">
                                        <label for="satuanBaru" class="col-form-label me-2" style="width: 40%;">Masukkan Satuan Baru</label>
                                        <input name="satuanBaru" class="form-control" type="text" id="satuanBaru">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="harga_total" class="col-sm-2 col-form-label">Harga Total <span class="text-danger">*</span></label>
                                <div class="form-group col-sm-10">
                                    <input name="harga_total" class="form-control" type="text" id="harga_total" onkeyup="formatRupiah(this)">
                                </div>
                            </div>

                            <script>
                                function formatRupiah(input) {
                                    // Hapus semua karakter non-digit (termasuk titik yang sudah ada)
                                    let number_string = input.value.replace(/\D/g, '').toString();
                                    
                                    // Jika input kosong, keluar
                                    if (number_string === '') {
                                        input.value = '';
                                        return;
                                    }

                                    let sisa = number_string.length % 3;
                                    let rupiah = number_string.substr(0, sisa);
                                    let ribuan = number_string.substr(sisa).match(/\d{3}/gi);

                                    if (ribuan) {
                                        let separator = sisa ? '.' : '';
                                        rupiah += separator + ribuan.join('.');
                                    }
                                    
                                    // Pastikan nilai dikembalikan ke input
                                    input.value = rupiah;
                                }

                                // PENTING: Untuk pengiriman data ke Controller/Server:
                                // Tambahkan event listener untuk membersihkan nilai sebelum form disubmit.
                                $(document).ready(function() {
                                    $('#myForm').on('submit', function() {
                                        let formattedValue = $('#harga_total').val();
                                        // HAPUS TITIK: Konversi ke nilai angka mentah (misal: "1500000")
                                        let rawValue = formattedValue.replace(/\./g, '');
                                        // SET NILAI INPUT KE ANGKA MENTAH SEBELUM SUBMIT
                                        $('#harga_total').val(rawValue);
                                    });
                                });
                            </script>
                            
                            <div class="row mb-3">
                                <label for="foto" class="col-sm-2 col-form-label">Foto Barang <span class="text-danger">*</span></label>
                                <div class="form-group col-sm-10">
                                    <input name="foto" class="form-control" type="file" id="foto" accept=".jpg,.jpeg,.png">
                                    <small class="form-text text-muted">Usahakan gambar dalam bentuk PNG, JPEG atau JPG untuk hasil yang lebih baik dengan format nama foto_"kode barang".</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <input type="submit" class="btn btn-info waves-effect waves-light" value="Tambahkan Barang">
                            </div>

                        </form>
                    </div>
                </div>
            </div> <!-- end col -->
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const satuanSelect = document.getElementById('satuan');
        const satuanBaruContainer = document.getElementById('satuanBaruContainer');
        const satuanBaruInput = document.getElementById('satuanBaru');

        satuanSelect.addEventListener('change', function() {
            if (this.value === 'lainnya') {
                satuanBaruContainer.style.display = 'block'; // Show the input and label
                satuanBaruInput.disabled = false; // Enable input field
            } else {
                satuanBaruContainer.style.display = 'none'; // Hide the input and label
                satuanBaruInput.disabled = true; // Disable input field
                satuanBaruInput.value = ''; // Clear input field
            }
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function (){

        $('#kode_barang').on('blur', function() {
            var kodeBarang = $(this).val();
            if (kodeBarang.length < 16) {
                $(this).tooltip({
                    title: "Kode barang harus terdiri dari 16 karakter.",
                    placement: "top", // Menggunakan placement 'top' untuk menempatkan tooltip di atas
                    trigger: "manual",
                    customClass: 'text-left-tooltip' // Custom class untuk text-align kiri
                }).tooltip('show');
            } else {
                $(this).tooltip('hide');
            }
        });

        $('#myForm').validate({
            rules: {
                nama: {
                    required : true,
                },
                kelompok_id: {
                    required: true,
                },
                kode_barang: {
                    required: true,
                },
                satuan: {
                    required: true,
                }
            },
            messages: {
                nama: {
                    required: "Nama barang harus diisi.",
                },
                kelompok_id: {
                    required: "Kelompok barang harus dipilih.",
                },
                kode_barang: {
                    required: "Kode barang harus diisi.",
                },
                satuan: {
                    required: "Satuan barang harus dipilih.",
                }
            },
            errorElement : 'span', 
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight : function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight : function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const kelompokSelect = document.getElementById('kelompok_select');
        const kategoriSelect = document.getElementById('kategori_select');
        const kategoriUrl = '{{ url('pilihan/get-kategori') }}';

        function loadKategoriOptions(kelompokId) {
            kategoriSelect.innerHTML = '<option disabled selected>Mengambil kategori...</option>';

            fetch(`${kategoriUrl}/${kelompokId}`)
                .then(response => response.json())
                .then(data => {
                    kategoriSelect.innerHTML = '<option disabled selected>Pilih kategori barang</option>';
                    if (Array.isArray(data) && data.length) {
                        data.forEach(kategori => {
                            const option = document.createElement('option');
                            option.value = kategori.id;
                            option.textContent = kategori.nama;
                            kategoriSelect.appendChild(option);
                        });
                    } else {
                        kategoriSelect.innerHTML = '<option disabled selected>Tidak ada kategori untuk kelompok ini</option>';
                    }
                })
                .catch(() => {
                    kategoriSelect.innerHTML = '<option disabled selected>Gagal memuat kategori</option>';
                });
        }

        kelompokSelect.addEventListener('change', function() {
            const kelompokId = this.value;
            if (kelompokId) {
                loadKategoriOptions(kelompokId);
            }
        });
    });
</script>

@endsection
