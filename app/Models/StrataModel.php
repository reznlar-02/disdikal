<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrataModel extends Model
{
    protected $table = 'strata';
    protected $primaryKey = 'idstrata';

    public $timestamps = false;

    protected $fillable = [
        'strata'
    ];
}
