<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countdowns', function (Blueprint $table): void {
            $table->id();
            $table->string('share_token', 48)->unique();
            $table->string('manage_token', 48)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->dateTime('target_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countdowns');
    }
};
