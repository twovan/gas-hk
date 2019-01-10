<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GasNozzle extends Model
{

    protected $table = 'gas_nozzle';

    protected $guarded = [];
    protected $hidden = ['extra'];
    protected $primaryKey = "id";
    protected $fields_all;
    public     $timestamps = false;



}
