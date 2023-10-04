<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory,SoftDeletes;
    protected $hidden = [];
    //protected $appends = ['active_status_name'];

    public static function boot() {
        parent::boot();
        static::deleting(function($data) { // before delete() method call this
            $data->items()->delete();
        });
    }

    function getActiveStatusNameAttribute() {
        /*$status = $this->hasOne(OrderStatusList::class,'id','active_status');
        return $status->name;*/
    }

    public function items(){
        return $this->hasMany(OrderItem::class,'order_id','id');
    }

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }

    public function orderStatus(){
        return $this->hasMany(OrderStatus::class,'order_id','id');
    }
}
