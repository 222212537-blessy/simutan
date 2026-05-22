@extends('admin.admin_master')
@section('admin')

<head>
    <title>
        Edit Kelompok Barang | SIMUTAN
    </title>
</head>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Halaman Edit Kelompok Barang</h4>
                        <form method="post" action="{{ route('kelompok.update', $kelompok->id) }}" id="myForm">
                            @csrf
                            @method('PUT') <!-- Menandakan bahwa ini adalah request untuk update -->
                            
                            <div class="row mb-3">
                                <label for="nama" class="col-sm-3 col-form-label">Nama Kelompok Barang <span class="text-danger">*</span></label>
                                <div class="form-group col-sm-9">
                                    <input name="nama" value="{{ old('nama', $kelompok->nama) }}" class="form-control" type="text" id="nama" required>                    
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label for="deskripsi" class="col-sm-3 col-form-label">Deskripsi Kelompok Barang</label>
                                <div class="form-group col-sm-9">
                                    <textarea name="deskripsi" class="form-control" id="deskripsi" rows="4">{{ old('deskripsi', $kelompok->deskripsi) }}</textarea>                    
                                </div>
                            </div> 
                        
                            <div class="d-flex justify-content-end">
                                <input type="submit" class="btn btn-info waves-effect waves-light" value="Update Kelompok Barang">
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#myForm').validate({
            rules: {
                nama: {
                    required: true,
                },
                deskripsi: {
                    maxlength: 500, // Contoh batasan maksimal karakter untuk deskripsi
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
