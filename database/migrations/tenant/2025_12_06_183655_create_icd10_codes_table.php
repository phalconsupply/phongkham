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
        Schema::create('icd10_codes', function (Blueprint $table) {
            $table->id();
            
            // Mã ICD-10
            $table->string('code', 10)->unique()->index();
            $table->string('code_normalized', 10)->nullable()->index(); // Mã không dấu
            
            // Tên bệnh
            $table->text('name_vi');
            $table->text('name_en')->nullable();
            
            // Phân cấp chương
            $table->string('chapter_code', 20)->nullable()->index();
            $table->string('chapter_name_vi')->nullable();
            $table->string('chapter_name_en')->nullable();
            
            // Nhóm chính
            $table->string('main_group_code', 20)->nullable()->index();
            $table->string('main_group_name_vi')->nullable();
            $table->string('main_group_name_en')->nullable();
            
            // Nhóm phụ 1
            $table->string('sub_group1_code', 20)->nullable();
            $table->string('sub_group1_name_vi')->nullable();
            $table->string('sub_group1_name_en')->nullable();
            
            // Nhóm phụ 2
            $table->string('sub_group2_code', 20)->nullable();
            $table->string('sub_group2_name_vi')->nullable();
            $table->string('sub_group2_name_en')->nullable();
            
            // Loại bệnh
            $table->string('type_code', 20)->nullable();
            $table->string('type_name_vi')->nullable();
            $table->string('type_name_en')->nullable();
            
            // Ràng buộc tự động
            $table->enum('gender', ['male', 'female', 'both'])->default('both');
            $table->integer('min_age')->nullable(); // Tuổi tối thiểu (tháng)
            $table->integer('max_age')->nullable(); // Tuổi tối đa (tháng)
            
            // Phân loại
            $table->boolean('can_be_primary')->default(true); // Có thể là chẩn đoán chính
            $table->boolean('is_chronic')->default(false); // Bệnh mạn tính
            $table->boolean('reportable')->default(false); // Bệnh truyền nhiễm báo cáo
            
            // Metadata
            $table->text('notes')->nullable(); // Ghi chú
            $table->string('severity', 20)->nullable(); // mild, moderate, severe
            
            $table->timestamps();
            
            // Indexes
            $table->fullText(['name_vi', 'name_en']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('icd10_codes');
    }
};
