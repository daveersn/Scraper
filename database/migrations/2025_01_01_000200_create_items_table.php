<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table): void {
            $table->id();
            $table->string('site_domain')->index();
            $table->text('url');
            $table->char('url_hash', 40)->unique();
            $table->string('external_id')->nullable();
            $table->string('title')->nullable();
            $table->integer('current_price')->nullable();
            $table->char('currency', 3)->nullable();
            $table->char('status', 1);
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['site_domain', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
