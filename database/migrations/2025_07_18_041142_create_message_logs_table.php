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
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->string('device')->nullable();
            $table->string('target');
            $table->text('message');
            $table->string('state_id')->nullable();
            $table->string('status');
            $table->string('state')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_logs');
    }
};
