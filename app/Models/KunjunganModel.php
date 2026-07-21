<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KunjunganModel extends Model
{
    protected $table = 'kunjungan';
    protected $primaryKey = 'idkunjungan';

    public $timestamps = true;

    protected $fillable = [
        'nama',
        'angkatan',
        'email',
        'idpendidikan',
        'created_at',
        'updated_at',
    ];

    public function pendidikan()
    {
        return $this->belongsTo(PendidikanModel::class, 'idpendidikan');
    }
}
