<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohorts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_id')->constrained('programmes');
            $table->unsignedTinyInteger('current_year');
            $table->unsignedTinyInteger('semester');
            $table->unsignedSmallInteger('tutorial_group');
            $table->string('academic_year', 20);
            $table->string('intake', 20);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE cohorts ADD CONSTRAINT cohorts_current_year_check CHECK (current_year BETWEEN 1 AND 3)');
        DB::statement('ALTER TABLE cohorts ADD CONSTRAINT cohorts_semester_check CHECK (semester BETWEEN 1 AND 3)');
    }

    public function down(): void
    {
        Schema::dropIfExists('cohorts');
    }
};
