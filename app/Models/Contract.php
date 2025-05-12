<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contract extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tenant_id',
        'property_id',
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

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}