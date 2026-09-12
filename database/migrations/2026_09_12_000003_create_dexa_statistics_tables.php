<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dexa_counters', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedBigInteger('total')->default(0);
            $table->timestamps();
        });

        Schema::create('dexa_viewers', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dexa_viewers');
        Schema::dropIfExists('dexa_counters');
    }
};
