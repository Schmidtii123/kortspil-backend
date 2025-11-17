<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lobby;
use App\Events\CardDrawn;
use App\Events\CardFlipped;
use App\Events\PlayerTurnChanged;

class GameController extends Controller
{
    public function drawCard(Request $request)
    {
        $validated = $request->validate([
            'lobby_code' => 'required|string',
            'card_number' => 'required|integer|min:1|max:10',
        ]);

        $lobby = Lobby::where('lobby_code', $validated['lobby_code'])->firstOrFail();

        // Broadcast til alle i lobbyen
        broadcast(new CardDrawn($lobby->lobby_code, $validated['card_number']))->toOthers();

        return response()->json([
            'success' => true,
            'card_number' => $validated['card_number'],
        ]);
    }

    public function setPlayerTurn(Request $request)
    {
        $validated = $request->validate([
            'lobby_code' => 'required|string',
            'player_name' => 'required|string',
        ]);

        $lobby = Lobby::where('lobby_code', strtoupper($validated['lobby_code']))->firstOrFail();

        // Broadcast player turn changed event
        broadcast(new PlayerTurnChanged($lobby->lobby_code, $validated['player_name']))->toOthers();

        return response()->json([
            'message' => 'Player turn set',
            'player_name' => $validated['player_name']
        ]);
    }

    public function flipCard(Request $request)
    {
        $validated = $request->validate([
            'lobby_code' => 'required|string',
            'flipped' => 'required|boolean',
        ]);

        $lobby = Lobby::where('lobby_code', strtoupper($validated['lobby_code']))->firstOrFail();

        // Broadcast flip state til alle
        broadcast(new CardFlipped($lobby->lobby_code, $validated['flipped']))->toOthers();

        return response()->json(['success' => true]);
    }
}
