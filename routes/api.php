<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LobbyController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\GameController;

Route::prefix('lobbies')->group(function () {
    Route::post('/create-lobby', [LobbyController::class, 'createLobby']);
    Route::post('/join-lobby', [LobbyController::class, 'joinLobby']);
    Route::post('/leave-lobby', [LobbyController::class, 'leaveLobby']);
    Route::post('/start-game', [LobbyController::class, 'startGame']);
    Route::post('/become-dm', [LobbyController::class, 'becomeDM']);
    Route::post('/become-player', [LobbyController::class, 'becomePlayer']);
    Route::get('/lobby/{code}', [LobbyController::class, 'getLobby']);
});

Route::prefix('players')->group(function () {
    Route::post('/join', [PlayerController::class, 'join']);
    Route::get('/lobby/{lobby_code}', [PlayerController::class, 'getByLobby']);
});

Route::prefix('game')->group(function () {
    Route::post('/draw-card', [GameController::class, 'drawCard']);
    Route::post('/flip-card', [GameController::class, 'flipCard']);
    Route::post('/set-player-turn', [GameController::class, 'setPlayerTurn']);
});

Route::prefix('cards')->group(function () {
    Route::get('/', [GameController::class, 'getAllCards']);
});

Route::prefix('chat')->group(function () {
    Route::post('/send', [\App\Http\Controllers\ChatController::class, 'send']);
    Route::get('/history/{lobby_code}', [\App\Http\Controllers\ChatController::class, 'history']);
});
