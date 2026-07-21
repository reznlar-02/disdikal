<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PertanyaansurveiModel extends Model
{
    protected $table = 'pertanyaansurvei';
    protected $primaryKey = 'idpertanyaansurvei';

    public $timestamps = false;

    protected $fillable = [
        'idpendidikan',
        'pertanyaan',
    ];

    public function pendidikan()
    {
        return $this->belongsTo(PendidikanModel::class, 'idpendidikan');
    }
}
