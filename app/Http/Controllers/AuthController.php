<?php

namespace App\Http\Controllers;

use App\Mail\Account\AktivasiPengguna;
use App\Mail\Account\AkunDiaktifkan;
use App\Mail\Account\AkunDinonaktifkan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Password as Pass;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\GudepDetail;
use App\Models\KetuaDetail;
use App\Models\PengurusDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function showRegisterForm()
    {   
        return view('auth.register');
    }

    public function register(Request $request)
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

        // kirim email ke admin untuk permintaan aktivasi akun
        $adminEmailList = User::where('role', 'Admin')->pluck('email')->toArray();

        foreach ($adminEmailList as $adminEmail) {
            Mail::to($adminEmail)->send(new AktivasiPengguna($user));
        }

        DB::commit();

        return redirect()->route('login.index')->with('success', 'Registrasi Berhasil! Mohon tunggu admin untuk mengaktifkan akun anda');
    }

    public function showLoginForm()
    {   
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $loginType = filter_var($request->email, FILTER_VALIDATE_EMAIL )
            ? 'email'
            : 'username';

        $account = User::where($loginType, $request->email)->first();
        if(!$account) {
            return back()->withErrors([
                'error' => 'The provided credentials do not match our records.',
            ]);
        }

        if($account->is_active === null) {
            return back()->withErrors([
                'error' => 'Akun belum diaktifkan, harap tunggu admin untuk mengaktifkan akun anda',
            ]);
        }

        if(!$account->is_active) {
            return back()->withErrors([
                'error' => 'Akun anda nonaktif',
            ]);
        }

        if(Auth::attempt([$loginType => $request->email, 'password' => $request->password])){ 
            return redirect()->route('dashboard');
        } 

        return back()->withErrors([
            'error' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
       Auth::logout();
       return redirect()->route('login.index');
    }

    public function showUserProfile()
    {
        return view('pages.profil.index');
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        $request->validate([
            'fullname'     => ['required'],
            'username'     => ['required','unique:ms_user,id,'.Auth::id()],
            'email'        => ['required','unique:ms_user,email,'.Auth::id()],
            'password'     => ['nullable','min:8','confirmed', Password::defaults()],
            'nama_mabigus' => ['nullable','string'],
            'nama_pengaju' => ['nullable','string'],
            'jabatan'      => ['nullable','string'],
            'no_hp'        => ['nullable','string'],
        ]);

        $user = User::find(Auth::id());

        $user->fullname  = $request->fullname;
        $user->username  = $request->username;
        $user->email     = $request->email;
        
        if($request->password) {
            $user->password  = Hash::make($request->password);
        }

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
                'no_hp'        => $request->no_hp ?? $user->detail->no_hp,
            ]);

        } else if ($user->role === 'Gudep') {
            $user->detail->update([
                'nama_mabigus' => $request->nama_mabigus,
                'no_hp'        => $request->no_hp ?? $user->detail->no_hp
            ]);

        } else if ($user->role === 'Ketua') {
            $user->detail->update([
                'no_hp'        => $request->no_hp ?? $user->detail->no_hp
            ]);
        }

        if($request->ttd && $user->role !== 'Admin') {

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

            if(Storage::exists($user->detail->ttd)) {
                Storage::delete($user->detail->ttd);
            }

            $user->detail->update([
                'ttd' => $ttd
            ]);
        }

        $user->save();

        DB::commit();

        return redirect()->route('profile.index')->with('success', 'Data pengguna berhasil diubah');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendForgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Pass::sendResetLink(
            $request->only('email')
        );

        return $status === Pass::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required',
            'token'    => 'required',
            'password' => 'required|confirmed|min:8',
        ]);
    
        $status = Pass::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );
    
        return $status == Pass::PASSWORD_RESET
                    ? redirect()->route('login.index')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
