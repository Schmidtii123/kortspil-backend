<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Find dubletter (samme lobby_id + alias), behold MIN(id), slet resten
        $dups = DB::select("
            SELECT lobby_id, alias, MIN(id) keep_id, COUNT(*) c
            FROM players
            GROUP BY lobby_id, alias
            HAVING c > 1
        ");

        foreach ($dups as $d) {
            $ids = DB::table('players')
                ->where('lobby_id', $d->lobby_id)
                ->where('alias', $d->alias)
                ->where('id', '!=', $d->keep_id)
                ->pluck('id')
                ->all();

            if (!empty($ids)) {
                DB::table('players')->whereIn('id', $ids)->delete();
            }
        }

        // 2) Tilføj unik constraint
        Schema::table('players', function (Blueprint $table) {
            $table->unique(['lobby_id', 'alias'], 'players_lobby_alias_unique');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropUnique('players_lobby_alias_unique');
        });
    }
};
