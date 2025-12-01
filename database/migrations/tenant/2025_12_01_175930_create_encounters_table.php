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
        Schema::create('encounters', function (Blueprint $table) {
            $table->id();
            $table->string('encounter_code')->unique(); // Mã cuộc khám (auto-generated)
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade'); // Bác sĩ khám
            $table->date('encounter_date'); // Ngày khám
            $table->time('encounter_time'); // Giờ khám
            $table->enum('type', ['outpatient', 'inpatient', 'emergency', 'followup'])->default('outpatient'); // Loại khám
            $table->enum('status', ['scheduled', 'in-progress', 'completed', 'cancelled'])->default('scheduled');
            $table->text('chief_complaint')->nullable(); // Lý do khám
            $table->text('history')->nullable(); // Tiền sử bệnh
            $table->text('examination')->nullable(); // Khám lâm sàng
            $table->text('diagnosis')->nullable(); // Chẩn đoán
            $table->text('treatment_plan')->nullable(); // Kế hoạch điều trị
            $table->text('notes')->nullable(); // Ghi chú
            // Vital signs
            $table->decimal('temperature', 4, 1)->nullable(); // Nhiệt độ (°C)
            $table->string('blood_pressure')->nullable(); // Huyết áp (mmHg)
            $table->integer('heart_rate')->nullable(); // Nhịp tim (bpm)
            $table->integer('respiratory_rate')->nullable(); // Nhịp thở (/phút)
            $table->decimal('weight', 5, 2)->nullable(); // Cân nặng (kg)
            $table->decimal('height', 5, 2)->nullable(); // Chiều cao (cm)
            $table->decimal('bmi', 4, 2)->nullable(); // BMI
            $table->integer('spo2')->nullable(); // SpO2 (%)
            $table->timestamp('started_at')->nullable(); // Thời gian bắt đầu khám
            $table->timestamp('completed_at')->nullable(); // Thời gian hoàn thành
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encounters');
    }
};
