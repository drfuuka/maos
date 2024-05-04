<?php

namespace App\Http\Controllers\Proposal;

use App\Http\Controllers\Controller;
use App\Mail\Proposal\ProposalDiterima;
use App\Mail\Proposal\ProposalDitolak;
use App\Mail\Proposal\VerifikasiProposal;
use App\Models\Proposal\ProposalGudep;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProposalGudepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(Auth::user()->role !== 'Gudep') {
            $data['proposalGudep'] = ProposalGudep::get();
        } else {
            $data['proposalGudep'] = ProposalGudep::where('user_id', Auth::id())->get();

        }

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
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
            }

            $file = $request->file('dokumen_proposal');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dokumenProposal = $file->storeAs('proposal-gudep/dokumen-pendukung', $fileName, 'public');
        }

        if(Auth::user()->role === 'Gudep' || Auth::user()->role === 'Admin') {
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
        }

        if($request->status_verifikasi && $request->status_verifikasi !== $proposalGudep->status_verifikasi) {
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
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
            }

        // end: delete dokumen di storage ketika data di delete

        $proposalGudep->delete();
        $proposalGudep->lpj->delete();

        DB::commit();
        return redirect()->route('proposal-gudep.index')->with('success', 'Data berhasil dihapus!');
    }

    public function export(Request $request)
    {
        $request->validate([
            'dari_tanggal'   => [Rule::requiredIf($request->filter_tanggal !== null), 'nullable', 'date'],
            'sampai_tanggal' => [Rule::requiredIf($request->filter_tanggal !== null), 'nullable', 'date', 'after_or_equal:dari_tanggal'],
        ]);
        
        $dari_tanggal = Carbon::create($request->dari_tanggal);
        $sampai_tanggal = Carbon::create($request->sampai_tanggal);
        
        $data['tanggal'] = $request->filter_tanggal ? $dari_tanggal->format('d M Y') . ' - ' . $sampai_tanggal->format('d M Y') : 'Seluruh Tanggal';
        
        $query = ProposalGudep::query();

        if(Auth::user()->role === 'Gudep') {
            $query->where('user_id', Auth::id());
        }
        
        if ($request->filter_tanggal) {
            $query->whereBetween('created_at', [$dari_tanggal, $sampai_tanggal]);
        }
        
        $data['data'] = $query->get()->map(function ($item) {
            return [
                'dibuat_oleh'       => $item->user->fullname,
                'jenis_proposal'    => $item->jenis_proposal,
                'dasar_kegiatan'    => $item->dasar_kegiatan,
                'maksud_tujuan'     => $item->maksud_tujuan,
                'nama_kegiatan'     => $item->nama_kegiatan,
                'tema_kegiatan'     => $item->tema_kegiatan,
                'kepanitiaan'       => $item->kepanitiaan,
                'tanggal_kegiatan'  => $item->tanggal_kegiatan,
                'jadwal_kegiatan'   => $item->jadwal_kegiatan,
                'rincian_dana'      => $item->rincian_dana,
                'penutup'           => $item->penutup,
                'status_verifikasi' => $item->status_verifikasi,
                'diverifikasi_oleh' => $item->verificator?->fullname ?? '-',
                'dibuat_oleh'       => $item->user->fullname,
                'dibuat_tanggal'    => Carbon::create($item->created_at)->format('d M Y'),
            ];
        });        
    
        // Render the view to HTML
        $html = view('exports.proposal.export-gudep', $data)->render();
        
        // Setup Dompdf options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        // Instantiate Dompdf with options
        $dompdf = new Dompdf($options);
        
        // Load HTML content
        $dompdf->loadHtml($html);
        
        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        
        // Render PDF (output to browser or file)
        $dompdf->render();
        
        // Output PDF to the browser
        return $dompdf->stream('proposal_gudep.pdf');
    }

    public function exportItem($id) {
        $data['data'] = ProposalGudep::find($id);

        $userTtd          = $data['data']->user->detail?->ttd;
        $verificatorTtd   = $data['data']->verificator->detail?->ttd;

        $data['ttd_user']        = $userTtd ? base64_encode(Storage::get($userTtd)) : null;
        $data['ttd_verificator'] = $verificatorTtd ? base64_encode(Storage::get($verificatorTtd)) : null;

        // Render the view to HTML
        $html = view('exports.proposal.export-pengesahan-gudep', $data)->render();
        
        // Setup Dompdf options
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        
        // Instantiate Dompdf with options
        $dompdf = new Dompdf($options);
        
        // Load HTML content
        $dompdf->loadHtml($html);
        
        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        
        // Render PDF (output to browser or file)
        $dompdf->render();
        
        // Output PDF to the browser
        return $dompdf->stream('Lembar Pengesahan Proposal Gugus Depan.pdf');
    }
}
