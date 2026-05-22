<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Satuan;


class DefaultController extends Controller
{
    public function getCategory(Request $request)
    {
        $kelompok_id = $request->kelompok_id;

        try {
            $allCategory = Barang::where('kelompok_id', $kelompok_id)
                                 ->select('id', 'nama', 'qty_item') // Pastikan untuk memilih kolom qty_item
                                 ->get();
            return response()->json($allCategory);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getSatuan(Request $request)
    {
        $barang_id = $request->input('barang_id');
    
        // Ambil satuan dari tabel barang
        $barang = Barang::where('id', $barang_id)->first(['satuan', 'qty_item']);
        
        if (!$barang) {
            return response()->json(['error' => 'Barang tidak ditemukan'], 404);
        }
    
        return response()->json([
            'satuan' => $barang->satuan,
            'qty_item' => $barang->qty_item
        ]);
    }
    


}
