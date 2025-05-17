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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->string('landlord_name');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('due_date')->nullable();
            $table->decimal('rent_amount', 10, 2);
            
            $table->string('status')->default('active');
            $table->text('terms_and_conditions_extra')->nullable();
            $table->json('tenant_signature')->nullable();
            $table->json('first_witness_signature')->nullable();
            $table->json('second_witness_signature')->nullable();
            $table->json('landlord_signature')->nullable();
            $table->date('hired_date')->nullable();
            $table->string('hired_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};