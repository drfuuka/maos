<?php

namespace App\Exports;

use App\Models\Lpj\LpjPengurus;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LpjPengurusExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $data = LpjPengurus::get()
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

        return view('exports.lpj.export-pengurus', [
            'data' => $data
        ]);
    }
}
