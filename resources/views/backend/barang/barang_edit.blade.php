@extends(auth()->user()->role === 'admin' ? 'admin.admin_master' : (auth()->user()->role === 'supervisor' ? 'supervisor.supervisor_master' : 'pegawai.pegawai_master'))
@section(auth()->user()->role === 'admin' ? 'admin' : (auth()->user()->role === 'supervisor' ? 'supervisor' : 'pegawai'))

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

                            <input type="hidden" name="id" value="{{ $barang->id }}">
                            <input type="hidden" name="existing_foto" value="{{ $barang->foto_barang }}"> <!-- Menyimpan nama foto yang ada -->

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
                                    <select id="kelompok_select" name="kelompok_id" class="form-select" required>
                                        <option selected="" disabled>Pilih jenis kelompok barang</option>
                                        @foreach($kelompok as $kel)
                                        <option value="{{$kel->id}}" {{ $kel->id == $barang->kelompok_id ? 'selected' : '' }}>{{$kel->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="kategori_barang" class="col-sm-2 col-form-label">Kategori Barang <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select id="kategori_select" name="kategori_id" class="form-select" required>
                                        <option selected disabled>Pilih kategori barang</option>
                                        @foreach($kategori as $kat)
                                            <option value="{{ $kat->id }}" {{ $kat->id == $barang->kategori_id ? 'selected' : '' }}>{{ $kat->nama }}</option>
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

                                document.addEventListener('DOMContentLoaded', function() {
                                    const satuanSelect = document.getElementById('satuan');
                                    const satuanBaruContainer = document.getElementById('satuanBaruContainer');
                                    const satuanBaruInput = document.getElementById('satuanBaru');
                                    const kelompokSelect = document.getElementById('kelompok_select');
                                    const kategoriSelect = document.getElementById('kategori_select');
                                    const form = document.getElementById('myForm');
                                    const kategoriUrl = '{{ url('pilihan/get-kategori') }}';
                                    const selectedKategoriId = '{{ $barang->kategori_id }}';

                                    function loadKategoriOptions(kelompokId, selectedKategori = null) {
                                        kategoriSelect.innerHTML = '<option disabled selected>Mengambil kategori...</option>';

                                        fetch(`${kategoriUrl}/${kelompokId}`)
                                            .then(response => response.json())
                                            .then(data => {
                                                if (!Array.isArray(data) || data.length === 0) {
                                                    kategoriSelect.innerHTML = '<option disabled selected>Tidak ada kategori untuk kelompok ini</option>';
                                                    return;
                                                }

                                                kategoriSelect.innerHTML = '<option disabled selected>Pilih kategori barang</option>';

                                                data.forEach(kategori => {
                                                    const option = document.createElement('option');
                                                    option.value = kategori.id;
                                                    option.textContent = kategori.nama;
                                                    if (selectedKategori && kategori.id == selectedKategori) {
                                                        option.selected = true;
                                                    }
                                                    kategoriSelect.appendChild(option);
                                                });
                                            })
                                            .catch(() => {
                                                kategoriSelect.innerHTML = '<option disabled selected>Gagal memuat kategori</option>';
                                            });
                                    }

                                    satuanSelect.addEventListener('change', function() {
                                        if (this.value === 'lainnya') {
                                            satuanBaruContainer.style.display = 'block';
                                            satuanBaruInput.disabled = false;
                                        } else {
                                            satuanBaruContainer.style.display = 'none';
                                            satuanBaruInput.disabled = true;
                                            satuanBaruInput.value = '';
                                        }
                                    });

                                    kelompokSelect.addEventListener('change', function() {
                                        const kelompokId = this.value;
                                        if (kelompokId) {
                                            loadKategoriOptions(kelompokId);
                                        }
                                    });

                                    // previous harga_total strip on submit removed; handled in submitHandler

                                    if (kelompokSelect.value) {
                                        loadKategoriOptions(kelompokSelect.value, selectedKategoriId);
                                    }

                                    // Pure JS form submit handler (HTML5 validation + SweetAlert confirmation)
                                    const submitHandler = function(event) {
                                        event.preventDefault();
                                        const hargaEl = document.getElementById('harga_total');
                                        const currentFormattedValue = hargaEl.value;
                                        const rawValue = currentFormattedValue.replace(/\./g, '');

                                        // Basic required fields validation (matches previous rules)
                                        const requiredNames = ['nama', 'kelompok_id', 'kategori_id', 'kode_barang', 'satuan'];
                                        let valid = true;
                                        for (let name of requiredNames) {
                                            const el = document.querySelector('[name="' + name + '"]');
                                            if (!el) continue;
                                            // for select, check selected value
                                            if (el.tagName === 'SELECT') {
                                                if (!el.value) { valid = false; el.classList.add('is-invalid'); }
                                                else el.classList.remove('is-invalid');
                                            } else {
                                                if (!el.value || el.value.trim() === '') { valid = false; el.classList.add('is-invalid'); }
                                                else el.classList.remove('is-invalid');
                                            }
                                        }

                                        if (!valid) {
                                            // show native validation UI
                                            if (typeof form.reportValidity === 'function') {
                                                form.reportValidity();
                                            }
                                            return;
                                        }

                                        Swal.fire({
                                            title: 'Apakah Anda yakin?',
                                            text: 'Perubahan akan disimpan!',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Ya, simpan perubahan!'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                // set raw value and submit
                                                hargaEl.value = rawValue;
                                                form.submit();
                                            } else {
                                                hargaEl.value = currentFormattedValue;
                                            }
                                        });
                                    };

                                    // Attach pure JS submit handler to the form
                                    form.removeEventListener('submit', submitHandler);
                                    form.addEventListener('submit', submitHandler);
                                });
                            </script>

                            <div class="row mb-3">
                                <label for="foto" class="col-sm-2 col-form-label">Foto Barang <span class="text-danger">*</span></label>
                                <div class="form-group col-sm-10">
                                    @if($barang->foto_barang && file_exists(public_path($barang->foto_barang)))
                                        <p class="text-success mb-1">Foto lama:</p>
                                        <p class="text-muted small">{{ basename($barang->foto_barang) }}</p>
                                        <div class="mt-2">
                                            <img src="{{ asset($barang->foto_barang) }}" alt="Foto Barang" class="img-fluid" style="max-width: 200px;">
                                        </div>
                                    @else
                                        <p class="text-warning">
                                            @if($barang->foto_barang)
                                                Foto lama: <strong>{{ basename($barang->foto_barang) }}</strong> (File tidak ditemukan - silakan upload ulang)
                                            @else
                                                Belum ada foto
                                            @endif
                                        </p>
                                    @endif

                                    <input name="foto" class="form-control mt-2" type="file" id="foto" accept=".jpg,.jpeg,.png">
                                    <small class="form-text text-muted">Usahakan gambar dalam bentuk PNG, JPEG atau JPG untuk hasil yang lebih baik.</small>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" id="editBtn" class="btn btn-info waves-effect waves-light">Edit Barang</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
