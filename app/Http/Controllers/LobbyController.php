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

        $lobby = \App\Models\Lobby::where('lobby_code', strtoupper($validated['lobby_code']))->firstOrFail();

        $player = \App\Models\Player::firstOrCreate(
            ['lobby_id' => $lobby->id, 'alias' => $validated['alias']],
            ['is_dm' => false]
        );

        if ($player->wasRecentlyCreated) {
            event(new \App\Events\PlayerJoined($lobby, $player));
        }

        return response()->json(['lobby' => $lobby, 'player' => $player]);
    }

    public function startGame(Request $request)
    {
        $validated = $request->validate([
            'lobby_code' => 'required|string',
        ]);

        $lobby = Lobby::where('lobby_code', strtoupper($validated['lobby_code']))->firstOrFail();

        // Tildel avatars til alle spillere uden avatar
        $players = Player::where('lobby_id', $lobby->id)->get();
        $avatarCount = 8;
        
        foreach ($players as $player) {
            if (!$player->avatar_url) {
                $randomNum = rand(1, $avatarCount);
                $player->avatar_url = "/assets/avatars/{$randomNum}.jpg";
                $player->save();
            }
        }

        $lobby->game_started = true;
        $lobby->save();

        broadcast(new GameStarted($lobby->lobby_code))->toOthers();

        return response()->json(['lobby' => $lobby]);
    }

    public function getLobby($code)
    {
        $lobby = Lobby::where('lobby_code', strtoupper($code))
            ->with(['players' => function ($query) {
                $query->select('id', 'lobby_id', 'alias', 'is_dm', 'avatar_url'); // ← Tilføj avatar_url
            }])
            ->firstOrFail();

        return response()->json($lobby);
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

    public function kickPlayer(Request $request)
    {
        $validated = $request->validate([
            'lobby_code' => 'required|string',
            'player_alias' => 'required|string',
            'dm_id' => 'required|integer',
        ]);

        $lobby = Lobby::where('lobby_code', strtoupper($validated['lobby_code']))->firstOrFail();

        // Verificer at requester er DM
        if ($lobby->dm_id !== $validated['dm_id']) {
            return response()->json(['error' => 'Only DM can kick players'], 403);
        }

        $player = Player::where('lobby_id', $lobby->id)
            ->where('alias', $validated['player_alias'])
            ->first();

        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        // Kan ikke kicke sig selv
        if ($player->id === $lobby->dm_id) {
            return response()->json(['error' => 'Cannot kick yourself'], 400);
        }

        $player->delete();

        broadcast(new \App\Events\PlayerKicked($lobby->lobby_code, $player->alias))->toOthers();

        return response()->json(['message' => 'Player kicked', 'alias' => $player->alias]);
    }
}
