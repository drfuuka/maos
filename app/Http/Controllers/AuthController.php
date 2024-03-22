<?php

namespace App\Http\Controllers;

use App\Mail\Account\AktivasiPengguna;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
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
        try {
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

        } catch (Exception $e) {
            $errorMessages = 'Error Occured';
            if(env('APP_DEBUG')) {
                $errorMessages = $e->getMessage();
            }
            dd($errorMessages);
        }
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
}
