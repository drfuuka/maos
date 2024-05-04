<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Laporan\LaporanGudep;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LaporanGudepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if(Auth::user()->role !== 'Gudep') {
            $data['laporanGudep'] = LaporanGudep::get();
        } else {
            $data['laporanGudep'] = LaporanGudep::where('user_id', Auth::id())->get();
        }

        return view('pages.laporan.gudep.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.laporan.gudep.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        $request->validate([
            'nama_kegiatan'     => ['required','string'],
            'tanggal_kegiatan'  => ['required','date'],
            'tempat_kegiatan'   => ['required','string'],
            'jumlah_peserta'    => ['required','string'],
            'foto_kegiatan'     => ['required','file'],
            'evaluasi_kegiatan' => ['required','string'],
            'dokumen_pendukung' => ['required','file'],
        ]);

        $fotoKegiatan = null;
        if ($request->hasFile('foto_kegiatan') && $request->foto_kegiatan->isValid()) {
            $file = $request->file('foto_kegiatan');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fotoKegiatan = $file->storeAs('laporan-gudep/foto-kegiatan', $fileName, 'public');
        }

        $dokumenPendukung = null;
        if ($request->hasFile('dokumen_pendukung') && $request->dokumen_pendukung->isValid()) {
            $file = $request->file('dokumen_pendukung');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dokumenPendukung = $file->storeAs('laporan-gudep/dokumen-pendukung', $fileName, 'public');
        }

        LaporanGudep::create([
            'user_id'           => Auth::id(),
            'nama_kegiatan'     => $request->nama_kegiatan,
            'tanggal_kegiatan'  => $request->tanggal_kegiatan,
            'tempat_kegiatan'   => $request->tempat_kegiatan,
            'jumlah_peserta'    => $request->jumlah_peserta,
            'foto_kegiatan'     => $fotoKegiatan,
            'evaluasi_kegiatan' => $request->evaluasi_kegiatan,
            'dokumen_pendukung' => $dokumenPendukung
        ]);

        DB::commit();

        return redirect()->route('laporan-gudep.index')->with('success', 'Data berhasil dibuat!');
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
        $data['laporanGudep'] = LaporanGudep::find($id);

        return view('pages.laporan.gudep.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        $request->validate([
            'nama_kegiatan'     => ['required','string'],
            'tanggal_kegiatan'  => ['required','date'],
            'tempat_kegiatan'   => ['required','string'],
            'jumlah_peserta'    => ['required','string'],
            'foto_kegiatan'     => ['nullable','file'],
            'evaluasi_kegiatan' => ['required','string'],
            'dokumen_pendukung' => ['nullable','file'],
        ]);

        $laporanGudep = LaporanGudep::find($id);

        $fotoKegiatan = $laporanGudep->foto_kegiatan;
        if ($request->hasFile('foto_kegiatan') && $request->foto_kegiatan->isValid()) {
            // Get the old file path from the database
            $oldFilePath = $laporanGudep->foto_kegiatan;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
            }

            $file = $request->file('foto_kegiatan');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fotoKegiatan = $file->storeAs('laporan-gudep/foto-kegiatan', $fileName, 'public');
        }

        $dokumenPendukung = $laporanGudep->dokumen_pendukung;
        if ($request->hasFile('dokumen_pendukung') && $request->dokumen_pendukung->isValid()) {
            // Get the old file path from the database
            $oldFilePath = $laporanGudep->dokumen_pendukung;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
            }

            $file = $request->file('dokumen_pendukung');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dokumenPendukung = $file->storeAs('laporan-gudep/dokumen-pendukung', $fileName, 'public');
        }

        if(Auth::user()->role === 'Gudep' || Auth::user()->role === 'Admin') {
            $laporanGudep->user_id           = Auth::id();
            $laporanGudep->nama_kegiatan     = $request->nama_kegiatan;
            $laporanGudep->tanggal_kegiatan  = $request->tanggal_kegiatan;
            $laporanGudep->tempat_kegiatan   = $request->tempat_kegiatan;
            $laporanGudep->jumlah_peserta    = $request->jumlah_peserta;
            $laporanGudep->foto_kegiatan     = $fotoKegiatan;
            $laporanGudep->evaluasi_kegiatan = $request->evaluasi_kegiatan;
            $laporanGudep->dokumen_pendukung = $dokumenPendukung;
        }

        $laporanGudep->save();

        DB::commit();

        return redirect()->route('laporan-gudep.index')->with('success', 'Data berhasil di-update!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(Auth::user()->role !== 'Admin') {
            return redirect()->route('laporan-gudep.index')->with('error', 'Kamu tidak memiliki akses');
        }

        DB::beginTransaction();

        $laporanGudep = LaporanGudep::find($id);

        // start: delete dokumen di storage ketika data di delete

            // Get the old file path from the database
            $oldFilePath = $laporanGudep->foto_kegiatan;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
            }

            // Get the old file path from the database
            $oldFilePath = $laporanGudep->dokumen_pendukung;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
            }

        // end: delete dokumen di storage ketika data di delete

        $laporanGudep->delete();

        DB::commit();
        return redirect()->route('laporan-gudep.index')->with('success', 'Data berhasil dihapus!');
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
        
        $query = LaporanGudep::query();

        if(Auth::user()->role === 'Gudep') {
            $query->where('user_id', Auth::id());
        }
        
        if ($request->filter_tanggal) {
            $query->whereBetween('created_at', [$dari_tanggal, $sampai_tanggal]);
        }
        
        $data['data'] = $query->get()->map(function ($item) {
            return [
                'nama_kegiatan'     => $item->nama_kegiatan,
                'tanggal_kegiatan'  => $item->tanggal_kegiatan,
                'tempat_kegiatan'   => $item->tempat_kegiatan,
                'jumlah_peserta'    => $item->jumlah_peserta,
                'dibuat_oleh'       => $item->user->fullname,
                'dibuat_tanggal'    => Carbon::create($item->created_at)->format('d M Y'),
                'evaluasi_kegiatan' => $item->evaluasi_kegiatan
            ];
        });        
    
        // Render the view to HTML
        $html = view('exports.laporan.export-gudep', $data)->render();
        
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
        return $dompdf->stream('laporan_gudep.pdf');
    }
}
