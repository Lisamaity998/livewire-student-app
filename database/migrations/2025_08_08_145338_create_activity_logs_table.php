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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_role'); // e.g., 'student', 'teacher', 'admin'
            $table->unsignedBigInteger('user_id');
            $table->string('activity_type'); // e.g., 'login', 'logout', 'class_created', etc.
            $table->text('description'); // Additional details about the activity
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
