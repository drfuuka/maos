<?php

namespace App\Http\Controllers;

use App\Mail\Account\AkunDiaktifkan;
use App\Mail\Account\AkunDinonaktifkan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class PenggunaController extends Controller
{
    public function index()
    {
        $data['user'] = User::get();

        return view('pages.pengguna.index', $data);
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
}
