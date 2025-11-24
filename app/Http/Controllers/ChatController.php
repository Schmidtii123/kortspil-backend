<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lobby;
use App\Models\ChatMessage;
use App\Events\ChatMessageSent;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'lobby_code' => 'required|string',
            'sender'     => 'required|string|max:50',
            'message'    => 'required|string|max:500',
        ]);

        $lobby = Lobby::where('lobby_code', strtoupper($data['lobby_code']))->firstOrFail();

        $chat = ChatMessage::create([
            'lobby_id' => $lobby->id,
            'sender'   => $data['sender'],
            'message'  => $data['message'],
        ]);

        broadcast(new ChatMessageSent(
            $lobby->lobby_code,
            $chat->sender,
            $chat->message,
            $chat->created_at->toISOString()
        ))->toOthers();

        return response()->json([
            'sender'     => $chat->sender,
            'message'    => $chat->message,
            'created_at' => $chat->created_at->toISOString(),
        ]);
    }

    public function history($lobbyCode)
    {
        $lobby = Lobby::where('lobby_code', strtoupper($lobbyCode))->firstOrFail();

        $messages = ChatMessage::where('lobby_id', $lobby->id)
            ->orderBy('id', 'asc')
            ->limit(200)
            ->get();

        return response()->json(
            $messages->map(fn($m) => [
                'sender'     => $m->sender,
                'message'    => $m->message,
                'created_at' => $m->created_at->toISOString(),
            ])
        );
    }
}