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
        Schema::create('tenant_themes', function (Blueprint $table) {
            $table->string('id')->primary(); // tenant_id
            $table->string('clinic_name');
            $table->string('logo_path')->nullable();
            $table->string('primary_color')->default('#3b82f6');
            $table->string('secondary_color')->default('#10b981');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_themes');
    }
};
