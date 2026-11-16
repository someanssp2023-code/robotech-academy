<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('title');
            $table->string('message');
            $table->string('icon')->default('🔔');
            $table->string('url')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('for_admin')->default(true);
            $table->boolean('for_user')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};