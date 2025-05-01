<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->morphs('addressable'); // ينشئ addressable_id و addressable_type
            $table->string('country')->nullable(); 
            $table->string('governorate')->nullable(); // المحافظة
            $table->string('city')->nullable(); 
            $table->string('district')->nullable(); // الحي
            $table->string('building_number')->nullable(); // رقم البناية
            $table->string('plot_number')->nullable(); // رقم القطعة
            $table->string('basin_number')->nullable(); // رقم الحوض
            $table->string('property_number')->nullable(); // رقم المبنى العقاري
            $table->string('street_name')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
