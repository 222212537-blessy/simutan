@extends(auth()->user()->role === 'admin' ? 'admin.admin_master' : (auth()->user()->role === 'supervisor' ? 'supervisor.supervisor_master' : 'pegawai.pegawai_master'))
@section(auth()->user()->role === 'admin' ? 'admin' : (auth()->user()->role === 'supervisor' ? 'supervisor' : 'pegawai'))

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/handlebars@4.7.7/dist/handlebars.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Halaman Edit Barang</h4><br><br>

                        <form method="POST" action="{{ route('barang.update', $barang->id) }}" id="myForm" enctype="multipart/form-data">
                            @csrf
                            @method('POST') <!-- Menggunakan method PUT untuk update -->

                            <input type="hidden" name="id" value="{{ $barang->id }}">
                            <input type="hidden" name="existing_foto" value="{{ $barang->foto }}"> <!-- Menyimpan nama foto yang ada -->

                            <div class="row mb-3">
                                <label for="kode_barang" class="col-sm-2 col-form-label">Kode Barang <span class="text-danger">*</span></label>
                                <div class="form-group col-sm-10">
                                    <input name="kode_barang" value="{{ $barang->kode }}" class="form-control" type="text" id="kode_barang" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="nama" class="col-sm-2 col-form-label">Nama Barang <span class="text-danger">*</span></label>
                                <div class="form-group col-sm-10">
                                    <input name="nama" value="{{ $barang->nama }}" class="form-control" type="text" id="nama" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="kelompok_barang" class="col-sm-2 col-form-label">Kelompok Barang <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select name="kelompok_id" class="form-select" required>
                                        <option selected="" disabled>Pilih jenis kelompok barang</option>
                                        @foreach($kelompok as $kel)
                                        <option value="{{$kel->id}}" {{ $kel->id == $barang->kelompok_id ? 'selected' : '' }}>{{$kel->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="qty_item" class="col-sm-2 col-form-label">Stok Barang <span class="text-danger">*</span></label>
                                <div class="form-group col-sm-10">
                                    <input name="qty_item" value="{{ $barang->qty_item }}" class="form-control" type="text" id="qty_item">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="satuan" class="col-sm-2 col-form-label">Satuan Barang <span class="text-danger">*</span></label>
                                <div class="form-group col-sm-10">
                                    <select name="satuan" id="satuan" class="form-select" required>
                                        <option value="" disabled>Pilih satuan barang</option>
                                        @foreach($satuans as $satuan_item)
                                            <option value="{{ $satuan_item }}" {{ $satuan_item == $barang->satuan ? 'selected' : '' }}>{{ $satuan_item }}</option>
                                        @endforeach
                                        <option value="lainnya" {{ $barang->satuan == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-7" id="satuanBaruContainer" style="{{ $barang->satuan == 'lainnya' ? 'display: block;' : 'display: none;' }}">
                                    <div class="d-flex">
                                        <label for="satuanBaru" class="col-form-label me-2" style="width: 40%;">Masukkan Satuan Baru</label>
                                        <input name="satuanBaru" class="form-control" type="text" id="satuanBaru" value="{{ old('satuanBaru', $barang->satuan == 'lainnya' ? $barang->satuan : '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="harga_total" class="col-sm-2 col-form-label">Harga Total <span class="text-danger">*</span></label>
                                <div class="form-group col-sm-10">
                                    <input 
                                        name="harga_total" 
                                        value="{{ number_format($barang->harga_total, 0, ',', '.') }}" class="form-control" 
                                        type="text" 
                                        id="harga_total" 
                                        onkeyup="formatRupiah(this)"
                                    >
                                </div>
                            </div>

                            <script type="text/javascript">
                                // --- FUNGSI FORMAT DINAMIS SAAT MENGETIK ---
                                function formatRupiah(input) {
                                    // Hapus semua karakter non-digit (termasuk titik yang sudah ada)
                                    let number_string = input.value.replace(/\D/g, '').toString();
                                    
                                    let sisa = number_string.length % 3;
                                    let rupiah = number_string.substr(0, sisa);
                                    let ribuan = number_string.substr(sisa).match(/\d{3}/gi);

                                    if (ribuan) {
                                        let separator = sisa ? '.' : '';
                                        rupiah += separator + ribuan.join('.');
                                    }
                                    
                                    input.value = rupiah;
                                }


                                // --- LOGIKA SUBMIT DAN PEMBESIHAN NILAI (MENGGUNAKAN JQUERY) ---
                                $(document).ready(function() {
                                    // Logika untuk menampilkan SatuanBaruContainer
                                    // ... (Kode untuk satuanBaruContainer di sini, tidak berubah) ...
                                    
                                    // Memastikan tombol edit bekerja dan membersihkan nilai
                                    $('#editBtn').on('click', function(e) {
                                        e.preventDefault(); 
                                        
                                        // 1. Ambil nilai yang diformat dari input (misal: "1.500.000")
                                        let currentFormattedValue = $('#harga_total').val();
                                        
                                        // 2. HAPUS TITIK: Konversi ke nilai angka mentah (misal: "1500000")
                                        let rawValue = formattedValue.replace(/\./g, '');

                                        // 3. SET NILAI INPUT KE ANGKA MENTAH SEBELUM VALIDASI & SUBMIT
                                        $('#harga_total').val(rawValue);

                                        // 4. Lanjutkan dengan validasi jQuery
                                        if ($('#myForm').valid()) {
                                            Swal.fire({
                                                title: 'Apakah Anda yakin?',
                                                text: "Perubahan akan disimpan!",
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#3085d6',
                                                cancelButtonColor: '#d33',
                                                confirmButtonText: 'Ya, simpan perubahan!'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $('#myForm').submit(); // Submit nilai mentah
                                                } else {
                                                    // Jika batal, kembalikan format tampilan
                                                    $('#harga_total').val(currentFormattedValue);
                                                }
                                            });
                                        } else {
                                            // Jika validasi gagal, kembalikan format tampilan
                                            $('#harga_total').val(currentFormattedValue);
                                        }
                                    });
                                });
                            </script>

                            <div class="row mb-3">
                                <label for="foto" class="col-sm-2 col-form-label">Foto Barang <span class="text-danger">*</span></label>
                                <div class="form-group col-sm-10">
                                    @if($barang->foto_barang)
                                        <p class="text-success">Foto saat ini:</p>
                                        <div class="mt-2">
                                            <!-- Menggunakan path lengkap dari barang->foto -->
                                            <img src="{{ asset('storage/' . $barang->foto_barang) }}" alt="Foto Barang" class="img-fluid" style="max-width: 200px;">
                                        </div>
                                    @else
                                        <p class="text-warning">Belum ada foto</p>
                                    @endif

                                    <input name="foto" class="form-control mt-2" type="file" id="foto" accept=".jpg,.jpeg,.png">
                                    <small class="form-text text-muted">Usahakan gambar dalam bentuk PNG, JPEG atau JPG untuk hasil yang lebih baik.</small>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" id="editBtn" class="btn btn-info waves-effect waves-light">Edit Barang</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const satuanSelect = document.getElementById('satuan');
        const satuanBaruContainer = document.getElementById('satuanBaruContainer');
        const satuanBaruInput = document.getElementById('satuanBaru');
        const form = document.getElementById('myForm');

        satuanSelect.addEventListener('change', function() {
            if (this.value === 'lainnya') {
                satuanBaruContainer.style.display = 'block'; // Show the input and label
                satuanBaruInput.disabled = false; // Enable input field
            } else {
                satuanBaruContainer.style.display = 'none'; // Hide the input and label
                satuanBaruInput.disabled = true;  // Disable input field
                satuanBaruInput.value = '';       // Clear input field
            }
        });

        form.addEventListener('submit', function() {
            if (satuanSelect.value === 'lainnya') {
                const satuanBaruValue = satuanBaruInput.value;
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'satuanBaru';
                hiddenInput.value = satuanBaruValue;
                form.appendChild(hiddenInput);
            }
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function (){
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('editBtn').addEventListener('click', function() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Perubahan akan disimpan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ya, simpan perubahan!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna mengkonfirmasi, submit form secara manual
                document.getElementById('myForm').submit();
            }
        });
    });
</script>

@endsection
