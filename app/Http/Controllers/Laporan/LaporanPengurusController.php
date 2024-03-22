<?php

namespace App\Http\Controllers\Laporan;

use App\Exports\LaporanPengurusExport;
use App\Http\Controllers\Controller;
use App\Models\Laporan\LaporanPengurus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPengurusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['laporanGudep'] = LaporanPengurus::get();

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
        $data['laporanGudep'] = LaporanPengurus::find($id);

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

        $laporanGudep = LaporanPengurus::find($id);

        $fotoKegiatan = $laporanGudep->foto_kegiatan;
        if ($request->hasFile('foto_kegiatan') && $request->foto_kegiatan->isValid()) {
            // Get the old file path from the database
            $oldFilePath = $laporanGudep->foto_kegiatan;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::disk('public')->exists($oldFilePath)) {
                // Delete the old file
                Storage::disk('public')->delete($oldFilePath);
            }

            $file = $request->file('foto_kegiatan');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fotoKegiatan = $file->storeAs('laporan-pengurus/foto-kegiatan', $fileName, 'public');
        }

        $dokumenPendukung = $laporanGudep->dokumen_pendukung;
        if ($request->hasFile('dokumen_pendukung') && $request->dokumen_pendukung->isValid()) {
            // Get the old file path from the database
            $oldFilePath = $laporanGudep->dokumen_pendukung;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::disk('public')->exists($oldFilePath)) {
                // Delete the old file
                Storage::disk('public')->delete($oldFilePath);
            }

            $file = $request->file('dokumen_pendukung');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dokumenPendukung = $file->storeAs('laporan-pengurus/dokumen-pendukung', $fileName, 'public');
        }

        $laporanGudep->user_id           = Auth::id();
        $laporanGudep->nama_kegiatan     = $request->nama_kegiatan;
        $laporanGudep->tanggal_kegiatan  = $request->tanggal_kegiatan;
        $laporanGudep->tempat_kegiatan   = $request->tempat_kegiatan;
        $laporanGudep->jumlah_peserta    = $request->jumlah_peserta;
        $laporanGudep->foto_kegiatan     = $fotoKegiatan;
        $laporanGudep->evaluasi_kegiatan = $request->evaluasi_kegiatan;
        $laporanGudep->dokumen_pendukung = $dokumenPendukung;
        $laporanGudep->save();

        DB::commit();

        return redirect()->route('laporan-pengurus.index')->with('success', 'Data berhasil di-update!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        $laporanGudep = LaporanPengurus::find($id);

        // start: delete dokumen di storage ketika data di delete

            // Get the old file path from the database
            $oldFilePath = $laporanGudep->foto_kegiatan;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::disk('public')->exists($oldFilePath)) {
                // Delete the old file
                Storage::disk('public')->delete($oldFilePath);
            }

            // Get the old file path from the database
            $oldFilePath = $laporanGudep->dokumen_pendukung;

            // Check if the old file path is not null and the file exists
            if ($oldFilePath !== null && Storage::disk('public')->exists($oldFilePath)) {
                // Delete the old file
                Storage::disk('public')->delete($oldFilePath);
            }

        // end: delete dokumen di storage ketika data di delete

        $laporanGudep->delete();

        DB::commit();
        return redirect()->route('laporan-pengurus.index')->with('success', 'Data berhasil dihapus!');
    }   

    public function export()
    {
        return Excel::download(new LaporanPengurusExport, 'laporan-pengurus.xlsx');
    }
}
