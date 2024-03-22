<?php

namespace App\Models\Laporan;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaporanGudep extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tr_laporan_gudep';

    protected $fillable = [
        'user_id',
        'nama_kegiatan',
        'tanggal_kegiatan',
        'tempat_kegiatan',
        'jumlah_peserta',
        'foto_kegiatan',
        'evaluasi_kegiatan',
        'dokumen_pendukung'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
