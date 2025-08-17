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
       Schema::create('activity_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('report_type', 100);
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->date('report_date');
            $table->foreignUuid('intern_id')->constrained('interns')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_reports');
    }
};
