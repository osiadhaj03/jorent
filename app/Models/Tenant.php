<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    //


    public function address()
{
    return $this->morphOne(Address::class, 'addressable');
}
   

    public function getFullAddressAttribute()
    {
        return $this->address->full_address;
    }

    public function getFullAddressWithStreetAttribute()
    {
        return $this->address->full_address_with_street;
    }

}
