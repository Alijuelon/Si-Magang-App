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
            Schema::create('submission__attempts', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('file_path');
                $table->timestamp('uploaded_at')->useCurrent();
                $table->foreignUuid('submission_id')->constrained('submissions')->onDelete('cascade');
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('sumission__attempts');
        }
    };
