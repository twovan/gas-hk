<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{

    protected $table = 'order_log';

    protected $guarded = [];
    protected $hidden = ['extra'];
    protected $primaryKey = "id";
    protected $fields_all;
    public $timestamps = false;



}
