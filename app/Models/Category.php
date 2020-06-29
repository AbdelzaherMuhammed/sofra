<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $table = 'categories';
    public $timestamps = true;
    protected $fillable = array('name', 'image' , 'resturant_id');

    public function resturant()
    {
        return $this->belongsTo('App\Models\Resturant');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }

    public function offers()
    {
        return $this->hasMany('App\Models\Offer');
    }

}
