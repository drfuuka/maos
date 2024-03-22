<?php

namespace App\Http\Controllers\Proposal;

use App\Exports\ProposalGudepExport;
use App\Http\Controllers\Controller;
use App\Mail\Proposal\ProposalDiterima;
use App\Mail\Proposal\ProposalDitolak;
use App\Mail\Proposal\VerifikasiProposal;
use App\Models\Proposal\ProposalGudep;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProposalGudepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['proposalGudep'] = ProposalGudep::get();

        return view('pages.proposal.gudep.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.proposal.gudep.create');
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
            'dokumen_proposal' => ['required','file'],
        ]);

        $dokumenProposal = null;
        if ($request->hasFile('dokumen_proposal') && $request->dokumen_proposal->isValid()) {
            $file = $request->file('dokumen_proposal');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dokumenProposal = $file->storeAs('proposal-gudep/dokumen-proposal', $fileName, 'public');
        }

        $proposal = ProposalGudep::create([
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

        return redirect()->route('proposal-gudep.index')->with('success', 'Data berhasil dibuat!');
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
        $data['proposalGudep'] = ProposalGudep::find($id);

        return view('pages.proposal.gudep.edit', $data);
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
            'dokumen_proposal'  => ['nullable'],
            'status_verifikasi' => ['nullable', 'string', 'in:Ditolak,Diterima'],
        ]);

        $proposalGudep = ProposalGudep::find($id);

        $dokumenProposal = $proposalGudep->dokumen_proposal;
        if ($request->hasFile('dokumen_proposal') && $request->dokumen_proposal->isValid()) {
            
            // Get the old file path from the database
            $oldFilePath = $proposalGudep->dokumen_proposal;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::disk('public')->exists($oldFilePath)) {
                // Delete the old file
                Storage::disk('public')->delete($oldFilePath);
            }

            $file = $request->file('dokumen_proposal');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dokumenProposal = $file->storeAs('proposal-gudep/dokumen-pendukung', $fileName, 'public');
        }

        $proposalGudep->jenis_proposal    = $request->jenis_proposal;
        $proposalGudep->dasar_kegiatan    = $request->dasar_kegiatan;
        $proposalGudep->maksud_tujuan     = $request->maksud_tujuan;
        $proposalGudep->nama_kegiatan     = $request->nama_kegiatan;
        $proposalGudep->tema_kegiatan     = $request->tema_kegiatan;
        $proposalGudep->kepanitiaan       = $request->kepanitiaan;
        $proposalGudep->tanggal_kegiatan  = $request->tanggal_kegiatan;
        $proposalGudep->jadwal_kegiatan   = $request->jadwal_kegiatan;
        $proposalGudep->rincian_dana      = $request->rincian_dana;
        $proposalGudep->penutup           = $request->penutup;
        $proposalGudep->dokumen_proposal  = $dokumenProposal;

        if($request->status_verifikasi) {
            $proposalGudep->status_verifikasi = $request->status_verifikasi;
            $proposalGudep->verificator_id = Auth::id();

            if($proposalGudep->status_verifikasi === 'Diterima') {
                Mail::to($proposalGudep->user->email)->send(new ProposalDiterima(Auth::user(), $proposalGudep));
            } else {
                Mail::to($proposalGudep->user->email)->send(new ProposalDitolak(Auth::user(), $proposalGudep));
            }
        }

        $proposalGudep->save();

        DB::commit();

        return redirect()->route('proposal-gudep.index')->with('success', 'Data berhasil di-update!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        $proposalGudep = ProposalGudep::find($id);

        // start: delete dokumen di storage ketika data di delete

            // Get the old file path from the database
            $oldFilePath = $proposalGudep->dokumen_proposal;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::disk('public')->exists($oldFilePath)) {
                // Delete the old file
                Storage::disk('public')->delete($oldFilePath);
            }

        // end: delete dokumen di storage ketika data di delete

        $proposalGudep->delete();
        $proposalGudep->lpj->delete();

        DB::commit();
        return redirect()->route('proposal-gudep.index')->with('success', 'Data berhasil dihapus!');
    }

    public function export()
    {
        return Excel::download(new ProposalGudepExport, 'proposal-gudep.xlsx');
    }
}
