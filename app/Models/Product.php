<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $table = 'products';
    public $timestamps = true;
    protected $fillable = array('name', 'description', 'image', 'price', 'price_in_offer', 'category_id', 'resturant_id');
    protected $appends=['is_offer'];

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function resturant()
    {
        return $this->belongsTo('App\Models\Resturant');
    }
    public function orders()
    {
        return $this->manyToMany('App\Models\Order');
    }

    protected $hidden = [
        'pivot'
    ];

    public function getIsOfferAttribute()
    {
        if ($this->price_in_offer != null && $this->price_in_offer < $this->price) {
            return true;
        } else {
            return false;
        }
    }

}
