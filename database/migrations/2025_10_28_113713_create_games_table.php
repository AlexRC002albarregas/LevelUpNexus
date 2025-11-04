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
		Schema::create('games', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->onDelete('cascade');
			$table->string('title');
			$table->enum('platform', ['pc', 'xbox', 'playstation', 'switch', 'mobile', 'other'])->default('pc');
			$table->string('genre')->nullable();
			$table->unsignedInteger('hours_played')->default(0);
			$table->boolean('is_favorite')->default(false);
			$table->timestamps();
			$table->index(['user_id', 'title']);
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
