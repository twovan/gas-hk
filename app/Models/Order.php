<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\VipCard;

class Order extends Model
{

    protected $table = 'order';

    protected $guarded = [];
    protected $hidden = ['extra'];
    protected $primaryKey = "id";
    protected $fields_all;
    public     $timestamps = true;

    public function vipcard(){
        return $this->hasOne(VipCard::class, 'openid', 'open_id');
    }

}
