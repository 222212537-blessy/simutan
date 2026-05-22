<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pilihan;
use App\Models\Satuan;
use App\Models\Barang;
use App\Models\Kelompok;
use App\Models\Kategori;
use App\Models\User;
use App\Models\Notification;
use App\Models\Permintaan;
use App\Models\Pengeluaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\DB;

class PilihanController extends Controller
{
    public function BarangAll(){
        // FIX TYPO COMPACT: Menyamakan nama variabel penyimpan data master
        $barangs = Barang::latest()->get();
        return view('backend.barang.barang_all', compact('barangs'));
    } 

    public function barangAdd(){
        $kelompok = Kelompok::all();
        return view('backend.barang.barang_add', compact('kelompok'));
    } 

    public function barangStore(Request $request){
        Barang::insert([
            'nama' => $request->nama,
            'kelompok_id' => $request->kelompok_id,
            'qty_item' => $request->qty_item,
            'satuan' => $request->satuan,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    
        $notification = array(
            'message' => "Barang berhasil ditambahkan.",
            'alert-type' => "success"
        );

        return redirect()->route('barang.all')->with($notification);
    }

    public function barangEdit($id){
        $kelompok = Kelompok::all();
        $barang = Barang::findOrFail($id);
        return view('backend.barang.barang_edit', compact('barang','kelompok'));
    }

    public function barangUpdate(Request $request){
        $barang_id = $request->id;

        Barang::findOrFail($barang_id)->update([
            'nama' => $request->nama,
            'kelompok_id' => $request->kelompok_id,
            'qty_item' => $request->qty_item,
            'satuan' => $request->satuan,
            'updated_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Barang berhasil di update',
            'alert-type' => 'success'
        );

        return redirect()->route('barang.all')->with($notification);
    }

    public function barangDelete($id){
        Barang::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Barang berhasil dihapus',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function PilihanAll()
    {
        // 1. Ambil data pilihan seperti biasa
        $pilihans = Pilihan::orderBy('date', 'desc')->get(); 
        
        // 2. SUNTIKKAN data kelompok di sini agar view pilihan_add tidak mengamuk
        $kelompok = Kelompok::all(); 
        
        // 3. Tambahkan 'kelompok' ke dalam compact()
        return view('backend.pilihan.pilihan_add', compact('pilihans', 'kelompok')); 
    }

    public function PilihanAdd()
    {
        $barang = Barang::all();
        $permintaan = Permintaan::all();
        $kelompok = Kelompok::all();
        return view('backend.pilihan.pilihan_add', compact('barang','permintaan','kelompok'));
    }

    public function PermintaanStore(Request $request)
    {
        $lastPermintaan = Permintaan::orderBy('id', 'desc')->first();
        $lastNoPermintaan = $lastPermintaan ? $lastPermintaan->no_permintaan : null;
    
        if ($lastNoPermintaan) {
            $lastDigit = (int) substr($lastNoPermintaan, 2, 4);
            $digitPertama = $lastDigit + 1; 
        } else {
            $digitPertama = 1; 
        }
        
        $bulan = Carbon::now()->format('m');
        $tahun = Carbon::now()->format('Y');
        $noPermintaan = "B-{$digitPertama}/31751/PL.711/{$bulan}/{$tahun}";
        
        // AMBIL INPUT DARI HIDDEN FIELD FRONT-END KATALOG
        $tanggalRequest = $request->input('hidden_date') ? $request->input('hidden_date') : Carbon::now()->format('Y-m-d');
        
        $permintaan = Permintaan::create([
            'user_id' => Auth::user()->id,
            'no_permintaan' => $noPermintaan,
            'tgl_request' => $tanggalRequest, // Menyimpan tanggal asli pilihan user
            'status' => 'pending',
            'ctt_adm' => null,
            'ctt_spv' => null, 
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    
        $rolesToNotify = ['admin', 'supervisor'];
        $usersToNotify = User::whereIn('role', $rolesToNotify)->get();
    
        foreach ($usersToNotify as $user) {
            $notificationMessage = 'Terdapat permintaan baru dari ' . Auth::user()->name . '.';
            Notification::create([
                'user_id' => $user->id,
                'permintaan_id' => $permintaan->id,
                'message' => $notificationMessage,
            ]);
        }
    
        return $permintaan->id;
    }
    
    public function PilihanStore(Request $request)
    {
        $tableData = $request->input('table_data');
        $date = $request->input('hidden_date') ?: Carbon::now()->format('Y-m-d');
        $description = trim($request->input('hidden_description') ?? '');

        if (empty($tableData)) {
            return redirect()->back()->with([
                'message' => 'Data laci keranjang permintaan kosong!',
                'alert-type' => 'error'
            ]);
        }

        $tableData = json_decode($tableData, true);
        if (!is_array($tableData) || count($tableData) === 0) {
            return redirect()->back()->with([
                'message' => 'Data tabel permintaan tidak valid.',
                'alert-type' => 'error'
            ]);
        }

        Log::info('[DEBUG PILIHAN STORE] Payload table_data decoded', ['table_data' => $tableData, 'date' => $date, 'description' => $description, 'user_id' => Auth::id()]);

        DB::beginTransaction();
        try {
            $permintaanId = $this->PermintaanStore($request);
            Log::info('[DEBUG PILIHAN STORE] Created permintaan', ['permintaan_id' => $permintaanId]);

            foreach ($tableData as $index => $item) {
                $requested_qty = isset($item['qty_req']) ? (int) $item['qty_req'] : 0;
                if ($requested_qty <= 0) {
                    Log::warning('[DEBUG PILIHAN STORE] Ignored item with invalid qty_req', ['item' => $item]);
                    continue;
                }

                $barang = null;
                if (!empty($item['id'])) {
                    $barang = Barang::find($item['id']);
                }

                if (!$barang && !empty($item['barang_nama'])) {
                    $barang = Barang::where('nama', trim($item['barang_nama']))->first();
                }

                if (!$barang) {
                    Log::warning('[DEBUG PILIHAN STORE] Barang tidak ditemukan', ['item' => $item]);
                    continue;
                }

                $hargaSatuan = 0;
                if ($barang->qty_item > 0 && $barang->harga_total > 0) {
                    $hargaSatuan = $barang->harga_total / $barang->qty_item;
                }
                $total_harga_pilihan = (int) round($requested_qty * $hargaSatuan);

                $pilihan = Pilihan::create([
                    'permintaan_id' => $permintaanId,
                    'date' => $date,
                    'description' => $description ?: 'Tidak ada catatan/deskripsi.',
                    'barang_id' => $barang->id,
                    'req_qty' => $requested_qty,
                    'harga_total' => $total_harga_pilihan,
                    'pilihan_no' => sprintf('P-%04d', $index + 1),
                    'created_by' => Auth::user()->name,
                ]);

                Pengeluaran::create([
                    'barang_id' => $barang->id,
                    'qty' => $requested_qty,
                    'tanggal' => $date,
                    'permintaan_id' => $permintaanId,
                ]);

                $barang->qty_item -= $requested_qty;
                $barang->harga_total -= $total_harga_pilihan;
                if ($barang->harga_total < 0) {
                    $barang->harga_total = 0;
                }
                if ($barang->qty_item < 0) {
                    $barang->qty_item = 0;
                }
                $barang->save();
            }

            DB::commit();

            return redirect()->route('permintaan.saya')->with([
                'message' => 'Permintaan alat dan barang sukses diajukan!',
                'alert-type' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[DEBUG PILIHAN STORE] Error saving pilihan', ['error' => $e->getMessage()]);
            return redirect()->back()->with(['message' => 'Gagal memproses data: ' . $e->getMessage(), 'alert-type' => 'error']);
        }
    }
     
    public function PilihanEdit($id)
    {
        // 1. Ambil data pilihan acuan utama
        $pilihan = Pilihan::findOrFail($id);
        
        // 2. KUNCI UTAMA: Ambil semua barang yang berada dalam satu nomor permintaan yang sama
        $pilihanBarangLama = Pilihan::where('permintaan_id', $pilihan->permintaan_id)->get();
        
        $barang = Barang::all();
        $kelompok = Kelompok::all();
        
        // 3. Kirim data ke view edit
        return view('backend.pilihan.pilihan_edit', compact('pilihan', 'pilihanBarangLama', 'barang', 'kelompok'));
    }

    public function PilihanUpdate(Request $request, $id)
    {
        Log::info('[DEBUGGING UPDATE] Memulai proses PilihanUpdate.');
        DB::beginTransaction();

        try {
            $pilihanAwal = Pilihan::findOrFail($id);
            $permintaanId = $pilihanAwal->permintaan_id;

            $pilihanLama = Pilihan::where('permintaan_id', $permintaanId)->get();
            foreach ($pilihanLama as $itemLama) {
                $barang = Barang::find($itemLama->barang_id);
                if ($barang) {
                    $barang->qty_item += $itemLama->req_qty;
                    $barang->harga_total += $itemLama->harga_total; 
                    $barang->save();
                }
            }
            
            // Hapus log lama sebelum diganti transaksi baru
            Pengeluaran::where('permintaan_id', $permintaanId)->delete();

            $tableData = json_decode($request->input('table_data'), true);
            $date = $request->input('hidden_date');
            $description = $request->input('hidden_description');

            if (empty($tableData)) {
                DB::commit(); 
                return redirect()->route('pilihan.all')->with(['message' => 'Permintaan berhasil dikosongkan.', 'alert-type' => 'success']);
            }

            foreach ($tableData as $index => $item) {
                // Support payload that may include barang id
                if (isset($item['id']) && !empty($item['id'])) {
                    $barang = Barang::find($item['id']);
                } else {
                    $barang = Barang::where('nama', $item['barang_nama'])->first();
                }
                
                if (!$barang) continue; 

                // FIX SINKRONISASI PAYLOAD KEY UPDATE: Menggunakan qty_req agar sama dengan fungsi store
                $requested_qty = isset($item['qty_req']) ? (int)$item['qty_req'] : (isset($item['req_qty']) ? (int)$item['req_qty'] : 0);
                
                if ($requested_qty <= 0) continue;
                if ($barang->qty_item < $requested_qty) {
                    throw new \Exception("Stok barang '{$barang->nama}' tidak mencukupi di gudang.");
                }

                $hargaSatuanBaru = 0;
                if ($barang->qty_item > 0 && $barang->harga_total > 0) {
                    $hargaSatuanBaru = $barang->harga_total / $barang->qty_item;
                }
                
                $totalHargaPilihan = (int) round($hargaSatuanBaru * $requested_qty);
                
                $pilihanBaru = new Pilihan();
                $pilihanBaru->permintaan_id = $permintaanId;
                $pilihanBaru->barang_id = $barang->id;
                $pilihanBaru->pilihan_no = sprintf('P-%04d', $index + 1);
                $pilihanBaru->date = $date ? $date : Carbon::now()->format('Y-m-d');
                $pilihanBaru->description = $description;
                $pilihanBaru->req_qty = $requested_qty;
                $pilihanBaru->harga_total = $totalHargaPilihan; 
                $pilihanBaru->created_by = Auth::user()->name;
                $pilihanBaru->save();

                $pengeluaran = new Pengeluaran();
                $pengeluaran->barang_id = $barang->id;
                $pengeluaran->qty = $requested_qty;
                $pengeluaran->tanggal = $date ? $date : Carbon::now()->format('Y-m-d');
                $pengeluaran->permintaan_id = $permintaanId;
                $pengeluaran->save();

                $barang->qty_item -= $requested_qty;
                $barang->harga_total -= $totalHargaPilihan;
                
                if ($barang->harga_total < 0) $barang->harga_total = 0;
                if ($barang->qty_item < 0) $barang->qty_item = 0;

                $barang->save();
            }

            DB::commit();
            return redirect()->route('pilihan.all')->with(['message' => 'Permintaan ATK berhasil diperbarui!', 'alert-type' => 'success']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[FATAL UPDATE ERROR] ' . $e->getMessage());
            return back()->with(['message' => 'Gagal memperbarui: ' . $e->getMessage(), 'alert-type' => 'error']);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $permintaan = Permintaan::find($id);
        if (!$permintaan) {
            return redirect()->route('pilihan.all')->with('error', 'Permintaan tidak ditemukan');
        }

        $permintaan->status = $request->input('status');
        $permintaan->save();

        return redirect()->route('pilihan.all')->with('success', 'Status permintaan berhasil diperbarui');
    }

    // Fungsi untuk live search katalog berdasarkan kelompok & keyword (SINKRON DENGAN ELOQUENT MODEL)
    public function getCategory(Request $request)
    {
        $kelompok_id = $request->kelompok_id;
        $keyword = $request->keyword;

        $query = Barang::query();

        if (!empty($kelompok_id) && $kelompok_id !== 'all') {
            $query->where('kelompok_id', $kelompok_id);
        }

        if (!empty($keyword)) {
            $query->where('nama', 'like', "%{$keyword}%");
        }

        $barang = $query->select('id', 'nama', 'qty_item', 'satuan', 'kelompok_id', 'kategori_id', 'foto_barang')
                        ->get();

        $kategoris = Kategori::select('id', 'kelompok_id', 'nama')->get();

        return response()->json([
            'barang' => $barang,
            'kategoris' => $kategoris
        ]);
    }

    public function getKategori($kelompok_id)
    {
        $kategoris = Kategori::where('kelompok_id', $kelompok_id)->get();
        return response()->json($kategoris);
    }
}