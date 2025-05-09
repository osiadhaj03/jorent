<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Tenant extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'id_number',
        'id_type',
        'occupation',
        'employer',
        'employer_phone',
        'emergency_contact_name',
        'emergency_contact_phone',
        'status',
        'notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function customInvoices(): HasMany
    {
        return $this->hasMany(CustomInvoice::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullAddressAttribute(): string
    {
        return $this->address?->full_address ?? 'No address';
    }

    public function getFullAddressWithStreetAttribute(): string
    {
        return $this->address?->full_address_with_street ?? 'No address';
    }

    public function getActiveContractAttribute()
    {
        return $this->contracts()->where('status', 'active')->first();
    }

    public function getTotalPaymentsAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getTotalInvoicesAttribute()
    {
        return $this->invoices()->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return $this->total_invoices - $this->total_payments;
    }
}
