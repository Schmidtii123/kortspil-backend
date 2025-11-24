<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $image_url
 * @property string $description
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GameState> $gameStates
 * @property-read int|null $game_states_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Card newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Card newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Card query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Card whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Card whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Card whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Card whereName($value)
 */
	class Card extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $lobby_id
 * @property int|null $current_card_id
 * @property int $round_number
 * @property string|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Card|null $currentCard
 * @property-read \App\Models\Lobby $lobby
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameState newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameState newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameState query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameState whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameState whereCurrentCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameState whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameState whereLobbyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameState whereRoundNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameState whereUpdatedAt($value)
 */
	class GameState extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $lobby_code
 * @property int|null $dm_id
 * @property int $is_active
 * @property int $game_started
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Player> $players
 * @property-read int|null $players_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby whereDmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby whereGameStarted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby whereLobbyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby whereUpdatedAt($value)
 */
	class Lobby extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $alias
 * @property int $lobby_id
 * @property int $is_dm
 * @property string $joined_at
 * @property-read \App\Models\Lobby $lobby
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereIsDm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereJoinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereLobbyId($value)
 */
	class Player extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

