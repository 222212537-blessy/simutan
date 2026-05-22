@extends('admin.admin_master')
@section('admin')

<head>
    <title>
        Tambah User | SIMUTAN
    </title>
</head>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/additional-methods.min.js"></script>

<style>
    .required::after {
        content: "*";
        color: red;
        margin-left: 0.2em;
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 text-info">Tambah Pengguna</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Pengguna</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Seluruh Pengguna</a></li>
                            <li class="breadcrumb-item active">Tambah Pengguna</li>
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
                        <h4 class="card-title">Tambah Pengguna</h4><br>
                        <form method="post" action="{{ route('user.store') }}" id="myForm" enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-3">
                                <label for="name" class="col-sm-2 col-form-label required">Nama Pengguna</label>
                                <div class="form-group col-sm-10">
                                    <input name="name" class="form-control" type="text" id="name" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="panggilan" class="col-sm-2 col-form-label required">Nama Panggilan</label>
                                <div class="form-group col-sm-10">
                                    <input name="panggilan" class="form-control" type="text" id="panggilan" required>
                                </div>
                            </div>
                        
                            <div class="row mb-3">
                                <label for="username" class="col-sm-2 col-form-label required">Username</label>
                                <div class="form-group col-sm-10">
                                    <input name="username" class="form-control" type="text" id="username" required>
                                </div>
                            </div>
                        
                            <div class="row mb-3">
                                <label for="role" class="col-sm-2 col-form-label required">Role</label>
                                <div class="col-sm-10">
                                    <select name="role" class="form-select" aria-label="Default select example" id="role" required>
                                        <option value="" selected disabled>Pilih role untuk User</option>
                                        <option value="admin">Admin</option>
                                        <option value="supervisor">Supervisor</option>
                                        <option value="pegawai">Pegawai</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Image Upload Field -->
                            <div class="row mb-3">
                                <label for="image" class="col-sm-2 col-form-label">Upload Gambar</label>
                                <div class="form-group col-sm-10">
                                    <input name="image" class="form-control" type="file" id="image" accept="image/png">
                                    <small class="form-text text-muted">Ketentuan: File harus berupa PNG dan background telah di-remove.</small>
                                </div>
                            </div>

                            <!-- Signature Upload Field -->
                            <div class="row mb-3">
                                <label for="signature" class="col-sm-2 col-form-label">Upload Tanda Tangan</label>
                                <div class="form-group col-sm-10">
                                    <input name="signature" class="form-control" type="file" id="signature" accept="image/png">
                                    <small class="form-text text-muted">Ketentuan: File harus berupa PNG.</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <input type="submit" class="btn btn-info waves-effect waves-light" value="Tambahkan Pengguna">
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
        $('#myForm').validate({
            rules: {
                name: {
                    required : true,
                },
                username: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true
                },
                role: {
                    required: true,
                }
            },
            messages: {
                name: {
                    required: "Nama pengguna harus diisi.",
                },
                username: {
                    required: "Username harus diisi.",
                },
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
    });
</script>
@endsection

