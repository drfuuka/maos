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
    public function index(Request $request)
    {
        $year = $request->input('year');

        $userRole = Auth::user()->role;

        $laporanGudep  = $year ? LaporanGudep::whereYear('created_at', $year)->count() : LaporanGudep::count();
        $proposalGudep = $year ? ProposalGudep::whereYear('created_at', $year)->count() : ProposalGudep::count();
        $lpjGudep      = $year ? LpjGudep::whereYear('created_at', $year)->count() : LpjGudep::count();

        $laporanPengurus  = $year ? LaporanPengurus::whereYear('created_at', $year)->count() : LaporanPengurus::count();
        $proposalPengurus = $year ? ProposalPengurus::whereYear('created_at', $year)->count() : ProposalPengurus::count();
        $lpjPengurus      = $year ? LpjPengurus::whereYear('created_at', $year)->count() : LpjPengurus::count();

        if($userRole === 'Gudep') {
            $laporanGudep  = $year ? LaporanGudep::whereYear('created_at', $year)->count() : LaporanGudep::where('user_id', Auth::id())->count();
            $proposalGudep = $year ? ProposalGudep::whereYear('created_at', $year)->count() : ProposalGudep::where('user_id', Auth::id())->count();
            $lpjGudep      = $year ? LpjGudep::whereYear('created_at', $year)->count() : LpjGudep::where('user_id', Auth::id())->count();
        }

        if($userRole === 'Pengurus') {
            $laporanPengurus  = $year ? LaporanPengurus::whereYear('created_at', $year)->count() : LaporanPengurus::where('user_id', Auth::id())->count();
            $proposalPengurus = $year ? ProposalPengurus::whereYear('created_at', $year)->count() : ProposalPengurus::where('user_id', Auth::id())->count();
            $lpjPengurus      = $year ? LpjPengurus::whereYear('created_at', $year)->count() : LpjPengurus::where('user_id', Auth::id())->count();
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

            $gugusDepan = User::where('role', 'Gudep')
            ->get()
            ->map(function ($item) use($year) {
                return [
                    'nama'     => $item->fullname,
                    'laporan'  => $year ? $item->laporan()->whereYear('created_at', $year)->count() : $item->laporan->count(),
                    'proposal' => $year ? $item->proposal()->whereYear('created_at', $year)->count() : $item->proposal->count(),
                    'lpj'      => $year ? $item->lpj()->whereYear('created_at', $year)->count() : $item->lpj->count(),
                ];
            });

            $pengurus = User::where('role', 'Pengurus')
            ->get()
            ->map(function ($item) use($year) {
                return [
                    'nama'     => $item->fullname,
                    'laporan'  => $year ? $item->laporan()->whereYear('created_at', $year)->count() : $item->laporan->count(),
                    'proposal' => $year ? $item->proposal()->whereYear('created_at', $year)->count() : $item->proposal->count(),
                    'lpj'      => $year ? $item->lpj()->whereYear('created_at', $year)->count() : $item->lpj->count(),
                ];
            });

            $data['detail'] = [
                'gugus_depan' => $gugusDepan,
                'pengurus'    => $pengurus,
            ];

            return view('pages.dashboard.admin', $data);

        } else if($userRole === 'Ketua') {

            $gugusDepan = User::where('role', 'Gudep')
            ->get()
            ->map(function ($item) use($year) {
                return [
                    'nama'     => $item->fullname,
                    'laporan'  => $year ? $item->laporan()->whereYear('created_at', $year)->count() : $item->laporan->count(),
                    'proposal' => $year ? $item->proposal()->whereYear('created_at', $year)->count() : $item->proposal->count(),
                    'lpj'      => $year ? $item->lpj()->whereYear('created_at', $year)->count() : $item->lpj->count(),
                ];
            });

            $pengurus = User::where('role', 'Pengurus')
            ->get()
            ->map(function ($item) use($year) {
                return [
                    'nama'     => $item->fullname,
                    'laporan'  => $year ? $item->laporan()->whereYear('created_at', $year)->count() : $item->laporan->count(),
                    'proposal' => $year ? $item->proposal()->whereYear('created_at', $year)->count() : $item->proposal->count(),
                    'lpj'      => $year ? $item->lpj()->whereYear('created_at', $year)->count() : $item->lpj->count(),
                ];
            });


            $data['detail'] = [
                'gugus_depan' => $gugusDepan,
                'pengurus'    => $pengurus,
            ];

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
