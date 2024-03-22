<?php

namespace App\Exports;

use App\Models\Laporan\LaporanGudep;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanGudepExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $data = LaporanGudep::get()
        ->map(function ($item) {
            return [
                'nama_kegiatan'     => $item->nama_kegiatan,
                'tanggal_kegiatan'  => $item->tanggal_kegiatan,
                'tempat_kegiatan'   => $item->tempat_kegiatan,
                'jumlah_peserta'    => $item->jumlah_peserta,
                'dibuat_oleh'       => $item->user->fullname,
                'evaluasi_kegiatan' => $item->evaluasi_kegiatan
            ];
        });

        return view('exports.laporan.export-gudep', [
            'data' => $data
        ]);
    }
}
