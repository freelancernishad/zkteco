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
        Schema::create('zk_users', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->unique(); // Internal Device ID
            $table->string('userid')->unique(); // Badge Number (used in logs)
            $table->string('name')->nullable();
            $table->integer('role')->default(0);
            $table->string('cardno')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zk_users');
    }
};
