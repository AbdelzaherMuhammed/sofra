<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{

    protected $table = 'offers';
    public $timestamps = true;
    protected $fillable = array('offer_title', 'offer_description', 'image', 'offer_start_date' ,'offer_expire_date', 'resturant_id');

    public function resturant()
    {
        return $this->belongsTo('App\Models\Resturant');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

}
