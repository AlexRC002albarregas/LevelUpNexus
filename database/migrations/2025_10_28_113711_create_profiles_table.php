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
		Schema::create('profiles', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->onDelete('cascade');
			$table->string('nick')->unique();
			$table->string('avatar')->nullable();
			$table->enum('platform', ['pc', 'xbox', 'playstation', 'switch', 'mobile', 'other'])->default('pc');
			$table->json('favorite_games')->nullable();
			$table->unsignedInteger('hours_played')->default(0);
			$table->json('achievements')->nullable();
			$table->text('bio')->nullable();
			$table->timestamps();
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
