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
            // إضافة حقل رقم العقد بحيث يكون فريد ويبدأ من 1 إلى ما لانهاية
            $table->string('contract_number')->unique()->default(DB::raw('(SELECT COALESCE(MAX(contract_number) + 1, 1) FROM contracts)'));
            // اسم المؤجر يضاف يدوي حاليا 
            $table->string('landlord_name');
            // ربط مع جدول المستاجرين بحيث كل مستاجر له عديد من العقود وكل عقد له مستاجر واحد ونفس الكلام عن الوحدة
            $table->foreignId('tenant_id')->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->constrained()->nullOnDelete();// بالنسبة للعنوان رح يتم احضاره من جدول الوحدات ثم جدول العقار 
            //
            $table->date('start_date');
            $table->date('end_date'); // هاي في العقد بنحطها بدل مدة الإيجار (حتى 5-5-2025)
            $table->decimal('rent_amount', 10, 2); // هاي بدل الايجار 

            $table->enum('payment_frequency', ['daily', 'weekly', 'monthly', 'yearly']); // تحديد خيارات الدفع
            $table->text('terms_and_conditions_extra')->nullable(); // هاي بدل الشروط والأحكام الإضافية

            $table->enum('status', ['active', 'inactive'])->default('active'); // هاي بدل حالة العقد (نشط أو غير نشط)
            // هاي بدل تاريخ الإنشاء وتاريخ التعديل

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