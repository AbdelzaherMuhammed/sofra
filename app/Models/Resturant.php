<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resturant extends Model
{

    protected $table = 'resturants';
    public $timestamps = true;
    protected $fillable = array('name', 'minimum_charge', 'delivery_fees', 'status', 'image', 'neighborhood_id', 'email', 'password', 'delivery_time', 'phone', 'whatsapp' , 'pin_code');
    protected $appends = ['review'];

    public function getReviewAttribute()
    {
        return $this->reviews()->avg('review');
    }


    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    public function offers()
    {
        return $this->hasMany('App\Models\Offer');
    }

    public function neighborhood()
    {
        return $this->belongsTo('App\Models\Neighborhood');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Payment');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Review');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }

    public function categories()
    {
        return $this->hasMany('App\Models\Category');
    }

    public function notifications()
    {
        return $this->morphMany('App\Models\Notification', 'notifiable');
    }

    public function contacts()
    {
        return $this->morphMany('App\Models\Contact', 'contactable');
    }

    public function tokens()
    {
        return $this->hasMany('App\Models\Token');
    }

    protected $hidden = [
        'password', 'api_token','pin_code'
    ];



}
