<?php

namespace App\Exports;

use App\Models\Proposal\ProposalGudep;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProposalGudepExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $data = ProposalGudep::get()
        ->map(function ($item) {
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
            ];
        });

        return view('exports.proposal.export-gudep', [
            'data' => $data
        ]);
    }
}
