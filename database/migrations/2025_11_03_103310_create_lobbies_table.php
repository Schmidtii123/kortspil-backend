<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('lobbies', function (Blueprint $table) {
            $table->id();
            $table->string('lobby_code', 8)->unique();
            $table->unsignedBigInteger('dm_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
