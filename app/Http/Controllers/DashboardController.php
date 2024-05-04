<?php

namespace App\Http\Controllers;

use App\Models\Laporan\LaporanGudep;
use App\Models\Laporan\LaporanPengurus;
use App\Models\Lpj\LpjGudep;
use App\Models\Lpj\LpjPengurus;
use App\Models\Proposal\ProposalGudep;
use App\Models\Proposal\ProposalPengurus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->role;

        $laporanGudep  = LaporanGudep::count();
        $proposalGudep = ProposalGudep::count();
        $lpjGudep      = LpjGudep::count();

        $laporanPengurus  = LaporanPengurus::count();
        $proposalPengurus = ProposalPengurus::count();
        $lpjPengurus      = LpjPengurus::count();

        if($userRole === 'Gudep') {
            $laporanGudep  = LaporanGudep::where('user_id', Auth::id())->count();
            $proposalGudep = ProposalGudep::where('user_id', Auth::id())->count();
            $lpjGudep      = LpjGudep::where('user_id', Auth::id())->count();
        }

        if($userRole === 'Pengurus') {
            $laporanPengurus  = LaporanPengurus::where('user_id', Auth::id())->count();
            $proposalPengurus = ProposalPengurus::where('user_id', Auth::id())->count();
            $lpjPengurus      = LpjPengurus::where('user_id', Auth::id())->count();
        }
    
        $data['total_laporan'] = [
            'gudep'    => $laporanGudep,
            'pengurus' => $laporanPengurus,
        ];

        $data['total_proposal'] = [
            'gudep'    => $proposalGudep,
            'pengurus' => $proposalPengurus,
        ];

        $data['total_lpj'] = [
            'gudep'    => $lpjGudep,
            'pengurus' => $lpjPengurus,
        ];

        if($userRole === 'Admin') {
            $data['user'] = User::whereNull('is_active')->get();
            return view('pages.dashboard.admin', $data);

        } else if($userRole === 'Ketua') {

            return view('pages.dashboard.ketua', $data);

        } else if($userRole === 'Gudep') {

            return view('pages.dashboard.gudep', $data);

        } else if($userRole === 'Pengurus') {

            return view('pages.dashboard.pengurus', $data);
        }
    }

    public function activateUser($userId)
    {
        DB::beginTransaction();

        $user = User::find($userId);
        
        $user->update([
            'is_active' => true
        ]);

        DB::commit();
        return redirect()->route('dashboard')->with('success', 'Pengguna '. $user->fullname .' berhasil di-aktivasi!');
    }
}
