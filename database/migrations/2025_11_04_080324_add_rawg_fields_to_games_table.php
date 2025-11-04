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
        Schema::table('games', function (Blueprint $table) {
            $table->unsignedBigInteger('rawg_id')->nullable()->after('user_id');
            $table->string('rawg_image')->nullable()->after('rawg_id');
            $table->decimal('rawg_rating', 3, 2)->nullable()->after('rawg_image');
            $table->date('released_date')->nullable()->after('rawg_rating');
            $table->string('rawg_slug')->nullable()->after('released_date');
            $table->index('rawg_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropIndex(['rawg_id']);
            $table->dropColumn(['rawg_id', 'rawg_image', 'rawg_rating', 'released_date', 'rawg_slug']);
        });
    }
};
