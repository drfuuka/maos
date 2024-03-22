<?php

namespace App\Http\Controllers\Lpj;

use App\Exports\LpjPengurusExport;
use App\Http\Controllers\Controller;
use App\Mail\Lpj\LpjDiterima;
use App\Mail\Lpj\LpjDitolak;
use App\Mail\Lpj\VerifikasiLpj;
use App\Models\Lpj\LpjPengurus;
use App\Models\Proposal\ProposalPengurus;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LpjPengurusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['lpjPengurus'] = LpjPengurus::with('proposal')->get();

        return view('pages.lpj.pengurus.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['proposalOption'] = ProposalPengurus::where('status_verifikasi', 'Diterima')->get();

        return view('pages.lpj.pengurus.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        $request->validate([
            'proposal_pengurus_id' => ['required', 'exists:tr_proposal_pengurus,id'],
            'foto_kegiatan'     => ['required', 'file'],
            'dokumen_lpj'       => ['required', 'file'],
            'evaluasi'          => ['required', 'string'],
            'saran'             => ['required', 'string'],
        ]);

        $fotoKegiatan = null;
        if ($request->hasFile('foto_kegiatan') && $request->foto_kegiatan->isValid()) {
            $file = $request->file('foto_kegiatan');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fotoKegiatan = $file->storeAs('lpj-pengurus/foto-kegiatan', $fileName, 'public');
        }

        $dokumenLpj = null;
        if ($request->hasFile('dokumen_lpj') && $request->dokumen_lpj->isValid()) {
            $file = $request->file('dokumen_lpj');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dokumenLpj = $file->storeAs('lpj-pengurus/dokumen-lpj', $fileName, 'public');
        }

        $lpj = LpjPengurus::create([
            'proposal_pengurus_id' => $request->proposal_pengurus_id,
            'user_id'           => Auth::id(),
            'foto_kegiatan'     => $fotoKegiatan,
            'dokumen_lpj'       => $dokumenLpj,
            'evaluasi'          => $request->evaluasi,
            'saran'             => $request->saran
        ]);

        // get seluruh akun dengan role ketua
        $ketuaEmailList = User::where('role', 'Ketua')->pluck('email')->toArray();

        // kirim email ke seluruh akun dengan role ketua dengan perulangan
        foreach ($ketuaEmailList as $email) {
            Mail::to($email)->send(new VerifikasiLpj(Auth::user(), $lpj));
        }

        DB::commit();

        return redirect()->route('lpj-pengurus.index')->with('success', 'Data berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['lpjPengurus']       = LpjPengurus::find($id);
        $data['proposalOption'] = ProposalPengurus::where('status_verifikasi', 'Diterima')->get();

        return view('pages.lpj.pengurus.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        $request->validate([
            'proposal_pengurus_id' => ['required', 'exists:tr_proposal_pengurus,id'],
            'foto_kegiatan'     => ['nullable', 'file'],
            'dokumen_lpj'       => ['nullable', 'file'],
            'evaluasi'          => ['required', 'string'],
            'saran'             => ['required', 'string'],
            'status_verifikasi' => ['nullable', 'string', 'in:Ditolak,Diterima'],
        ]);

        $lpjPengurus = LpjPengurus::find($id);

        $fotoKegiatan = $lpjPengurus->foto_kegiatan;
        if ($request->hasFile('foto_kegiatan') && $request->foto_kegiatan->isValid()) {
            // Get the old file path from the database
            $oldFilePath = $lpjPengurus->foto_kegiatan;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::disk('public')->exists($oldFilePath)) {
                // Delete the old file
                Storage::disk('public')->delete($oldFilePath);
            }

            $file = $request->file('foto_kegiatan');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fotoKegiatan = $file->storeAs('lpj-pengurus/foto-kegiatan', $fileName, 'public');
        }

        $dokumenLpj = $lpjPengurus->dokumen_lpj;
        if ($request->hasFile('dokumen_lpj') && $request->dokumen_lpj->isValid()) {
            // Get the old file path from the database
            $oldFilePath = $lpjPengurus->dokumen_lpj;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::disk('public')->exists($oldFilePath)) {
                // Delete the old file
                Storage::disk('public')->delete($oldFilePath);
            }

            $file = $request->file('dokumen_lpj');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dokumenLpj = $file->storeAs('lpj-pengurus/dokumen-lpj', $fileName, 'public');
        }
        
        $lpjPengurus->proposal_pengurus_id = $request->proposal_pengurus_id;
        $lpjPengurus->foto_kegiatan     = $fotoKegiatan;
        $lpjPengurus->dokumen_lpj       = $dokumenLpj;
        $lpjPengurus->evaluasi          = $request->evaluasi;
        $lpjPengurus->saran             = $request->saran;

        if($request->status_verifikasi) {
            $lpjPengurus->status_verifikasi = $request->status_verifikasi;
            $lpjPengurus->verificator_id = Auth::id();

            if($lpjPengurus->status_verifikasi === 'Diterima') {
                Mail::to($lpjPengurus->user->email)->send(new LpjDiterima(Auth::user(), $lpjPengurus));
            } else {
                Mail::to($lpjPengurus->user->email)->send(new LpjDitolak(Auth::user(), $lpjPengurus));
            }
        }

        $lpjPengurus->save();

        DB::commit();

        return redirect()->route('lpj-pengurus.index')->with('success', 'Data berhasil di-update!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        $lpjPengurus = LpjPengurus::find($id);

        // start: delete dokumen di storage ketika data di delete

            // Get the old file path from the database
            $oldFilePath = $lpjPengurus->dokumen_lpj;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::disk('public')->exists($oldFilePath)) {
                // Delete the old file
                Storage::disk('public')->delete($oldFilePath);
            }

            // Get the old file path from the database
            $oldFilePath = $lpjPengurus->foto_kegiatan;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::disk('public')->exists($oldFilePath)) {
                // Delete the old file
                Storage::disk('public')->delete($oldFilePath);
            }

        // end: delete dokumen di storage ketika data di delete

        $lpjPengurus->delete();

        DB::commit();
        return redirect()->route('lpj-pengurus.index')->with('success', 'Data berhasil dihapus!');
    }

    public function export()
    {
        return Excel::download(new LpjPengurusExport, 'lpj-pengurus.xlsx');
    }
}
