<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('alias', 50);
            $table->foreignId('lobby_id')->constrained('lobbies')->onDelete('cascade');
            $table->boolean('is_dm')->default(false);
            $table->timestamp('joined_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
