<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class WizardController extends Controller
{
    public function index()
    {
        // Ambil data yang diperlukan untuk form (misalnya, data kelompok barang)
        $kelompok = \App\Models\Kelompok::all(); // Sesuaikan model dengan model kelompok barang Anda
        return view('wizard.index', compact('kelompok'));
    }

    public function submit(Request $request)
    {
        // Validasi data yang diterima dari form
        $validatedData = $request->validate([
            'nama_pengaju' => 'required|string|max:255',
            'nip' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'catatan' => 'nullable|string|max:225',
            'kelompok_id' => 'required|integer',
            'barang_id' => 'required|integer',
            'req_qty' => 'required|integer|min:1'
        ]);

        // Lakukan proses penyimpanan atau logika lainnya dengan data yang sudah divalidasi
        // ...

        // Redirect atau berikan respon setelah proses selesai
        return redirect()->route('wizard.index')->with('success', 'Pengajuan permintaan berhasil dikirim.');
    }
}