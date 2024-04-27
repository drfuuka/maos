<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Laporan\LaporanPengurus;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LaporanPengurusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(Auth::user()->role !== 'Pengurus') {
            $data['laporanPengurus'] = LaporanPengurus::get();
        } else {
            $data['laporanPengurus'] = LaporanPengurus::where('user_id', Auth::id())->get();
        }

        return view('pages.laporan.pengurus.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.laporan.pengurus.create');
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
            $fotoKegiatan = $file->storeAs('laporan-pengurus/foto-kegiatan', $fileName, 'public');
        }

        $dokumenPendukung = null;
        if ($request->hasFile('dokumen_pendukung') && $request->dokumen_pendukung->isValid()) {
            $file = $request->file('dokumen_pendukung');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dokumenPendukung = $file->storeAs('laporan-pengurus/dokumen-pendukung', $fileName, 'public');
        }

        LaporanPengurus::create([
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

        return redirect()->route('laporan-pengurus.index')->with('success', 'Data berhasil dibuat!');
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
        $data['laporanPengurus'] = LaporanPengurus::find($id);

        return view('pages.laporan.pengurus.edit', $data);
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

        $laporanPengurus = LaporanPengurus::find($id);

        $fotoKegiatan = $laporanPengurus->foto_kegiatan;
        if ($request->hasFile('foto_kegiatan') && $request->foto_kegiatan->isValid()) {
            // Get the old file path from the database
            $oldFilePath = $laporanPengurus->foto_kegiatan;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
            }

            $file = $request->file('foto_kegiatan');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fotoKegiatan = $file->storeAs('laporan-pengurus/foto-kegiatan', $fileName, 'public');
        }

        $dokumenPendukung = $laporanPengurus->dokumen_pendukung;
        if ($request->hasFile('dokumen_pendukung') && $request->dokumen_pendukung->isValid()) {
            // Get the old file path from the database
            $oldFilePath = $laporanPengurus->dokumen_pendukung;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
            }

            $file = $request->file('dokumen_pendukung');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dokumenPendukung = $file->storeAs('laporan-pengurus/dokumen-pendukung', $fileName, 'public');
        }

        $laporanPengurus->user_id           = Auth::id();
        $laporanPengurus->nama_kegiatan     = $request->nama_kegiatan;
        $laporanPengurus->tanggal_kegiatan  = $request->tanggal_kegiatan;
        $laporanPengurus->tempat_kegiatan   = $request->tempat_kegiatan;
        $laporanPengurus->jumlah_peserta    = $request->jumlah_peserta;
        $laporanPengurus->foto_kegiatan     = $fotoKegiatan;
        $laporanPengurus->evaluasi_kegiatan = $request->evaluasi_kegiatan;
        $laporanPengurus->dokumen_pendukung = $dokumenPendukung;
        $laporanPengurus->save();

        DB::commit();

        return redirect()->route('laporan-pengurus.index')->with('success', 'Data berhasil di-update!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        $laporanPengurus = LaporanPengurus::find($id);

        // start: delete dokumen di storage ketika data di delete

            // Get the old file path from the database
            $oldFilePath = $laporanPengurus->foto_kegiatan;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
            }

            // Get the old file path from the database
            $oldFilePath = $laporanPengurus->dokumen_pendukung;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::exists($oldFilePath)) {
                // Delete the old file
                Storage::delete($oldFilePath);
            }

        // end: delete dokumen di storage ketika data di delete

        $laporanPengurus->delete();

        DB::commit();
        return redirect()->route('laporan-pengurus.index')->with('success', 'Data berhasil dihapus!');
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
        
        $query = LaporanPengurus::query();
        
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
        $html = view('exports.laporan.export-pengurus', $data)->render();
        
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
        return $dompdf->stream('laporan_pengurus.pdf');
    }
}
