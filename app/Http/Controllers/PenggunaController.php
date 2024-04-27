<?php

namespace App\Http\Controllers;

use App\Mail\Account\AktivasiPengguna;
use App\Mail\Account\AkunDiaktifkan;
use App\Mail\Account\AkunDinonaktifkan;
use App\Models\GudepDetail;
use App\Models\KetuaDetail;
use App\Models\PengurusDetail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class PenggunaController extends Controller
{
    public function index()
    {
        $data['user'] = User::get();

        return view('pages.pengguna.index', $data);
    }

    public function create()
    {
        return view('pages.pengguna.create');
    }

    public function edit($id)
    {
        $user = User::find($id);
        
        $detail = null;
        if($user->role !== 'Admin') {
            $detail = $user->detail;
        }

        $user->detail = $detail;

        $data['user'] = $user;

        return view('pages.pengguna.edit', $data);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $request->validate([
            'fullname'     => ['required'],
            'username'     => ['required','unique:ms_user'],
            'email'        => ['required','unique:ms_user','email'],
            'password'     => ['required','min:8','confirmed', Password::defaults()],
            'role'         => ['required','in:Pengurus,Gudep,Ketua'],
            'nama_mabigus' => ['nullable','string'],
            'nama_pengaju' => ['nullable','string'],
            'jabatan'      => ['nullable','string'],
            'no_hp'        => ['required','string'],
            'ttd'          => ['required'],
        ]);

        $user = User::create([
            'fullname'  => $request->fullname,
            'username'  => $request->username,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'is_active' => NULL
        ]);
        
        // Get the base64 image data from the request
        $base64Image = $request->input('ttd');

        // Remove the data URL prefix (e.g., 'data:image/png;base64,')
        $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);

        // Decode the base64 image data
        $imageData = base64_decode($base64Image);

        // Generate a unique filename for the image
        $fileName = time() . '_ttd.png';

        // Specify the storage directory
        $storagePath = 'public/ttd/';

        // Save the image using Laravel's storage disk
        Storage::put($storagePath . $fileName, $imageData);

        // Set the file path in the $ttd variable
        $ttd = $storagePath . $fileName;

        if ($request->role === 'Pengurus') {
            PengurusDetail::create([
                'user_id'      => $user->id,
                'nama_pengaju' => $request->nama_pengaju,
                'jabatan'      => $request->jabatan,
                'no_hp'        => $request->no_hp,
                'ttd'          => $ttd,
            ]);

        } else if ($request->role === 'Gudep') {
            GudepDetail::create([
                'user_id'      => $user->id,
                'nama_mabigus' => $request->nama_mabigus,
                'no_hp'        => $request->no_hp,
                'ttd'          => $ttd,
            ]);

        } else if ($request->role === 'Ketua') {
            KetuaDetail::create([
                'user_id'      => $user->id,
                'no_hp'        => $request->no_hp,
                'ttd'          => $ttd,
            ]);
        }

        DB::commit();

        return redirect()->route('pengguna.index')->with('success', 'Registrasi Berhasil! Mohon tunggu admin untuk mengaktifkan akun anda');
    }

    public function update(Request $request, $id)
    {
            DB::beginTransaction();

            $request->validate([
                'fullname'     => ['required'],
                'username'     => ['required','unique:ms_user,id,'.$id],
                'email'        => ['required','unique:ms_user,email,'.$id],
                'password'     => ['nullable','min:8','confirmed', Password::defaults()],
                'role'         => ['nullable','in:Pengurus,Gudep,Ketua'],
                'nama_mabigus' => ['nullable','string'],
                'nama_pengaju' => ['nullable','string'],
                'jabatan'      => ['nullable','string'],
                'no_hp'        => ['required','string'],
                'is_active'    => ['required','boolean'],
            ]);

            $user = User::find($id);

            $user->fullname  = $request->fullname;
            $user->username  = $request->username;
            $user->email     = $request->email;
            
            if($request->password) {
                $user->password  = Hash::make($request->password);
            }
            
            $user->role      = $request->role;

            // hanya lakukan fungsi dibawah jika admin ubah status user
            if($request->is_active !== null && $user->is_active != $request->is_active) {
                $user->is_active = $request->is_active;

                // kirim email jika admin update user aktif atau nonaktif
                if($user->is_active) {
                    Mail::to($user->email)->send(new AkunDiaktifkan($user));
                } else {
                    Mail::to($user->email)->send(new AkunDinonaktifkan($user));
                }
            }

            if ($user->role === 'Pengurus') {
                $user->detail->update([
                    'nama_pengaju' => $request->nama_pengaju,
                    'jabatan'      => $request->jabatan,
                    'no_hp'        => $request->no_hp,
                ]);

            } else if ($user->role === 'Gudep') {
                $user->detail->update([
                    'nama_mabigus' => $request->nama_mabigus,
                    'no_hp'        => $request->no_hp
                ]);

            } else if ($user->role === 'Ketua') {
                $user->detail->update([
                    'no_hp'        => $request->no_hp
                ]);
            }

            $user->save();

            DB::commit();

            return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil diubah');
    }

    public function destroy(string $id)
    {
        DB::beginTransaction();

        $user = User::find($id);

        // start: delete dokumen di storage ketika data di delete

            // Get the old file path from the database
            $oldFilePath = $user->ttd;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
            }

        // end: delete dokumen di storage ketika data di delete

        $user->delete();
        $user->detail?->delete();

        DB::commit();
        return redirect()->route('pengguna.index')->with('success', 'Data berhasil dihapus!');
    }
}
