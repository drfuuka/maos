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

class LpjGudepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if(Auth::user()->role !== 'Gudep') {
            $data['lpjGudep'] = LpjGudep::with('proposal')->get();
        } else {
            $data['lpjGudep'] = LpjGudep::with('proposal')->where('user_id', Auth::id())->get();
        }

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
            'evaluasi'          => ['required', 'string']
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
            'evaluasi'          => $request->evaluasi
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
            'saran'             => ['nullable', 'string'],
            'status_verifikasi' => ['nullable', 'string', 'in:Ditolak,Diterima'],
        ]);

        $lpjGudep = LpjGudep::find($id);

        $fotoKegiatan = $lpjGudep->foto_kegiatan;
        if ($request->hasFile('foto_kegiatan') && $request->foto_kegiatan->isValid()) {
            // Get the old file path from the database
            $oldFilePath = $lpjGudep->foto_kegiatan;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
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
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
            }

            $file = $request->file('dokumen_lpj');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dokumenLpj = $file->storeAs('lpj-gudep/dokumen-lpj', $fileName, 'public');
        }
        
        $lpjGudep->proposal_gudep_id = $request->proposal_gudep_id;
        $lpjGudep->foto_kegiatan     = $fotoKegiatan;
        $lpjGudep->dokumen_lpj       = $dokumenLpj;
        $lpjGudep->evaluasi          = $request->evaluasi;

        // hanya ubah saran jika admin yang ubah
        if(Auth::user()->role === 'Admin') {
            $lpjGudep->saran = $request->saran;
        }

        if($request->status_verifikasi && $request->status_verifikasi !== $lpjGudep->status_verifikasi) {
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
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
            }

            // Get the old file path from the database
            $oldFilePath = $lpjGudep->foto_kegiatan;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
            }

        // end: delete dokumen di storage ketika data di delete

        $lpjGudep->delete();

        DB::commit();
        return redirect()->route('lpj-gudep.index')->with('success', 'Data berhasil dihapus!');
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
        
        $query = LpjGudep::query();
        
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
        $html = view('exports.lpj.export-gudep', $data)->render();
        
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
        return $dompdf->stream('lpj_gudep.pdf');
    }

    public function exportItem($id) {
        $data['data'] = LpjGudep::find($id);

        $userTtd          = $data['data']->user->detail?->ttd;
        $verificatorTtd   = $data['data']->verificator->detail?->ttd;

        $data['ttd_user']        = $userTtd ? base64_encode(Storage::get($userTtd)) : null;
        $data['ttd_verificator'] = $verificatorTtd ? base64_encode(Storage::get($verificatorTtd)) : null;

        // Render the view to HTML
        $html = view('exports.lpj.export-pengesahan-gudep', $data)->render();
        
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
        return $dompdf->stream('Lembar Pengesahan Laporan Pertanggungjawaban Gugus Depan.pdf');
    }
}
