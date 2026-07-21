<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabansurveiModel extends Model
{
    protected $table = 'jawabansurvei';
    protected $primaryKey = 'idjawabansurvei';

    public $timestamps = true;

    protected $fillable = [
        'idpertanyaansurvei',
        'jawaban',
        'created_at',
        'updatet_at',
    ];

    public function pertanyaansurvei()
    {
        return $this->belongsTo(PertanyaansurveiModel::class, 'idpertanyaansurvei');
    }
}
