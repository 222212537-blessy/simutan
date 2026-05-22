@extends(auth()->user()->role === 'admin' ? 'admin.admin_master' : 
         (auth()->user()->role === 'supervisor' ? 'supervisor.supervisor_master' : 
         'pegawai.pegawai_master'))

@section(auth()->user()->role === 'admin' ? 'admin' : 
         (auth()->user()->role === 'supervisor' ? 'supervisor' : 'pegawai'))

<head>
    <title>
        Edit User | SIMUTAN
    </title>
</head>
         
         <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
         <script src="https://cdn.jsdelivr.net/npm/handlebars@4.7.7/dist/handlebars.min.js"></script>
         <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">Edit Profile Pengguna</h4><br><br>
                        
                        <form method="post" action="{{ route('user.update', $user->id) }}" id="myForm" enctype="multipart/form-data">
                            @csrf
                            @method('PUT') <!-- Use PUT method for updating resources -->

                            <input type="hidden" name="id" value="{{ $user->id }}">

                            <div class="row mb-3">
                                <label for="name" class="col-sm-2 col-form-label">Nama Pengguna</label>
                                <div class="form-group col-sm-10">
                                    <input name="name" value="{{ $user->name }}" class="form-control" type="text" id="name" readonly>
                                </div>
                            </div>
                        
                            <div class="row mb-3">
                                <label for="username" class="col-sm-2 col-form-label">Username</label>
                                <div class="form-group col-sm-10">
                                    <input name="username" value="{{ $user->username }}" class="form-control" type="text" id="username" readonly>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="panggilan" class="col-sm-2 col-form-label">Panggilan</label>
                                <div class="form-group col-sm-10">
                                    <input name="panggilan" value="{{ $user->panggilan }}" class="form-control" type="text" id="username">
                                </div>
                            </div>
                        
                            <div class="row mb-3">
                                <label for="role" class="col-sm-2 col-form-label">Role</label>
                                <div class="col-sm-10">
                                    <select name="role" class="form-select" aria-label="Default select example" required>
                                        <option value="" disabled>Pilih role untuk User</option>
                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="supervisor" {{ $user->role == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                        <option value="pegawai" {{ $user->role == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Display existing images -->
                            <div class="row mb-4">
                                <label class="col-sm-2 col-form-label">Foto</label>
                                <div class="col-sm-10">
                                    <div class="d-flex align-items-start">
                                        <!-- Existing Image -->
                                        <div class="me-4">
                                            @if($user->foto)
                                                <p class="text-success">Foto saat ini:</p>
                                                <div class="mt-2">
                                                    <!-- Menggunakan path lengkap dari user->foto -->
                                                    <img id="foto-preview" src="{{ asset('storage/' . $user->foto) }}" alt="Foto Pengguna" class="img-fluid" style="max-width: 200px;">
                                                </div>
                                            @else
                                                <p class="text-warning">Belum ada foto</p>
                                            @endif
                                        </div>
                                        <!-- File Input -->
                                        <div class="flex-grow-1">
                                            <input name="image" class="form-control mb-2" type="file" id="image" accept="image/png">
                                            <small class="form-text text-muted">Ketentuan: File harus berupa PNG dan background telah di-remove.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label class="col-sm-2 col-form-label">Tanda Tangan</label>
                                <div class="col-sm-10">
                                    <div class="d-flex align-items-start">
                                        
                                        <!-- Existing Signature -->
                                        <div class="me-4">
                                            @if($user->ttd)
                                                <p class="text-success">Tanda tangan saat ini:</p>
                                                <div class="mt-2">
                                                    <!-- Menggunakan path lengkap dari user->signature -->
                                                    <img id="ttd-preview" src="{{ asset('storage/' . $user->ttd) }}" alt="Tanda Tangan Pengguna" class="img-fluid" style="max-width: 200px;">
                                                </div>
                                            @else
                                                <p class="text-warning">Belum ada tanda tangan</p>
                                            @endif
                                        </div>
                                        <!-- File Input -->
                                        <div class="flex-grow-1">
                                            <input name="signature" class="form-control mb-2" type="file" id="signature" accept="image/png">
                                            <small class="form-text text-muted">Ketentuan: File harus berupa PNG.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <input type="submit" class="btn btn-info waves-effect waves-light" value="Edit Pengguna">
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div> <!-- end col -->
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function (){
        // Validasi Form
        $('#myForm').validate({
            rules: {
                role: {
                    required : true,
                },
            },
            messages: {
                role: {
                    required: "Role harus dipilih.",
                }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
        });

        // Image preview functionality for photo
        $('#image').change(function(){
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#foto-preview').attr('src', e.target.result);
            }
            // Ensure there is a file selected
            if (this.files && this.files[0]) {
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Image preview functionality for signature
        $('#signature').change(function(){
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#ttd-preview').attr('src', e.target.result);
            }
            // Ensure there is a file selected
            if (this.files && this.files[0]) {
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>

@endsection
