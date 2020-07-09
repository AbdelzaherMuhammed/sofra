<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $table = 'orders';
    public $timestamps = true;
    protected $fillable = array('address', 'payment_method_id', 'cost', 'total', 'net', 'commission', 'state',
        'client_id', 'resturant_id', 'notes' ,'reason_of_rejection');

    public function resturant()
    {
        return $this->belongsTo('App\Models\Resturant');
    }

    public function payment_method()
    {
        return $this->belongsTo('App\Models\PaymentMethod');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product')->withPivot('price' , 'quantity' , 'note');
    }

    public function notifications()
    {
        return $this->hasMany('App\Models\Notification');
    }




}
