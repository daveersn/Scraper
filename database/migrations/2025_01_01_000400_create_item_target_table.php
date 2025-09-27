<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_target', function (Blueprint $table): void {
            $table->foreignId('target_id')->constrained();
            $table->foreignId('item_id')->constrained();
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->primary(['target_id', 'item_id']);
            $table->index(['item_id', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_item');
    }
};
