<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $table = 'orders';
    public $timestamps = true;
    protected $fillable = array('address', 'payment_method_id', 'cost', 'sub_total', 'total', 'commission', 'state', 'client_id', 'resturant_id', 'notes', 'special_order_details');

    public function resturent()
    {
        return $this->belongsTo('Resturant');
    }

    public function payment_method()
    {
        return $this->belongsTo('PaymentMethod');
    }

    public function client()
    {
        return $this->blongsTo('App\Models\Client');
    }

    public function products()
    {
        return $this->manyToMany('App\Models\Product' );
    }

    public function notifications()
    {
        return $this->morphToMany('Notification', 'notificatable');
    }

}
