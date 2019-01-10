<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RechargeRule extends Model
{

    protected $guarded = [];
    protected $hidden = ['extra'];
    protected $table = 'recharge_rules';
    protected $primaryKey = "id";
    protected $fields_all;
    public     $timestamps = true;

}
