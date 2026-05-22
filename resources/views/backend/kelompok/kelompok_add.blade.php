@extends('admin.admin_master')
@section('admin')

<head>
    <title>
        Tambah Kelompok Barang | SIMUTAN
    </title>
</head>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 text-info">Penambahan Kelompok Barang</h4>
            
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Barang</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Kelompok Barang</a></li>
                            <li class="breadcrumb-item active">Tambah Kelompok Barang</li>
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
                        <h4 class="card-title">Tambah Kelompok Barang</h4><br>
                        
                        <form method="post" action="{{ route('kelompok.store') }}" id="myForm">
                            @csrf

                            <div class="row mb-3">
                                <label for="nama" class="col-sm-3 col-form-label">Nama Kelompok Barang <span class="text-danger">*</span></label>
                                <div class="form-group col-sm-9">
                                    <input name="nama" class="form-control" type="text" id="nama" required>                    
                                </div>
                            </div> 

                            <div class="row mb-3">
                                <label for="deskripsi" class="col-sm-3 col-form-label">Deskripsi Kelompok Barang</label>
                                <div class="form-group col-sm-9">
                                    <textarea name="deskripsi" class="form-control" id="deskripsi" rows="4"></textarea>                    
                                </div>
                            </div> 
                        
                            <div class="d-flex justify-content-end">
                                <input type="submit" class="btn btn-info waves-effect waves-light" value="Tambahkan Kelompok Barang">
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
                nama: {
                    required : true,
                }, 
            },
            errorElement : 'span', 
            errorPlacement: function (error,element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight : function(element, errorClass, validClass){
                $(element).addClass('is-invalid');
            },
            unhighlight : function(element, errorClass, validClass){
                $(element).removeClass('is-invalid');
            },
        });
    });
</script>

@endsection
