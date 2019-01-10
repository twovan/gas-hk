<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VipCard extends Model
{

    protected $guarded = [];
    protected $hidden = ['extra'];
    protected $table = 'vip_card';
    protected $primaryKey = "id";
    protected $fields_all;
    public     $timestamps = true;


}
