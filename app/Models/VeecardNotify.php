<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VeecardNotify extends Model
{

    protected $table = 'veecard_notify';

    protected $guarded = [];
    protected $hidden = ['extra'];
    protected $primaryKey = "ID";
    protected $fields_all;
    public $timestamps = false;



}
