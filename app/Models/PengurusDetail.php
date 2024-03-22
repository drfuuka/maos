<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengurusDetail extends Model
{
    use HasFactory;

    protected $table = 'ms_pengurus_detail';

    protected $fillable = [
        'user_id',
        'nama_pengaju',
        'jabatan',
        'no_hp',
        'ttd'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
