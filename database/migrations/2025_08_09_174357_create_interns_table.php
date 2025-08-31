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
       Schema::create('interns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('full_name');
            $table->string('division');
            $table->string('school_origin');
            $table->string('major');
            $table->string('gender');
            $table->string('phone_number');
            $table->date('birth_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('intern_type', ['School', 'College', 'General']);
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('supervisor_id')->nullable()->constrained('supervisors')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interns');
    }
};
