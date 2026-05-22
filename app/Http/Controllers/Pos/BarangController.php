<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelompok;
use App\Models\Kategori;
use App\Models\Pemasukan;
use App\Models\Barang;
use App\Models\StokAwalBulan;
use Auth;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use App\Exports\BarangExport;
use App\Exports\PemasukanExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class BarangController extends Controller
{
    public function BarangAll(Request $request)
    {
        if ($request->ajax()) {
            // Query dasar dengan relasi ke tabel 'kelompok'
            $query = Barang::with('kelompok')
                ->select(['barangs.id', 'barangs.kode', 'barangs.kelompok_id', 'barangs.nama', 'barangs.qty_item', 'barangs.satuan', 'barangs.foto_barang', 'barangs.harga_total']);

            // Filter berdasarkan kelompok barang
            if ($request->has('kelompok_id') && !empty($request->kelompok_id)) {
                $kelompokBarang = $request->kelompok_id;
                $query->where('kelompok_id', $kelompokBarang);
            }

            // Eksekusi query dan kembalikan hasilnya dalam format DataTables
            $barangs = $query->latest()->get();
            $kelompoks = Kelompok::all();

            return DataTables::of($barangs)
                ->addIndexColumn()
                ->addColumn('kode', function ($row) {
                    return $row->kode;
                })
                ->addColumn('kelompok_barang', function ($row) {
                    return $row->kelompok->nama ?? 'Tidak ada data';
                })
                ->addColumn('nama_barang', function ($row) {
                    return $row->nama;
                })
                ->addColumn('stok', function ($row) {
                    return $row->qty_item;
                })
                ->addColumn('satuan', function ($row) {
                    return $row->satuan ?? 'Tidak ada data';
                })
                // ->addColumn('foto_barang', function ($row) {
                //     // Misalnya, jika Anda ingin menampilkan gambar, bisa seperti ini:
                //     return $row->foto_barang ? '<img src="' . asset('storage/' . $row->foto_barang) . '" alt="Foto Barang" style="width: 50px; height: 50px;">' : 'Tidak ada foto';
                // })
                // ->rawColumns(['foto_barang']) // Pastikan rawColumns digunakan untuk kolom yang mengandung HTML
                ->make(true);
        }

        $kelompokFilt = Kelompok::select('id', 'nama')->distinct()->get();

        $barangs = Barang::with('kelompok')->latest()->get();

        return view('backend.barang.barang_all', compact('kelompokFilt', 'barangs'));
    }

    public function BarangAllAct(Request $request)
    {
        if ($request->ajax()) {
            $query = Barang::with(['kelompok', 'kategori'])
                ->select(['barangs.id', 'barangs.kode', 'barangs.kelompok_id', 'barangs.kategori_id', 'barangs.nama', 'barangs.qty_item', 'barangs.satuan', 'barangs.foto_barang', 'barangs.harga_total']);

            if ($request->has('kelompok_id') && !empty($request->kelompok_id)) {
                $query->where('kelompok_id', $request->kelompok_id);
            }

            if ($request->has('kategori_id') && !empty($request->kategori_id)) {
                $query->where('kategori_id', $request->kategori_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('kelompok_barang', function ($row) {
                    return $row->kelompok->nama ?? 'Tidak ada data';
                })
                ->addColumn('kategori_barang', function ($row) {
                    return $row->kategori->nama ?? 'Tidak ada data';
                })
                ->addColumn('harga_total', function ($row) {
                    // 1. Ambil nilai harga
                    $harga = $row->harga_total;

                    // 2. Format menggunakan number_format: 0 desimal, koma untuk desimal (diabaikan), titik untuk ribuan
                    return number_format($harga, 0, ',', '.'); // Hasil: "1.500.000"
                })
                ->addColumn('foto_barang', function ($row) {
                    return $row->foto_barang ? '<img src="' . asset('storage/' . $row->foto_barang) . '" alt="Foto Barang" style="width: 50px; height: 50px;">' : 'Tidak ada foto';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="table-actions" style="text-align: center; vertical-align: middle;">
                    <button class="btn btn-sm add-stock-btn hover:bg-success" 
                        data-bs-toggle="modal" 
                        data-bs-target="#addStockModal" 
                        data-id="' . $row->id . '" 
                        data-nama="' . $row->nama . '" 
                        data-harga-total="'.$row->harga_total.'" // <-- UBAH MENJADI INI
                        style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: green; padding: 15px;" 
                        data-tooltip="Tambah Stok Barang">
                        <i class="ti ti-circle-plus font-size-20 align-middle"></i>
                    </button>
                    <a href="'.route('barang.edit', $row->id).'" class="btn btn-sm hover:bg-warning" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #e1a017; padding: 15px;" data-tooltip="Edit Barang">
                        <i class="ti ti-edit font-size-20 align-middle"></i>
                    </a>
                    <a href="'.route('barang.delete', $row->id).'" class="btn btn-sm text-danger hover:bg-danger delete-btn" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: red; padding: 15px;" data-tooltip="Hapus Barang">
                        <i class="ti ti-trash font-size-20 align-middle text-danger"></i>
                    </a>
                   
                </div>';
                })
                ->rawColumns(['foto_barang', 'harga_total', 'action'])
                ->make(true);
        }

        $kelompokFilt = Kelompok::select('id', 'nama')->distinct()->get();
        return view('backend.barang.barang_all', compact('kelompokFilt'));
    }

    public function dataForAll()
    {
        $barangs = Barang::with('kelompok')->get(); // Hapus 'satuan'

        return DataTables::of($barangs)
            ->addColumn('action', function ($barang) {
                return '<div class="table-actions" style="text-align: center; vertical-align: middle;">
                        <a href="'.route('barang.edit', $barang->id).'" class="btn bg-warning btn-sm">
                            <i class="fas fa-edit" style="color: #ca8a04"></i>
                        </a>
                        <a href="'.route('barang.delete', $barang->id).'" class="btn bg-danger btn-sm">
                            <i class="fas fa-trash-alt text-danger"></i>
                        </a>
                    </div>';
            })
            ->rawColumns(['action'])
            ->toJson();
    }
    public function dataForIndex()
    {
        $barangs = Barang::with('kelompok')->get(); // Hapus 'satuan'

        return DataTables::of($barangs)
            ->rawColumns([]) // No raw columns
            ->toJson();
    }

    public function barangStore(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:255|unique:barangs,kode',
            'nama' => 'required|string|max:255',
            'kelompok_id' => 'required|integer|exists:kelompoks,id',
            'kategori_id' => 'required|integer|exists:kategoris,id',
            'qty_item' => 'nullable|integer',
            'satuan' => 'required|string',
            'satuanBaru' => 'nullable|string|max:100',
            'harga_total' => 'nullable|string',
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $satuan = $validated['satuan'];
        $satuanBaru = $validated['satuanBaru'] ?? null;

        // Jika pilihan satuan adalah 'lainnya' dan satuanBaru tidak kosong
        if ($satuan === 'lainnya' && !empty($satuanBaru)) {
            // Periksa apakah satuan baru sudah ada di database
            $existingSatuan = Barang::where('satuan', strtolower($satuanBaru))->first();

            if ($existingSatuan) {
                // Jika sudah ada, gunakan satuan yang sudah ada
                $satuan = $existingSatuan->satuan;
            } else {
                // Jika belum ada, simpan satuan baru ke dalam tabel barang
                $satuan = strtolower($satuanBaru);
            }
        }

        // Simpan data barang baru
        $barang = new Barang();
        $barang->nama = $validated['nama'];
        $barang->kode = $validated['kode_barang'];
        $barang->kelompok_id = $validated['kelompok_id'];
        $barang->kategori_id = $validated['kategori_id'];
        $barang->qty_item = $validated['qty_item'] ?? 0;
        $barang->satuan = $satuan; // Simpan satuan di kolom satuan
        $barang->created_at = Carbon::now();
        $barang->updated_at = Carbon::now();
        $barang->harga_total = (int) preg_replace('/\D/', '', $validated['harga_total'] ?? '0');

        // Handle file upload
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension(); // Mendapatkan ekstensi file asli (jpg, jpeg, png, dll)
            $fileName = 'foto_' . $validated['kode_barang'] . '.' . $extension;
            $file->storeAs('backend/assets/images/barang', $fileName, 'public'); // Simpan file ke disk public

            $barang->foto_barang = 'backend/assets/images/barang/' . $fileName; // Simpan path relatif untuk asset storage
        } else {
            $barang->foto_barang = null; // Jika tidak ada foto, simpan nilai null
        }

        $barang->save();

        // Simpan stok awal bulan
        $stokAwalBulan = new StokAwalBulan();
        $stokAwalBulan->barang_id = $barang->id;
        $stokAwalBulan->qty_awal = $request->qty_item; // Set qty_awal dari qty_item yang dimasukkan
        $stokAwalBulan->harga_total = $barang->harga_total;
        $stokAwalBulan->tahun = Carbon::now()->year; // Menggunakan tahun saat ini
        $stokAwalBulan->bulan = Carbon::now()->month; // Menggunakan bulan saat ini
        $stokAwalBulan->save();

        // Notifikasi sukses
        $notification = array(
            'message' => "Barang berhasil ditambahkan.",
            'alert-type' => "success"
        );

        return redirect()->route('barang.all')->with($notification);
    }

    public function KelompokStore(Request $request)
    {
        Kelompok::insert([
            'nama' => $request->nama,
            'kode' => $request->kode_barang,
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

    public function KelompokEdit($id)
    {
        $kelompok = Kelompok::findOrFail($id);
        return view('backend.kelompok.kelompok_edit', compact('kelompok'));
    }

    public function KelompokUpdate(Request $request)
    {
        $kelompok_id = $request->id;

        Kelompok::findOrFail($kelompok_id)->update([
            'nama' => $request->nama,
            'updated_at' => Carbon::now()
        ]);

        $notification = array(
            'message' => 'Kelompok Barang berhasil di update',
            'alert-type' => 'success'
        );

        return redirect()->route('kelompok.all')->with($notification);
    }

    public function KelompokDelete($id)
    {
        Kelompok::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Kelompok Barang berhasil dihapus',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function barangAdd()
    {
        $kelompok = Kelompok::all();
        $kategori = Kategori::all();
        $satuan = Barang::select('satuan')
        ->distinct()
        ->get();

        return view('backend.barang.barang_add', compact('kelompok', 'kategori', 'satuan'));
    }

    public function barangEdit($id)
    {
        $kelompok = Kelompok::all();
        $satuans = Barang::select('satuan')->distinct()->pluck('satuan'); // Mengambil koleksi string

        $barang = Barang::findOrFail($id);
        $kategori = Kategori::where('kelompok_id', $barang->kelompok_id)->get();

        return view('backend.barang.barang_edit', compact('barang', 'kelompok', 'satuans', 'kategori'));
    }



    // Proses update (terima $id dari route)
    public function barangUpdate(Request $request, $id)
    {
        $validated = $request->validate([
        'kode_barang' => 'required|string|max:255',
        'nama' => 'required|string|max:255',
        'kelompok_id' => 'required|integer|exists:kelompoks,id',
        'kategori_id' => 'required|integer|exists:kategoris,id',
        'qty_item' => 'nullable|integer',
        'satuan' => 'required|string',
        'satuanBaru' => 'nullable|string|max:100',
        'harga_total' => 'nullable|string', // akan dibersihkan sebelum disimpan
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);


        // Ambil barang yang akan diupdate
        $barang = Barang::findOrFail($id);
        $kode_barang = $validated['kode_barang'];


        // Ambil qty lama untuk log pemasukan/pengeluaran
        $qtyLama = $barang->qty_item ?? 0;


        // Proses harga: bersihkan semua non-digit lalu cast ke integer
        $hargaTotalMentah = 0;
        if (!empty($request->harga_total)) {
        // hilangkan titik/komma/karakter lain
        $hargaTotalMentah = (int) preg_replace('/\D/', '', $request->harga_total);
        }


        // Tangani satuan 'lainnya'
        $satuan = $validated['satuan'];
        if ($satuan === 'lainnya' && !empty($validated['satuanBaru'])) {
        $satuanBaru = trim($validated['satuanBaru']);
        $existingSatuan = Barang::whereRaw('LOWER(satuan) = ?', [strtolower($satuanBaru)])->first();
        $satuan = $existingSatuan ? $existingSatuan->satuan : $satuanBaru;
        }


        // Tangani upload foto (simpan ke disk public backend/assets/images/barang)
        $fotoPath = $barang->foto_barang;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $filename = 'foto_' . $validated['kode_barang'] . '.' . $extension;
            $newFotoPath = 'backend/assets/images/barang/' . $filename;
            $file->storeAs('backend/assets/images/barang', $filename, 'public');

            if ($barang->foto_barang && $barang->foto_barang !== $newFotoPath && Storage::disk('public')->exists($barang->foto_barang)) {
                Storage::disk('public')->delete($barang->foto_barang);
            }

            $fotoPath = $newFotoPath;
        } elseif ($barang->foto_barang && $validated['kode_barang'] !== $barang->kode) {
            $oldFotoPath = $barang->foto_barang;
            $oldExtension = pathinfo($oldFotoPath, PATHINFO_EXTENSION);
            $newFilename = 'foto_' . $validated['kode_barang'] . '.' . $oldExtension;
            $newFotoPath = 'backend/assets/images/barang/' . $newFilename;

            if (Storage::disk('public')->exists($oldFotoPath)) {
                Storage::disk('public')->move($oldFotoPath, $newFotoPath);
                $fotoPath = $newFotoPath;
            }
        }


        // Update record barang
        $barang->update([
        'kode' => $validated['kode_barang'],
        'nama' => $validated['nama'],
        'kelompok_id' => $validated['kelompok_id'],
        'kategori_id' => $validated['kategori_id'],
        'qty_item' => $validated['qty_item'] ?? 0,
        'satuan' => $satuan,
        'foto_barang' => $fotoPath,
        'harga_total' => $hargaTotalMentah,
        // updated_at otomatis di-handle oleh Eloquent
        ]);

        $notification = array(
            'message' => 'Barang berhasil diupdate',
            'alert-type' => 'success'
        );

        return redirect()->route('barang.all')->with($notification);
    }


    public function addStock(Request $request)
    {
        try {
            // 1. Validasi Input
            $request->validate([
                'barang_id' => 'required|exists:barangs,id',
                'qty' => 'required|integer|min:1',
                // Pastikan Anda menerima harga total stok baru dari frontend
                'harga_total_stok_baru' => 'required|numeric|min:0',
            ]);

            // Gunakan transaksi database untuk memastikan data konsisten
            DB::beginTransaction();

            // 2. Temukan barang
            $barang = Barang::findOrFail($request->barang_id);

            // Ambil nilai dari request
            $qtyBaru = $request->qty;
            $hargaStokBaru = $request->harga_total_stok_baru; // Ini adalah Harga Total dari stok yang baru masuk

            // 3. Simpan data pemasukan (History)
            Pemasukan::create([
                'barang_id' => $barang->id, // Gunakan $barang->id yang sudah terverifikasi
                'qty' => $qtyBaru,
                'harga_total_pemasukan' => $hargaStokBaru, // 💡 Tambahkan kolom harga ke riwayat pemasukan
                'tanggal' => now()->toDateString(),
            ]);

            // 4. Perbarui Stok dan Harga Total Barang (Barang Induk)
            $barang->qty_item += $qtyBaru;
            // 💡 Tambahkan Harga Total Baru ke Harga Total Kumulatif yang sudah ada
            $barang->harga_total += $hargaStokBaru;

            $barang->save();

            // Commit transaksi jika semua berhasil
            DB::commit();

            return response()->json(['success' => 'Stok dan Harga Total berhasil ditambahkan.']);

        } catch (ValidationException $e) {
            // Rollback transaksi jika terjadi error validasi
            DB::rollBack();
            return response()->json(['errors' => $e->errors()], 422); // Kode 422 untuk error validasi

        } catch (Exception $e) {
            // Rollback transaksi jika terjadi error lain
            DB::rollBack();
            // Anda harus mengaktifkan Log::error jika ingin merekam error
            // \Log::error('Error adding stock:', ['exception' => $e->getMessage()]);

            return response()->json(['error' => 'Terjadi kesalahan sistem saat menambahkan stok.'], 500);
        }
    }

    public function barangDelete($id)
    {
        $barang = barang::findOrFail($id);
        $barang->delete();

        // Jika request berasal dari AJAX, kembalikan JSON response
        if (request()->ajax()) {
            return response()->json(['message' => 'Data barang berhasil dihapus.']);
        }

        // Jika bukan AJAX, lanjutkan dengan redirect
        $notification = array(
            'message' => 'Barang berhasil dihapus',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function exportToExcel(Request $request)
    {
        // Ambil tanggal dari input form
        $tanggal = $request->input('tanggal');

        // Ambil semua data barang
        $barang = Barang::all();

        // Format filename sesuai dengan tanggal yang dipilih
        $formattedDate = Carbon::parse($tanggal)->format('d M Y');
        $filename = "BA Stock Opname {$formattedDate}.xlsx";

        // Kirim data barang dan tanggal ke BarangExport
        return Excel::download(new BarangExport($barang, $tanggal), $filename);
    }


    public function exportPemasukan(Request $request)
    {
        // Dapatkan tanggal dari request
        $startDate = Carbon::parse($request->get('start_date'));
        $endDate = Carbon::parse($request->get('end_date'));

        // Format tanggal menjadi nama bulan dan tahun dalam bahasa Indonesia
        $startDateFormatted = $startDate->translatedFormat('j F Y');
        $endDateFormatted = $endDate->translatedFormat('j F Y');

        // Tentukan path file Excel yang akan diakses di storage/excel
        $filePath = storage_path('app/excel/Laporan_Rincian_Persediaan.xlsx');

        // Pastikan file Excel benar-benar ada
        if (file_exists($filePath)) {
            // Panggil fungsi untuk memproses file Excel
            $exporter = new PemasukanExport();
            $updatedFilePath = $exporter->export($filePath, $startDate, $endDate);

            // Periksa apakah file sementara berhasil dibuat dan siap diunduh
            if ($updatedFilePath) {
                // Gunakan format tanggal yang diinginkan dalam nama file
                $filename = "Laporan_Rincian_Persediaan_{$startDateFormatted}_{$endDateFormatted}.xlsx";
                return response()->download($updatedFilePath, $filename)->deleteFileAfterSend(true);
            } else {
                return redirect()->back()->with('error', 'Gagal membuat file untuk diunduh.');
            }
        } else {
            return redirect()->back()->with('error', 'File Excel tidak ditemukan!');
        }
    }

}