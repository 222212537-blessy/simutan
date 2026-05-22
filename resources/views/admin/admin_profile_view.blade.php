@extends('admin.admin_master')
@section('admin')

<head>
    <title>
        Lihat Profile | SIMUTAN
    </title>
</head>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <center>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <center>
                                @if(Auth::user()->id == 1)
                                    <img class="rounded-circle" 
                                         style="object-fit: cover; object-position: center top 10%; transform: translateY(-10px); height: 150px; width: 150px; border: 1.5px solid lightgray;" 
                                         src="{{ !empty($adminData->foto) ? url($adminData->foto) : url('upload/no_image.jpg') }}"
                                         alt="Header Avatar">
                                @else
                                    <img class="rounded-circle" 
                                    style="object-fit: cover; object-position: top center; height: 150px; width: 150px; border: 1.5px solid lightgray" 
                                         src="{{ !empty($adminData->foto) ? url($adminData->foto) : url('upload/no_image.jpg') }}"
                                         alt="Header Avatar">
                                @endif
                            </center>
                            <hr>
                            <h4 class="card-title">{{ $adminData->name }} </h4>
                            <p>{{ $adminData->username }} </p> 
                            <button class="btn btn-secondary bg-primary text-info btn-sm font-size-16" 
                                    style="border: 1px solid #043277; pointer-events: none; cursor: not-allowed; margin-bottom: 0.5rem; opacity:0.7; padding: .1rem .5rem; text-transform: capitalize;">
                                {{ $adminData->role }}
                            </button>
                            <br><br>
                            <hr>
                            <center>
                                <a href="{{ route('user.edit', Auth::user()->id) }}" 
                                   class="btn btn-info btn-rounded waves-effect waves-light">Edit Profile</a>
                            </center>
                        </div>
                    </div>
                </div> 
            </center>                   
        </div> 
    </div>
</div>

@endsection
