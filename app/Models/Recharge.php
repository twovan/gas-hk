<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recharge extends Model
{

    protected $guarded = [];
    protected $hidden = ['extra'];
    protected $table = 'recharges';
    protected $primaryKey = "id";
    protected $fields_all;
    public     $timestamps = true;


}
