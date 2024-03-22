<?php

namespace App\Models\Proposal;

use App\Models\Lpj\LpjGudep;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProposalGudep extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tr_proposal_gudep';
    
    protected $fillable = [
        'user_id',
        'jenis_proposal',
        'dasar_kegiatan',
        'maksud_tujuan',
        'nama_kegiatan',
        'tema_kegiatan',
        'kepanitiaan',
        'tanggal_kegiatan',
        'jadwal_kegiatan',
        'rincian_dana',
        'penutup',
        'dokumen_proposal',
        'status_verifikasi',
        'verificator_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function lpj()
    {
        return $this->hasOne(LpjGudep::class, 'proposal_gudep_id', 'id');
    }

    public function verificator()
    {
        return $this->belongsTo(User::class, 'verificator_id', 'id');
    }
}
