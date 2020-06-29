<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{

    protected $table = 'contacts';
    public $timestamps = true;
    protected $fillable = array('name', 'email', 'phone', 'message', 'subject', 'contactable_id', 'contactable_type');

    public function client()
    {
        return $this->morphTo();
    }

    public function resturant()
    {
        return $this->morphTo();
    }

}
