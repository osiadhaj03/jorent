<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'type1',
        'type2',
        'account_manager_id',
        'birth_date',
        'floors_count',
        'floor_area',
        'total_area',
        'features',
    ];
    protected $casts = [
        'features' => 'array',
        'birth_date' => 'date',
        'floors_count' => 'integer',
        'floor_area' => 'decimal:2',
        'total_area' => 'decimal:2',
    ];



    protected $table = 'properties';
    protected $hidden = [
        'created_at',
        'updated_at',
    ];


    public function setFeaturesAttribute($value)
    {
        $this->attributes['features'] = json_encode($value);
    }

    public function address()
{
    return $this->morphOne(Address::class, 'addressable');
}

    public function accountManager()
    {
        return $this->belongsTo(Acc::class, 'account_manager_id');
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
