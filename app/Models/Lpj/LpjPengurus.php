<?php

namespace App\Models\Lpj;

use App\Models\Proposal\ProposalPengurus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LpjPengurus extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tr_lpj_pengurus';
    
    protected $fillable = [
        'proposal_pengurus_id',
        'user_id',
        'foto_kegiatan',
        'dokumen_lpj',
        'evaluasi',
        'saran',
        'status_verifikasi',
        'verificator_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function verificator()
    {
        return $this->belongsTo(User::class, 'verificator_id', 'id');
    }

    public function proposal()
    {
        return $this->belongsTo(Proposalpengurus::class, 'proposal_pengurus_id', 'id');
    }
}
