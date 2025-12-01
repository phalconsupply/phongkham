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
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->onDelete('cascade');
            $table->string('medication_name'); // Tên thuốc
            $table->string('dosage')->nullable(); // Liều lượng (vd: 500mg)
            $table->string('frequency')->nullable(); // Tần suất (vd: 2 lần/ngày)
            $table->string('duration')->nullable(); // Thời gian (vd: 7 ngày)
            $table->string('route')->nullable(); // Đường dùng (vd: Uống, Tiêm, Bôi)
            $table->integer('quantity')->nullable(); // Số lượng
            $table->string('unit')->nullable(); // Đơn vị (viên, chai, ống...)
            $table->text('instructions')->nullable(); // Hướng dẫn sử dụng
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
