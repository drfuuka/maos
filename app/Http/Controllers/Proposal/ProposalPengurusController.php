<?php

namespace App\Http\Controllers\Proposal;

use App\Exports\ProposalPengurusExport;
use App\Http\Controllers\Controller;
use App\Mail\Proposal\ProposalDiterima;
use App\Mail\Proposal\ProposalDitolak;
use App\Mail\Proposal\VerifikasiProposal;
use App\Models\Proposal\ProposalPengurus;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProposalPengurusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['proposalPengurus'] = ProposalPengurus::get();

        return view('pages.proposal.pengurus.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.proposal.pengurus.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        $request->validate([
            'jenis_proposal'   => ['required', 'string'],
            'dasar_kegiatan'   => ['required', 'string'],
            'maksud_tujuan'    => ['required', 'string'],
            'nama_kegiatan'    => ['required', 'string'],
            'tema_kegiatan'    => ['required', 'string'],
            'kepanitiaan'      => ['required', 'string'],
            'tanggal_kegiatan' => ['required', 'date'],
            'jadwal_kegiatan'  => ['required', 'string'],
            'rincian_dana'     => ['required', 'string'],
            'penutup'          => ['required', 'string'],
            'dokumen_proposal' => ['required', 'file'],
        ]);

        $dokumenProposal = null;
        if ($request->hasFile('dokumen_proposal') && $request->dokumen_proposal->isValid()) {
            $file = $request->file('dokumen_proposal');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dokumenProposal = $file->storeAs('proposal-pengurus/dokumen-proposal', $fileName, 'public');
        }

        $proposal = ProposalPengurus::create([
            'user_id'           => Auth::id(),
            'jenis_proposal'    => $request->jenis_proposal,
            'dasar_kegiatan'    => $request->dasar_kegiatan,
            'maksud_tujuan'     => $request->maksud_tujuan,
            'nama_kegiatan'     => $request->nama_kegiatan,
            'tema_kegiatan'     => $request->tema_kegiatan,
            'kepanitiaan'       => $request->kepanitiaan,
            'tanggal_kegiatan'  => $request->tanggal_kegiatan,
            'jadwal_kegiatan'   => $request->jadwal_kegiatan,
            'rincian_dana'      => $request->rincian_dana,
            'penutup'           => $request->penutup,
            'dokumen_proposal'  => $dokumenProposal,
            'status_verifikasi' => null
        ]);
        
        // get seluruh akun dengan role ketua
        $ketuaEmailList = User::where('role', 'Ketua')->pluck('email')->toArray();

        // kirim email ke seluruh akun dengan role ketua dengan perulangan
        foreach ($ketuaEmailList as $email) {
            Mail::to($email)->send(new VerifikasiProposal(Auth::user(), $proposal));
        }

        DB::commit();

        return redirect()->route('proposal-pengurus.index')->with('success', 'Data berhasil dibuat!');
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
        $data['proposalPengurus'] = ProposalPengurus::find($id);

        return view('pages.proposal.pengurus.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        $request->validate([
            'jenis_proposal'    => ['required', 'string'],
            'dasar_kegiatan'    => ['required', 'string'],
            'maksud_tujuan'     => ['required', 'string'],
            'nama_kegiatan'     => ['required', 'string'],
            'tema_kegiatan'     => ['required', 'string'],
            'kepanitiaan'       => ['required', 'string'],
            'tanggal_kegiatan'  => ['required', 'date'],
            'jadwal_kegiatan'   => ['required', 'string'],
            'rincian_dana'      => ['required', 'string'],
            'penutup'           => ['required', 'string'],
            'dokumen_proposal'  => ['nullable', 'file'],
            'status_verifikasi' => ['nullable', 'string', 'in:Ditolak,Diterima'],
        ]);

        $proposalPengurus = ProposalPengurus::find($id);

        $dokumenProposal = $proposalPengurus->dokumen_proposal;
        if ($request->hasFile('dokumen_proposal') && $request->dokumen_proposal->isValid()) {
            // Get the old file path from the database
            $oldFilePath = $proposalPengurus->dokumen_proposal;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::disk('public')->exists($oldFilePath)) {
                // Delete the old file
                Storage::disk('public')->delete($oldFilePath);
            }

            $file = $request->file('dokumen_proposal');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dokumenProposal = $file->storeAs('proposal-pengurus/dokumen-pendukung', $fileName, 'public');
        }

        $proposalPengurus->jenis_proposal    = $request->jenis_proposal;
        $proposalPengurus->dasar_kegiatan    = $request->dasar_kegiatan;
        $proposalPengurus->maksud_tujuan     = $request->maksud_tujuan;
        $proposalPengurus->nama_kegiatan     = $request->nama_kegiatan;
        $proposalPengurus->tema_kegiatan     = $request->tema_kegiatan;
        $proposalPengurus->kepanitiaan       = $request->kepanitiaan;
        $proposalPengurus->tanggal_kegiatan  = $request->tanggal_kegiatan;
        $proposalPengurus->jadwal_kegiatan   = $request->jadwal_kegiatan;
        $proposalPengurus->rincian_dana      = $request->rincian_dana;
        $proposalPengurus->penutup           = $request->penutup;
        $proposalPengurus->dokumen_proposal  = $dokumenProposal;

        if($request->status_verifikasi) {
            $proposalPengurus->status_verifikasi = $request->status_verifikasi;
            $proposalPengurus->verificator_id = Auth::id();

            if($proposalPengurus->status_verifikasi === 'Diterima') {
                Mail::to($proposalPengurus->user->email)->send(new ProposalDiterima(Auth::user(), $proposalPengurus));
            } else {
                Mail::to($proposalPengurus->user->email)->send(new ProposalDitolak(Auth::user(), $proposalPengurus));
            }
        }

        $proposalPengurus->save();

        DB::commit();

        return redirect()->route('proposal-pengurus.index')->with('success', 'Data berhasil di-update!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        $proposalPengurus = ProposalPengurus::find($id);

        // start: delete dokumen di storage ketika data di delete

            // Get the old file path from the database
            $oldFilePath = $proposalPengurus->dokumen_proposal;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::disk('public')->exists($oldFilePath)) {
                // Delete the old file
                Storage::disk('public')->delete($oldFilePath);
            }

        // end: delete dokumen di storage ketika data di delete

        $proposalPengurus->delete();

        DB::commit();
        return redirect()->route('proposal-pengurus.index')->with('success', 'Data berhasil dihapus!');
    }

    public function export()
    {
        return Excel::download(new ProposalPengurusExport, 'proposal-pengurus.xlsx');
    }
}
