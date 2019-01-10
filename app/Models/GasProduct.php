<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GasProduct extends Model
{

    protected $table = 'gas_products';

    protected $guarded = [];
    protected $hidden = ['extra'];
    protected $primaryKey = "id";
    protected $fields_all;
    public     $timestamps = true;



}
