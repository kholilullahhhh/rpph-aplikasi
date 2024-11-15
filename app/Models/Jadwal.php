<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;
    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'tema_id',
        'modul_id',
        'tgl_jadwal',
        'modul_id',
        'jam_mulai',
        'jam_selesai',
    ];

    // Relasi dengan siswa
    public function siswa()
    {
        return $this->belongsTo(siswa::class,'id', 'siswa_id');
    }

    // Relasi dengan kelas
    public function kelas()
    {
        return $this->belongsTo(kelas::class,'id','kelas_id');
    }

    // Relasi dengan tema
    public function tema()
    {
        return $this->belongsTo(Tema::class,'id','tema_id');
    }

    // Relasi dengan modul pembelajaran
    public function modul()
    {
        return $this->belongsTo(modul::class,'id','modul_id');
    }
}
