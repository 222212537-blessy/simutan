@extends(auth()->user()->role === 'admin' ? 'admin.admin_master' : 
         (auth()->user()->role === 'supervisor' ? 'supervisor.supervisor_master' : 
         'pegawai.pegawai_master'))

@section(auth()->user()->role === 'admin' ? 'admin' : 
         (auth()->user()->role === 'supervisor' ? 'supervisor' : 'pegawai'))

<head>
    <title>
        Lihat Permintaan | SIMUTAN
    </title>
</head>


<div class="page-content">
    <div class="container-fluid">
        <!-- Start Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 text-info">Permintaan {{ $permintaan->no_permintaan }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('permintaan.all') }}">Permintaan</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('permintaan.all') }}">View Permintaan</a></li>
                            <li class="breadcrumb-item active">Permintaan {{ $permintaan->no_permintaan }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Title -->

        <!-- Approval Information -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Permintaan</h5>
                        <hr>

                        <!-- Display Permintaan Details -->
                        {{-- <div class="row mb-4">
                            <div class="col-sm-6">
                                <p><strong>Nomor Permintaan     :</strong> {{ $permintaan->no_permintaan }}</p>
                                <p><strong>Nama Pegawai         :</strong> {{ $permintaan->user->name }}</p>
                                <p><strong>Tanggal Permintaan   :</strong> {{ $permintaan->tgl_request }}</p>
                                <p><strong>Status               :</strong> {{ $permintaan->status }}</p>
                                <p><strong>Deskripsi            :</strong> {{ $permintaan->pilihan->first()->description }}</p>
                                <p><strong>Catatan Admin        :</strong> {{ $permintaan->ctt_adm }}</p>
                                <p><strong>Catatan Supervisor   :</strong> {{ $permintaan->ctt_spv }}</p>
                            </div>
                        </div> --}}

                        <table class="table table-centered mb-0 align-middle" style="border: 1px solid white;">
                            <tr>
                                <td style="white-space: nowrap; width: 1%; min-width: 200px;"><strong>Nomor Permintaan</strong></td>
                                <td>{{ $permintaan->no_permintaan }}</td>
                            </tr>
                            <tr>
                                <td style="white-space: nowrap; width: 1%; min-width: 200px;"><strong>Nama Pegawai</strong></td>
                                <td>{{ $permintaan->user->name ?? 'Pegawai SIMUTAN' }}</td>
                            </tr>
                            <tr>
                                <td style="white-space: nowrap; width: 1%; min-width: 200px;"><strong>Tanggal Permintaan</strong></td>
                                <td>{{ $permintaan->tgl_request }}</td>
                            </tr>
                            <tr>
                                <td style="white-space: nowrap; width: 1%; min-width: 200px;"><strong>Approval Admin</strong></td>
                                <td style="white-space: nowrap;">
                                    @if($permintaan->status == 'pending')
                                        <button class="btn btn-secondary text-gray btn-sm font-size-13" style="border: 1px solid #505D69; color: #6b7280; background-color:#edeef0; pointer-events: none; cursor: not-allowed; margin-bottom: 0.5rem; opacity:0.7; padding: .1rem .25rem;">Pending</button>                    
                                    @elseif(($permintaan->status == 'rejected by admin') || ($permintaan->status == 'rejected by supervisor' && $permintaan->ctt_adm !== NULL))
                                    <button class="btn btn-secondary text-danger btn-sm font-size-13" style="border: 1px solid #F32F53; pointer-events: none; background-color: #feeef1; cursor: not-allowed; margin-bottom: 0.5rem; opacity:0.8; padding: .1rem .25rem;">Rejected</button>
                                    @elseif($permintaan->status == 'approved by admin' || ($permintaan->status == 'rejected by supervisor' && $permintaan->ctt_adm == NULL) || $permintaan->status == 'approved by supervisor')
                                    <button class="btn btn-secondary text-success btn-sm font-size-13" style="border: 1px solid #46cf74; background-color:#f3fbf5; pointer-events: none; cursor: not-allowed; margin-bottom: 0.5rem; opacity:0.8; padding: .1rem .25rem;">Approved</button>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space: nowrap; width: 1%; min-width: 200px;"><strong>Approval Supervisor</strong></td>
                                <td style="white-space: nowrap;">
                                        @if($permintaan->status == 'approved by admin' || $permintaan->status == 'pending')
                                        <button class="btn btn-secondary text-gray btn-sm font-size-13" style="border: 1px solid #505D69; color: #6b7280; background-color:#edeef0; pointer-events: none; cursor: not-allowed; margin-bottom: 0.5rem; opacity:0.7; padding: .1rem .25rem;">Pending</button>
                                        @elseif($permintaan->status == 'rejected by supervisor' || $permintaan->status == 'rejected by admin')
                                            <button class="btn btn-secondary text-danger btn-sm font-size-13" style="border: 1px solid #F32F53; pointer-events: none; background-color: #feeef1; cursor: not-allowed; margin-bottom: 0.5rem; opacity:0.8; padding: .1rem .25rem;">Rejected</button>
                                        @elseif($permintaan->status == 'approved by supervisor')
                                        <button class="btn btn-secondary text-success btn-sm font-size-13" style="border: 1px solid #46cf74; background-color:#f3fbf5; pointer-events: none; cursor: not-allowed; margin-bottom: 0.5rem; opacity:0.8; padding: .1rem .25rem;">Approved</button>                                       
                                        @endif
                                    </td>
                            </tr>
                            <tr>
                                <td style="white-space: nowrap; width: 1%; min-width: 200px;"><strong>Deskripsi</strong></td>
                                <td>{{ optional($permintaan->pilihan->first())->description ?? 'Tidak ada deskripsi/catatan.' }}</td>
                            </tr>
                            <tr>
                                <td style="white-space: nowrap; width: 1%; min-width: 200px;"><strong>Catatan Admin</strong></td>
                                <td>{{ $permintaan->ctt_adm ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td style="white-space: nowrap; width: 1%; min-width: 200px;"><strong>Catatan Supervisor</strong></td>
                                <td>{{ $permintaan->ctt_spv ?: '-' }}</td>
                            </tr>
                        </table>
                        <br>

                        <!-- Display Pilihan Details -->
                        <h5 class="card-title">Detail Barang Permintaan</h5>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-centered mb-0 align-middle table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Kuantitas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pilihan as $item)
                                        <tr>
                                            @if($item->barang)
                                                <td>{{ $item->barang->nama }}</td>
                                                <td style="white-space: nowrap; width: 1%; min-width: 400px;">{{ $item->req_qty }} {{ $item->barang->satuan }}</td>
                                            @else
                                                <td>Barang Telah dihapus</td>
                                                <td style="white-space: nowrap; width: 1%; min-width: 400px;">Barang Telah dihapus</td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada barang terpilih</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Approval Actions -->
                        <div class="mt-4 d-flex justify-content-end">
                            <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
