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
        Schema::table('encounters', function (Blueprint $table) {
            $table->foreignId('icd10_code_id')->nullable()->after('diagnosis')->constrained('icd10_codes')->onDelete('set null');
            $table->text('icd10_secondary')->nullable()->after('icd10_code_id')->comment('JSON array of secondary ICD-10 code IDs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encounters', function (Blueprint $table) {
            $table->dropForeign(['icd10_code_id']);
            $table->dropColumn(['icd10_code_id', 'icd10_secondary']);
        });
    }
};
