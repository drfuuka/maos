<?php

namespace App\Http\Controllers\Lpj;

use App\Http\Controllers\Controller;
use App\Models\Lpj\LpjPengurus;
use App\Models\Proposal\ProposalPengurus;
use App\Exports\LpjPengurusExport;
use App\Mail\Lpj\LpjDiterima;
use App\Mail\Lpj\LpjDitolak;
use App\Mail\Lpj\VerifikasiLpj;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class LpjPengurusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if(Auth::user()->role !== 'Pengurus') {
            $data['lpjPengurus'] = LpjPengurus::with('proposal')->get();
        } else {
            $data['lpjPengurus'] = LpjPengurus::with('proposal')->where('user_id', Auth::id())->get();
        }

        return view('pages.lpj.pengurus.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['proposalOption'] = ProposalPengurus::where(([
            'user_id'           => Auth::id(),
            'status_verifikasi' => 'Diterima',
        ]))->get();

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
            'saran'             => ['nullable', 'string'],
            'status_verifikasi' => ['nullable', 'string', 'in:Ditolak,Diterima'],
        ]);

        $lpjPengurus = LpjPengurus::find($id);

        $fotoKegiatan = $lpjPengurus->foto_kegiatan;
        if ($request->hasFile('foto_kegiatan') && $request->foto_kegiatan->isValid()) {
            // Get the old file path from the database
            $oldFilePath = $lpjPengurus->foto_kegiatan;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
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
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
            }

            $file = $request->file('dokumen_lpj');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dokumenLpj = $file->storeAs('lpj-pengurus/dokumen-lpj', $fileName, 'public');
        }
        
        if(Auth::user()->role === 'Pengurus' || Auth::user()->role === 'Admin') {
            $lpjPengurus->proposal_pengurus_id = $request->proposal_pengurus_id;
            $lpjPengurus->foto_kegiatan     = $fotoKegiatan;
            $lpjPengurus->dokumen_lpj       = $dokumenLpj;
            $lpjPengurus->evaluasi          = $request->evaluasi;
        }

        // hanya ubah saran jika admin yang ubah
        if(Auth::user()->role === 'Admin') {
            $lpjPengurus->saran             = $request->saran;
        }

        if($request->status_verifikasi && $request->status_verifikasi !== $lpjPengurus->status_verifikasi) {
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
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
            }

            // Get the old file path from the database
            $oldFilePath = $lpjPengurus->foto_kegiatan;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
            }

        // end: delete dokumen di storage ketika data di delete

        $lpjPengurus->delete();

        DB::commit();
        return redirect()->route('lpj-pengurus.index')->with('success', 'Data berhasil dihapus!');
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
        
        $query = LpjPengurus::query();

        if(Auth::user()->role === 'Pengurus') {
            $query->where('user_id', Auth::id());
        }
        
        if ($request->filter_tanggal) {
            $query->whereBetween('created_at', [$dari_tanggal, $sampai_tanggal]);
        }
        
        $data['data'] = $query->get()->map(function ($item) {
            return [
                'nama_proposal'     => $item->proposal->nama_kegiatan,
                'dibuat_oleh'       => $item->user->fullname,
                'evaluasi'          => $item->evaluasi,
                'saran'             => $item->saran,
                'status_verifikasi' => $item->status_verifikasi,
                'diverifikasi_oleh' => $item->verificator?->fullname ?? '-',
                'dibuat_oleh'       => $item->user->fullname,
                'dibuat_tanggal'    => Carbon::create($item->created_at)->format('d M Y'),
            ];
        });        
    
        // Render the view to HTML
        $html = view('exports.lpj.export-pengurus', $data)->render();
        
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
        return $dompdf->stream('lpj_pengurus.pdf');
    }

    public function exportItem($id) {
        $data['data'] = LpjPengurus::find($id);

        $userTtd          = $data['data']->user->detail?->ttd;
        $verificatorTtd   = $data['data']->verificator->detail?->ttd;

        $data['ttd_user']        = $userTtd ? base64_encode(Storage::get($userTtd)) : null;
        $data['ttd_verificator'] = $verificatorTtd ? base64_encode(Storage::get($verificatorTtd)) : null;

        // Render the view to HTML
        $html = view('exports.lpj.export-pengesahan-pengurus', $data)->render();
        
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
        return $dompdf->stream('Lembar Pengesahan Laporan Pertanggungjawaban Pengurus.pdf');
    }
}
