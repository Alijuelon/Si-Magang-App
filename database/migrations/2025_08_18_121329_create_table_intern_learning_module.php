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
        Schema::create('intern_learning_module', function (Blueprint $table) {
            $table->uuid('intern_id');
            $table->foreign('intern_id')->references('id')->on('interns')->onDelete('cascade');

            $table->uuid('learning_module_id');
            $table->foreign('learning_module_id')->references('id')->on('learning_modules')->onDelete('cascade');

            $table->primary(['intern_id', 'learning_module_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_learning_module');
    }
};
