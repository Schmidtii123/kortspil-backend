<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('game_state', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lobby_id');
            $table->unsignedBigInteger('current_card_id')->nullable();
            $table->integer('round_number')->default(1);
            $table->timestamps();

            $table->foreign('lobby_id')
                ->references('id')
                ->on('lobbies')
                ->onDelete('cascade');

            $table->foreign('current_card_id')
                ->references('id')
                ->on('cards')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('game_state');
    }
};
