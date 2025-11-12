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
        Schema::table('posts', function (Blueprint $table) {
            $table->integer('rawg_game_id')->nullable()->after('game_id');
            $table->string('game_title')->nullable()->after('rawg_game_id');
            $table->string('game_image')->nullable()->after('game_title');
            $table->string('game_platform')->nullable()->after('game_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['rawg_game_id', 'game_title', 'game_image', 'game_platform']);
        });
    }
};
