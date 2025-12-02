<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Card;

class CardSeeder extends Seeder
{
    public function run(): void
    {
        $cards = [
            ['id'=>1, 'name'=>'Card 1', 'image_url'=>'/assets/cards/1.webp', 'description'=>'A sudden surge of energy grants advantage on your next roll.'],
            ['id'=>2, 'name'=>'Card 2', 'image_url'=>'/assets/cards/2.webp', 'description'=>'The air chills; reduce incoming damage by 2 this round.'],
            ['id'=>3, 'name'=>'Card 3', 'image_url'=>'/assets/cards/3.webp', 'description'=>'You spot a hidden clue—reveal an extra narrative detail.'],
            ['id'=>4, 'name'=>'Card 4', 'image_url'=>'/assets/cards/4.webp', 'description'=>'Minor inspiration: reroll any failed check once.'],
            ['id'=>5, 'name'=>'Card 5', 'image_url'=>'/assets/cards/5.webp', 'description'=>'A distracting echo forces another player to skip a turn.'],
            ['id'=>6, 'name'=>'Card 6', 'image_url'=>'/assets/cards/6.webp', 'description'=>'Restore a small portion of lost HP (DM decides exact amount).'],
            ['id'=>7, 'name'=>'Card 7', 'image_url'=>'/assets/cards/7.webp', 'description'=>'Trap sense: negate the next environmental hazard.'],
            ['id'=>8, 'name'=>'Card 8', 'image_url'=>'/assets/cards/8.webp', 'description'=>'Gain temporary armor until end of round.'],
            ['id'=>9, 'name'=>'Card 9', 'image_url'=>'/assets/cards/9.webp', 'description'=>'Strategic insight: swap initiative with any player.'],
            ['id'=>10,'name'=>'Card 10','image_url'=>'/assets/cards/10.webp','description'=>'Critical omen: next attack has +2 to hit.'],
        ];

        foreach ($cards as $c) {
            Card::updateOrCreate(['id' => $c['id']], $c);
        }
    }
}