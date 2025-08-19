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
        Schema::create('submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('status',['submitted','resubmitted'])->default('submitted');
            $table->date('submission_date')->nullable();
            $table->foreignUuid('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignUuid('intern_id')->constrained('interns')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sumissions');
    }
};
