<?php

namespace App\Models;

use App\Models\Laporan\LaporanGudep;
use App\Models\Laporan\LaporanPengurus;
use App\Models\Lpj\LpjGudep;
use App\Models\Lpj\LpjPengurus;
use App\Models\Proposal\ProposalGudep;
use App\Models\Proposal\ProposalPengurus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'ms_user';

    protected $fillable = [
        'fullname',
        'username',
        'email',
        'password',
        'role',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function detail()
    {
        if($this->role === 'Gudep') {
            return $this->hasOne(GudepDetail::class);
        }
        if($this->role === 'Pengurus') {
            return $this->hasOne(PengurusDetail::class);
        }
        if($this->role === 'Ketua') {
            return $this->hasOne(KetuaDetail::class);
        }
    }

    public function laporan()
    {
        if($this->role === 'Gudep') {
            return $this->hasMany(LaporanGudep::class, 'user_id', 'id');
        }
        if($this->role === 'Pengurus') {
            return $this->hasMany(LaporanPengurus::class, 'user_id', 'id');
        }
    }

    public function proposal()
    {
        if($this->role === 'Gudep') {
            return $this->hasMany(ProposalGudep::class, 'user_id', 'id');
        }
        if($this->role === 'Pengurus') {
            return $this->hasMany(ProposalPengurus::class, 'user_id', 'id');
        }
    }

    public function lpj()
    {
        if($this->role === 'Gudep') {
            return $this->hasMany(LpjGudep::class, 'user_id', 'id');
        }
        if($this->role === 'Pengurus') {
            return $this->hasMany(LpjPengurus::class, 'user_id', 'id');
        }
    }
}
