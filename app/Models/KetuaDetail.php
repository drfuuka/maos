<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetuaDetail extends Model
{
    use HasFactory;

    protected $table = 'ms_ketua_detail';

    protected $fillable = [
        'user_id',
        'no_hp',
        'ttd'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
