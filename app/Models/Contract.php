<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contract extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tenant_id',
        'unit_id',
        'landlord_national_id',
        'tenant_national_id',
        'start_date',
        'end_date',
        'contract_period',
        'annual_rent',
        'payment_frequency',
        'payment_amount',
        'education_tax',
        'education_tax_amount',
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

    protected $appends = [
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
        'property_boundaries'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Accessors للوصول إلى معلومات العقار والوحدة
    public function getPropertyTypeAttribute()
    {
        return $this->unit->property->type1 . ' - ' . $this->unit->property->type2;
    }

    public function getPropertyLocationAttribute()
    {
        return $this->unit->property->address->getFullAddressAttribute();
    }

    public function getFloorNumberAttribute()
    {
        return $this->unit->floor_number;
    }

    public function getApartmentNumberAttribute()
    {
        return $this->unit->apartment_number;
    }

    public function getLandPieceNumberAttribute()
    {
        return $this->unit->property->address->plot_number;
    }

    public function getBasinNumberAttribute()
    {
        return $this->unit->property->address->basin_number;
    }

    public function getAreaNameAttribute()
    {
        return $this->unit->property->address->district;
    }

    public function getStreetNameAttribute()
    {
        return $this->unit->property->address->street_name;
    }

    public function getBuildingNumberAttribute()
    {
        return $this->unit->property->address->building_number;
    }

    public function getBuildingNameAttribute()
    {
        return $this->unit->property->name;
    }

    public function getUsageTypeAttribute()
    {
        return $this->unit->property->type2; // residential, commercial, industrial
    }

    public function getPropertyBoundariesAttribute()
    {
        $address = $this->unit->property->address;
        return [
            'north' => $address->north_boundary ?? null,
            'south' => $address->south_boundary ?? null,
            'east' => $address->east_boundary ?? null,
            'west' => $address->west_boundary ?? null
        ];
    }
}