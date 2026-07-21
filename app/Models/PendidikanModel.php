<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendidikanModel extends Model
{
    protected $table = 'pendidikan';
    protected $primaryKey = 'idpendidikan';

    public $timestamps = false;

    protected $fillable = [
        'idstrata',
        'pendidikan',
    ];

    public function strata()
    {
        return $this->belongsTo(StrataModel::class, 'idstrata');
    }
}
