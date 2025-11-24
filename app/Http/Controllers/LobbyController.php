<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lobby;
use App\Models\Player;
use Illuminate\Support\Str;
use App\Events\GameStarted;

class LobbyController extends Controller
{
    public function createLobby(Request $request)
    {
        $validated = $request->validate([
            'alias' => 'required|string|max:50',
        ]);

        // Generate unique 6-char code (A-Z, 0-9)
        do {
            $code = strtoupper(Str::random(6));
        } while (Lobby::where('lobby_code', $code)->exists());

        $lobby = Lobby::create([
            'lobby_code' => $code,
            'is_active' => true,
        ]);

        $player = Player::create([
            'alias' => $validated['alias'],
            'lobby_id' => $lobby->id,
            'is_dm' => true,
        ]);

        $lobby->update(['dm_id' => $player->id]);

        return response()->json([
            'lobby_code' => $lobby->lobby_code,
            'player' => $player,
        ]);
    }

    public function joinLobby(Request $request)
    {
        $validated = $request->validate([
            'lobby_code' => 'required|string|size:6',
            'alias' => 'required|string|max:50',
        ]);

        $lobby = Lobby::where('lobby_code', strtoupper($validated['lobby_code']))->firstOrFail();

        $player = Player::create([
            'alias' => $validated['alias'],
            'lobby_id' => $lobby->id,
            'is_dm' => false,
        ]);

        event(new \App\Events\PlayerJoined($lobby, $player));

        return response()->json(['player' => $player]);
    }

    public function startGame(Request $request)
    {
        $validated = $request->validate([
            'lobby_code' => 'required|string',
        ]);

        $lobby = Lobby::where('lobby_code', strtoupper($validated['lobby_code']))->firstOrFail();

        $lobby->game_started = true;
        $lobby->save();

        // Broadcast game started event
        broadcast(new GameStarted($lobby->lobby_code))->toOthers();

        return response()->json(['message' => 'Game started', 'lobby' => $lobby]);
    }

    public function getLobby($code)
    {
        $lobby = Lobby::with('players')->where('lobby_code', strtoupper($code))->firstOrFail();

        return response()->json([
            'id' => $lobby->id,
            'lobby_code' => $lobby->lobby_code,
            'is_active' => $lobby->is_active,
            'dm_id' => $lobby->dm_id,
            'players' => $lobby->players,
            'game_started' => $lobby->game_started ?? false,
        ]);
    }

    public function becomeDM(Request $request)
    {
        $validated = $request->validate([
            'lobby_code' => 'required|string',
            'player_id' => 'required|integer',
        ]);

        $lobby = Lobby::where('lobby_code', strtoupper($validated['lobby_code']))->firstOrFail();

        $player = Player::findOrFail($validated['player_id']);

        // Fjern DM fra alle andre spillere i lobbyen
        Player::where('lobby_id', $lobby->id)->update(['is_dm' => false]);

        // Gør den valgte spiller til DM
        $player->is_dm = true;
        $player->save();

        $lobby->dm_id = $player->id;
        $lobby->save();

        broadcast(new \App\Events\DungeonMasterChanged($lobby->lobby_code, $player->alias))->toOthers();

        return response()->json(['dm' => $player]);
    }

    public function becomePlayer(Request $request)
    {
        $validated = $request->validate([
            'lobby_code' => 'required|string',
            'player_id' => 'required|integer',
        ]);

        $lobby = Lobby::where('lobby_code', strtoupper($validated['lobby_code']))->firstOrFail();

        $player = Player::findOrFail($validated['player_id']);

        // Tjek at spilleren er nuværende DM
        if ($player->id !== $lobby->dm_id) {
            return response()->json(['error' => 'You are not the DM'], 403);
        }

        // Fjern DM status
        $player->is_dm = false;
        $player->save();

        $lobby->dm_id = null;
        $lobby->save();

        broadcast(new \App\Events\DungeonMasterChanged($lobby->lobby_code, null))->toOthers();

        return response()->json(['player' => $player]);
    }

    public function leaveLobby(Request $request)
    {
        $validated = $request->validate([
            'lobby_code' => 'required|string',
            'player_id' => 'required|integer',
        ]);

        $lobby = Lobby::where('lobby_code', strtoupper($validated['lobby_code']))->first();

        if (!$lobby) {
            return response()->json(['error' => 'Lobby not found'], 404);
        }

        // Fjern spilleren
        $player = Player::find($validated['player_id']);
        if ($player) {
            $player->delete();
        }

        // Tjek om der er flere spillere tilbage
        $remainingPlayers = Player::where('lobby_id', $lobby->id)->count();

        if ($remainingPlayers === 0) {
            // Ingen spillere tilbage - slet lobbyen
            $lobby->delete();
            return response()->json(['message' => 'Lobby deleted', 'lobby_deleted' => true]);
        }

        // Hvis DM forlader, find en ny DM eller sæt til null
        if ($lobby->dm_id === $validated['player_id']) {
            $newDM = Player::where('lobby_id', $lobby->id)->first();
            if ($newDM) {
                $newDM->is_dm = true;
                $newDM->save();
                $lobby->dm_id = $newDM->id;
                $lobby->save();
                broadcast(new \App\Events\DungeonMasterChanged($lobby->lobby_code, $newDM->alias))->toOthers();
            } else {
                $lobby->dm_id = null;
                $lobby->save();
                broadcast(new \App\Events\DungeonMasterChanged($lobby->lobby_code, null))->toOthers();
            }
        }

        return response()->json([
            'message' => 'Player left lobby',
            'lobby_deleted' => false,
            'game_started' => $lobby->game_started ?? false
        ]);
    }
}
