# Crit Cards – Backend (Laravel 11 API)

En Laravel REST API med WebSocket broadcasting via Pusher til at håndtere D&D critical hit kort i realtid.

---

## Live Demo

**Backend API**: [https://kortspil-backend-production.up.railway.app](https://kortspil-backend-production.up.railway.app)

---

## Tech Stack

- **Laravel 11** (REST API + broadcasting)
- **MySQL** (Railway-hosted database)
- **Pusher** (WebSocket server til realtids-events)
- **PHP 8.3**
- **Railway** (deployment + MySQL hosting)

---

## Kør Projektet Lokalt

### 1. Clone repository
```bash
git clone https://github.com/Schmidtii123/kortspil-backend
cd kortspil-backend
```

### 2. Installer dependencies
```bash
composer install
npm install  # For Vite/Laravel assets
```

### 3. Konfigurer miljøvariabler

Kopier `.env.example` til `.env`:
```bash
cp .env.example .env
```

Opdater følgende i `.env`:

**Database (MySQL):**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crit_cards
DB_USERNAME=root
DB_PASSWORD=your_password
```

**Pusher (Hent keys fra [pusher.com](https://pusher.com)):**
```env
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=eu
```

**CORS (Frontend URL):**
```env
FRONTEND_URL=http://localhost:5173
```

### 4. Generer application key
```bash
php artisan key:generate
```

### 5. Kør migrations + seed database
```bash
php artisan migrate:fresh --seed
```

Det opretter:
- `lobbies` (lobby-data + game_started flag)
- `players` (spillere med alias, is_dm, avatar_url)
- `chat_messages` (chat-historik per lobby)
- `cards` (10 kort med beskrivelser)

### 6. Start Laravel development server
```bash
php artisan serve
```

Backend kører nu på **http://127.0.0.1:8000**

---

## Projekt-struktur

```
app/
├── Events/             # CardDrawn, CardFlipped, GameStarted, etc.
├── Http/Controllers/   # LobbyController, GameController, ChatController
└── Models/             # Lobby, Player, Card, ChatMessage

database/
├── migrations/         # Database schema
└── seeders/            # CardSeeder (seeder 10 kort)

routes/
├── api.php             # REST endpoints
└── channels.php        # Pusher channel auth
```

---

## API Endpoints

### Lobby Management
```http
POST   /api/lobbies/create-lobby       # Opret lobby
POST   /api/lobbies/join-lobby         # Join lobby
GET    /api/lobbies/lobby/{code}       # Hent lobby-data
POST   /api/lobbies/leave-lobby        # Forlad lobby
POST   /api/lobbies/become-dm          # Skift til DM-rolle
POST   /api/lobbies/become-player      # Skift til Player-rolle
POST   /api/lobbies/kick-player        # Kick spiller (kun DM)
POST   /api/lobbies/start-game         # Start spil (broadcaster avatars)
```

**Eksempel request (Create Lobby):**
```json
POST /api/lobbies/create-lobby
{
  "alias": "Emil"
}
```

**Eksempel response:**
```json
{
  "lobby": {
    "id": 1,
    "lobby_code": "ABC123",
    "dm_id": 1,
    "is_active": true,
    "game_started": false
  },
  "player": {
    "id": 1,
    "alias": "Emil",
    "is_dm": true,
    "lobby_id": 1
  }
}
```

---

### Game Actions
```http
POST   /api/game/draw-card        # Træk kort
POST   /api/game/flip-card        # Vend kort
POST   /api/game/set-player-turn  # Sæt spiller-tur (DM only)
POST   /api/game/clear-player-turn # Clear tur (DM only)
```

**Eksempel request (Draw Card):**
```json
POST /api/game/draw-card
{
  "lobby_code": "ABC123",
  "card_number": 5
}
```

**Backend broadcaster:**
```php
broadcast(new CardDrawn($lobby->lobby_code, $validated['card_number']))->toOthers();
```

---

### Chat
```http
POST   /api/chat/send             # Send besked
GET    /api/chat/history/{code}   # Hent chat-historik
```

**Eksempel request (Send Message):**
```json
POST /api/chat/send
{
  "lobby_code": "ABC123",
  "sender": "Emil",
  "message": "Ready to start!"
}
```

---

### Cards
```http
GET    /api/cards                 # Hent alle kort (fallback hvis DB tom)
```

**Eksempel response:**
```json
[
  {
    "id": 1,
    "name": "Card 1",
    "image_url": "/assets/cards/1.webp",
    "description": "Lost hand: you can no longer wield two-handed weapons or shields."
  },
  ...
]
```

---

## WebSocket Broadcasting (Pusher)

Backend broadcaster følgende events via Pusher:

### Event-eksempler

**1. GameStarted** (broadcaster avatars):
```php
class GameStarted implements ShouldBroadcast {
    public function broadcastOn() {
        return new Channel('lobby.' . $this->lobbyCode);
    }
    
    public function broadcastWith() {
        $lobby = Lobby::where('lobby_code', $this->lobbyCode)
            ->with('players:id,lobby_id,alias,avatar_url')
            ->first();
        
        $avatars = [];
        foreach ($lobby->players as $p) {
            $avatars[$p->alias] = $p->avatar_url;
        }
        
        return ['lobby_code' => $this->lobbyCode, 'avatars' => $avatars];
    }
}
```

**2. CardDrawn:**
```php
broadcast(new CardDrawn($lobby->lobby_code, $validated['card_number']))->toOthers();
```

**3. ChatMessageSent:**
```php
broadcast(new ChatMessageSent($lobby->lobby_code, $chat->sender, $chat->message, $chat->created_at->toISOString()))->toOthers();
```

---

## Database Schema

### `lobbies`
```sql
CREATE TABLE lobbies (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  lobby_code VARCHAR(8) UNIQUE NOT NULL,
  dm_id BIGINT NULL,
  is_active BOOLEAN DEFAULT TRUE,
  game_started BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (dm_id) REFERENCES players(id) ON DELETE SET NULL
);
```

### `players`
```sql
CREATE TABLE players (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  alias VARCHAR(50) NOT NULL,
  lobby_id BIGINT NOT NULL,
  is_dm BOOLEAN DEFAULT FALSE,
  avatar_url VARCHAR(255) NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  UNIQUE KEY players_lobby_alias_unique (lobby_id, alias),
  FOREIGN KEY (lobby_id) REFERENCES lobbies(id) ON DELETE CASCADE
);
```

** Avatar-logic:** Avatars tildeles ved `startGame()` i [`LobbyController`](app/Http/Controllers/LobbyController.php):

```php
foreach ($players as $player) {
    if (!$player->avatar_url) {
        $randomNum = rand(1, 8);
        $player->avatar_url = "/assets/avatars/{$randomNum}.jpg";
        $player->save();
    }
}
```

### `chat_messages`
```sql
CREATE TABLE chat_messages (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  lobby_id BIGINT NOT NULL,
  sender VARCHAR(50) NOT NULL,
  message VARCHAR(500) NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (lobby_id) REFERENCES lobbies(id) ON DELETE CASCADE
);
```

---

## Deployment (Railway)

### 1. Link GitHub repository i Railway dashboard

### 2. Railway auto-detecter Laravel

Railway kører automatisk:
```bash
composer install
php artisan migrate --force
```


### 3. Tilføj environment variables

Railway injecter automatisk database credentials. Tilføj manuelt:

```env
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=eu
FRONTEND_URL=https://chipper-moonbeam-100da6.netlify.app
```

### 4. Deploy
Railway rebuilder automatisk ved push til `main`.

---

## 📚 Relateret Dokumentation

- **Frontend app:** [crit-cards-app/README.md](../crit-cards-app/README.md)
- **API routes:** [`routes/api.php`](routes/api.php)
- **Database migrations:** [`database/migrations/`](database/migrations/)
- **Pusher docs:** [https://pusher.com/docs](https://pusher.com/docs)

---

## Forfatter

**Emil Schmidt**  
Webudvikling  
December/Januar 2025/2026