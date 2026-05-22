<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function UserAll(Request $request)
{
    if ($request->ajax()) {
        // Apply filtering based on the role if it's provided in the request
        $query = User::query();

        if ($request->has('role') && !empty($request->role)) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->get();

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                return '<div class="table-actions" style="text-align: center; vertical-align: middle;">
                    <a href="'.route('user.edit', $row->id).'" class="btn btn-sm hover:bg-warning" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #e1a017; padding: 15px;" data-tooltip="Edit Permintaan">
                        <i class="ti ti-edit font-size-20 align-middle"></i>
                    </a>
                    <a href="'.route('user.delete', $row->id).'" class="btn btn-sm text-danger hover:bg-danger" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: red; padding: 15px;" data-tooltip="Hapus Permintaan">
                        <i class="ti ti-trash font-size-20 align-middle text-danger"></i>
                    </a>
                   
                </div>';
            })
            ->make(true);
    }

    // Fetch distinct roles from the users table for filtering options
    $roles = User::select('role')->distinct()->get();

    return view('backend.user.user_all', compact('roles'));
}


    public function UserAdd(){
        return view('backend.user.user_add');
    } // End Method

    public function UserStore(Request $request)
    {
        // Validasi input termasuk file
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'panggilan' => 'required|string|max:255',
            'role' => 'required|in:admin,supervisor,pegawai',
            'image' => 'nullable|image|mimes:png|max:2048',
            'signature' => 'nullable|image|mimes:png|max:2048',
        ]);
    
        // Simpan user tanpa path file terlebih dahulu untuk mendapatkan ID user
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'panggilan' => $request->panggilan,
            'role' => $request->role,
            'password' => Hash::make('password'),
        ]);
    
        // Proses upload file
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            // Menyimpan file di direktori yang benar
            $imagePath = $image->storeAs('public/backend/assets/images/users', 'foto_' . $user->id . '.png');
            // Mengupdate path relatif untuk disimpan di database
            $imagePath = str_replace('public/', '', $imagePath);
        } else {
            $imagePath = null;
        }
    
        if ($request->hasFile('signature')) {
            $signature = $request->file('signature');
            // Menyimpan file di direktori yang benar
            $signaturePath = $signature->storeAs('public/backend/assets/images/users', 'ttd_' . $user->id . '.png');
            // Mengupdate path relatif untuk disimpan di database
            $signaturePath = str_replace('public/', '', $signaturePath);
        } else {
            $signaturePath = null;
        }
    
        // Update user dengan path file
        $user->update([
            'foto' => $imagePath,
            'ttd' => $signaturePath,
        ]);
    
        $notification = array(
            'message' => "Pengguna berhasil ditambahkan.",
            'alert-type' => "success"
        );
    
        return redirect()->route('user.all')->with($notification);
    }
    
    
    public function UserEdit($id){
        $user = User::findOrFail($id);
        return view('backend.user.user_edit', compact('user'));
    }

    public function UserUpdate(Request $request, $id)
{
    $user = User::findOrFail($id);

    // Validasi input termasuk file
    $request->validate([
        'role' => 'required|in:admin,supervisor,pegawai',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'signature' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Update user role and other fields
    $user->update([
        'role' => strtolower($request->role),
        'panggilan' => $request->panggilan,
    ]);

    // Handle file upload for photo
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $extension = $image->getClientOriginalExtension(); // Mendapatkan ekstensi file asli
        $imagePath = $image->storeAs('public/backend/assets/images/users', 'foto_' . $user->id . '.' . $extension);
        $user->update([
            'foto' => str_replace('public/', '', $imagePath)
        ]);
    }

    // Handle file upload for signature
    if ($request->hasFile('signature')) {
        $signature = $request->file('signature');
        $extension = $signature->getClientOriginalExtension(); // Mendapatkan ekstensi file asli
        $signaturePath = $signature->storeAs('public/backend/assets/images/users', 'ttd_' . $user->id . '.' . $extension);
        $user->update([
            'ttd' => str_replace('public/', '', $signaturePath)
        ]);
    }

    $notification = array(
        'message' => 'Data pengguna berhasil diperbarui.',
        'alert-type' => 'success'
    );

    return redirect()->route('user.all')->with($notification);
}

public function UpdateProfile(Request $request, $id)
{
    $user = User::findOrFail($id);

    // Validate the input including files
    $request->validate([
        'role' => 'required|in:admin,supervisor,pegawai',
        'name' => 'required|string|max:255',
        'panggilan' => 'nullable|string|max:255',
        'username' => 'required|string|max:255|unique:users,username,' . $user->id,
        'image' => 'nullable|image|mimes:png|max:2048',
        'signature' => 'nullable|image|mimes:png|max:2048',
    ]);

    $user->update([
        'name' => $request->input('name'),
        'panggilan' => $request->input('panggilan'),
        'username' => $request->input('username'),
    ]);

    // Handle file upload for photo
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imagePath = $image->storeAs('public/assets/images/users', 'foto_' . $user->id . '.png');
        $user->update([
            'foto' => str_replace('public/', 'backend/', $imagePath)
        ]);
    }

    // Handle file upload for signature
    if ($request->hasFile('signature')) {
        $signature = $request->file('signature');
        $signaturePath = $signature->storeAs('public/assets/images/users', 'ttd_' . $user->id . '.png');
        $user->update([
            'ttd' => str_replace('public/', 'backend/', $signaturePath)
        ]);
    }

    $notification = array(
        'message' => 'Data Anda berhasil diperbarui.',
        'alert-type' => 'success'
    );

    return redirect()->route('edit.profile')->with($notification);
}


    public function UserDelete($id){
        user::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Pengguna berhasil dihapus',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

}
