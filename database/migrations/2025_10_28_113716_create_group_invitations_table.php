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
		Schema::create('group_invitations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
			$table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
			$table->foreignId('recipient_id')->constrained('users')->onDelete('cascade');
			$table->enum('status', ['pending', 'accepted', 'declined', 'cancelled'])->default('pending');
			$table->timestamps();
			$table->unique(['group_id', 'recipient_id', 'status']);
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_invitations');
    }
};
