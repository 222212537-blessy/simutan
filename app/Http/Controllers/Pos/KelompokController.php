<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelompok;
use Auth;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;




class KelompokController extends Controller
{
    public function KelompokAll(){
        $kelompoks = Kelompok::latest()->get();
        return view('backend.kelompok.kelompok_all', compact('kelompoks'));
    } // End Method

    public function KelompokAdd(){
        return view('backend.kelompok.kelompok_add');
    } // End Method

    public function KelompokStore(Request $request){
        Kelompok::insert([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            // 'created_by' => Auth::user()->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    
        $notification = array(
            'message' => "Kelompok Barang berhasil ditambahkan.",
            'alert-type' => "Success"
        );

        return redirect()->route('kelompok.all')->with($notification);
    }

    public function KelompokEdit($id){
        $kelompok = Kelompok::findOrFail($id);
        return view('backend.kelompok.kelompok_edit', compact('kelompok'));
    }

    public function KelompokUpdate(Request $request, $id)
    {
        // Validate request
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
        ]);
    
        // Find the kelompok and update
        $kelompok = Kelompok::findOrFail($id);
        $kelompok->update([
            'nama' => $validatedData['nama'],
            'deskripsi' => $validatedData['deskripsi'] ?? $kelompok->deskripsi,
            'updated_at' => Carbon::now()
        ]);
    
        // Redirect with success notification
        return redirect()->route('kelompok.all')->with([
            'message' => 'Kelompok Barang berhasil di update',
            'alert-type' => 'success'
        ]);
    }

    public function KelompokDelete($id){
        Kelompok::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Kelompok Barang berhasil dihapus',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

public function data()
{
    $kelompoks = Kelompok::select(['id', 'nama', 'deskripsi']); // Specify the columns you want

    return DataTables::of($kelompoks)
    ->addColumn('action', function ($kelompok) {
        return '<div class="table-actions" style="text-align: center; vertical-align: middle;">
                    <a href="'.route('kelompok.edit', $kelompok->id).'" class="btn btn-sm hover:bg-warning" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #e1a017; padding: 15px;" data-tooltip="Edit Permintaan">
                        <i class="ti ti-edit font-size-20 align-middle"></i>
                    </a>
                    <a href="'.route('kelompok.delete', $kelompok->id).'" class="btn btn-sm text-danger hover:bg-danger" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: red; padding: 15px;" data-tooltip="Hapus Permintaan">
                        <i class="ti ti-trash font-size-20 align-middle text-danger"></i>
                    </a>
                   
                </div>';
    })
    
        ->addColumn('action', function ($kelompok) {
            return '<div class="table-actions" style="text-align: center; vertical-align: middle;">
                        <a href="'.route('kelompok.edit', $kelompok->id).'" class="btn bg-warning btn-sm">
                            <i class="fas fa-edit" style="color: #ca8a04"></i>
                        </a>
                        <a href="'.route('kelompok.delete', $kelompok->id).'" class="btn bg-danger btn-sm">
                            <i class="fas fa-trash-alt text-danger"></i>
                        </a>
                    </div>';
        })
        ->make(true);
}


}
