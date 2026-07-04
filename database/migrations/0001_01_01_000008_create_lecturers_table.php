<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lecturers', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('staff_id', 30)->unique();
            $table->foreignId('dept_id')->constrained('departments');
            $table->boolean('is_pl')->default(false);
            $table->timestamps();

            $table->primary('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecturers');
    }
};
