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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('prescription_code')->unique(); // Mã tơ thuốc (auto-generated)
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('encounter_id')->nullable()->constrained()->onDelete('set null'); // Liên kết với cuộc khám
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade'); // Bác sĩ kê đơn
            $table->date('prescription_date'); // Ngày kê đơn
            $table->text('diagnosis')->nullable(); // Chẩn đoán
            $table->text('notes')->nullable(); // Ghi chú, hướng dẫn sử dụng
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
