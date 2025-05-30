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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            
            $table->bigInteger('invoice_number')->unique();
            $table->date('issue_date'); // تاريخ الإصدار
           
            // تاريخ الاستحقاق يؤخذ من العقد عند إنشاء الفاتورة
            $table->date('due_date');

            $table->enum('status', ['paid', 'pending', 'unpaid', 'canceled', 'on_hold'])->default('pending');
            
            // خيار إذا كانت الفاتورة مولدة تلقائياً أو يدوياً
            $table->enum('generation_type', ['تلقائي', 'يدوي'])->default('تلقائي');

            $table->text('notes')->nullable();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
