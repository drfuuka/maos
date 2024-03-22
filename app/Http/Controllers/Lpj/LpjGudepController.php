<?php

namespace App\Http\Controllers\Lpj;

use App\Http\Controllers\Controller;
use App\Models\Lpj\LpjGudep;
use App\Models\Proposal\ProposalGudep;
use App\Exports\LpjGudepExport;
use App\Mail\Lpj\LpjDiterima;
use App\Mail\Lpj\LpjDitolak;
use App\Mail\Lpj\VerifikasiLpj;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LpjGudepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['lpjGudep'] = LpjGudep::with('proposal')->get();

        return view('pages.lpj.gudep.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['proposalOption'] = ProposalGudep::where('status_verifikasi', 'Diterima')->get();

        return view('pages.lpj.gudep.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        $request->validate([
            'proposal_gudep_id' => ['required', 'exists:tr_proposal_gudep,id'],
            'foto_kegiatan'     => ['required', 'file'],
            'dokumen_lpj'       => ['required', 'file'],
            'evaluasi'          => ['required', 'string'],
            'saran'             => ['required', 'string'],
        ]);

        $fotoKegiatan = null;
        if ($request->hasFile('foto_kegiatan') && $request->foto_kegiatan->isValid()) {
            $file = $request->file('foto_kegiatan');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fotoKegiatan = $file->storeAs('lpj-gudep/foto-kegiatan', $fileName, 'public');
        }

        $dokumenLpj = null;
        if ($request->hasFile('dokumen_lpj') && $request->dokumen_lpj->isValid()) {
            $file = $request->file('dokumen_lpj');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dokumenLpj = $file->storeAs('lpj-gudep/dokumen-lpj', $fileName, 'public');
        }

        $lpj = LpjGudep::create([
            'proposal_gudep_id' => $request->proposal_gudep_id,
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

        return redirect()->route('lpj-gudep.index')->with('success', 'Data berhasil dibuat!');
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
        $data['lpjGudep']       = LpjGudep::find($id);
        $data['proposalOption'] = ProposalGudep::where('status_verifikasi', 'Diterima')->get();

        return view('pages.lpj.gudep.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        $request->validate([
            'proposal_gudep_id' => ['required', 'exists:tr_proposal_gudep,id'],
            'foto_kegiatan'     => ['nullable', 'file'],
            'dokumen_lpj'       => ['nullable', 'file'],
            'evaluasi'          => ['required', 'string'],
            'saran'             => ['required', 'string'],
            'status_verifikasi' => ['nullable', 'string', 'in:Ditolak,Diterima'],
        ]);

        $lpjGudep = LpjGudep::find($id);

        $fotoKegiatan = $lpjGudep->foto_kegiatan;
        if ($request->hasFile('foto_kegiatan') && $request->foto_kegiatan->isValid()) {
            // Get the old file path from the database
            $oldFilePath = $lpjGudep->foto_kegiatan;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::disk('public')->exists($oldFilePath)) {
                // Delete the old file
                Storage::disk('public')->delete($oldFilePath);
            }

            $file = $request->file('foto_kegiatan');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fotoKegiatan = $file->storeAs('lpj-gudep/foto-kegiatan', $fileName, 'public');
        }

        $dokumenLpj = $lpjGudep->dokumen_lpj;
        if ($request->hasFile('dokumen_lpj') && $request->dokumen_lpj->isValid()) {
            // Get the old file path from the database
            $oldFilePath = $lpjGudep->dokumen_lpj;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::disk('public')->exists($oldFilePath)) {
                // Delete the old file
                Storage::disk('public')->delete($oldFilePath);
            }

            $file = $request->file('dokumen_lpj');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dokumenLpj = $file->storeAs('lpj-gudep/dokumen-lpj', $fileName, 'public');
        }
        
        $lpjGudep->proposal_gudep_id = $request->proposal_gudep_id;
        $lpjGudep->foto_kegiatan     = $fotoKegiatan;
        $lpjGudep->dokumen_lpj       = $dokumenLpj;
        $lpjGudep->evaluasi          = $request->evaluasi;
        $lpjGudep->saran             = $request->saran;

        if($request->status_verifikasi) {
            $lpjGudep->status_verifikasi = $request->status_verifikasi;
            $lpjGudep->verificator_id = Auth::id();

            if($lpjGudep->status_verifikasi === 'Diterima') {
                Mail::to($lpjGudep->user->email)->send(new LpjDiterima(Auth::user(), $lpjGudep));
            } else {
                Mail::to($lpjGudep->user->email)->send(new LpjDitolak(Auth::user(), $lpjGudep));
            }
        }

        $lpjGudep->save();

        DB::commit();

        return redirect()->route('lpj-gudep.index')->with('success', 'Data berhasil di-update!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        $lpjGudep = LpjGudep::find($id);

        // start: delete dokumen di storage ketika data di delete

            // Get the old file path from the database
            $oldFilePath = $lpjGudep->dokumen_lpj;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::disk('public')->exists($oldFilePath)) {
                // Delete the old file
                Storage::disk('public')->delete($oldFilePath);
            }

            // Get the old file path from the database
            $oldFilePath = $lpjGudep->foto_kegiatan;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::disk('public')->exists($oldFilePath)) {
                // Delete the old file
                Storage::disk('public')->delete($oldFilePath);
            }

        // end: delete dokumen di storage ketika data di delete

        $lpjGudep->delete();

        DB::commit();
        return redirect()->route('lpj-gudep.index')->with('success', 'Data berhasil dihapus!');
    }

    public function export()
    {
        return Excel::download(new LpjGudepExport, 'lpj-gudep.xlsx');
    }
}
