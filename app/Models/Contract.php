<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'tenant_id',
        'property_id',
        'landlord_national_id',
        'tenant_national_id',
        'property_type',
        'property_location',
        'floor_number',
        'apartment_number',
        'land_piece_number',
        'basin_number',
        'area_name',
        'street_name',
        'building_number',
        'building_name',
        'usage_type',
        'property_boundaries',
        'start_date',
        'end_date',
        'contract_period',
        'annual_rent',
        'payment_frequency',
        'payment_amount',
        'education_tax',
        'education_tax_amount',
        'property_fixtures',
        'additional_terms',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'annual_rent' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'education_tax_amount' => 'decimal:2'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}