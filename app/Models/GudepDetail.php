<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GudepDetail extends Model
{
    use HasFactory;

    protected $table = 'ms_gudep_detail';

    protected $fillable = [
        'user_id',
        'nama_mabigus',
        'no_hp',
        'ttd'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
