<?php

namespace App\Exports;

use App\Models\Lpj\LpjGudep;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LpjGudepExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $data = LpjGudep::get()
        ->map(function ($item) {
            return [
                'nama_proposal'     => $item->proposal->nama_kegiatan,
                'dibuat_oleh'       => $item->user->fullname,
                'evaluasi'          => $item->evaluasi,
                'saran'             => $item->saran,
                'status_verifikasi' => $item->status_verifikasi,
                'diverifikasi_oleh' => $item->verificator?->fullname ?? '-',
            ];
        });

        return view('exports.lpj.export-gudep', [
            'data' => $data
        ]);
    }
}
