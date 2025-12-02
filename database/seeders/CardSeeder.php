<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Card;

class CardSeeder extends Seeder
{
    public function run(): void
    {
        $cards = [
            ['id'=>1, 'name'=>'Card 1', 'image_url'=>'/assets/cards/1.webp', 'description'=>'Lost hand: you can no longer wield two-handed weapons or shields.'],
            ['id'=>2, 'name'=>'Card 2', 'image_url'=>'/assets/cards/2.webp', 'description'=>'Spear through the gut: take 3 damage immediately and fall prone.'],
            ['id'=>3, 'name'=>'Card 3', 'image_url'=>'/assets/cards/3.webp', 'description'=>'Broken nose: vision blurred, -2 to attack rolls for 2 rounds.'],
            ['id'=>4, 'name'=>'Card 4', 'image_url'=>'/assets/cards/4.webp', 'description'=>'Hammer to the groin: stunned for 1 turn, all rolls at disadvantage.'],
            ['id'=>5, 'name'=>'Card 5', 'image_url'=>'/assets/cards/5.webp', 'description'=>'Severed ear: permanent -3 to Perception checks involving hearing.'],
            ['id'=>6, 'name'=>'Card 6', 'image_url'=>'/assets/cards/6.webp', 'description'=>'Broken arm: you drop whatever you\'re holding and cannot use that arm until healed.'],
            ['id'=>7, 'name'=>'Card 7', 'image_url'=>'/assets/cards/7.webp', 'description'=>'Knocked-out teeth: speech impaired, disadvantage on Charisma checks.'],
            ['id'=>8, 'name'=>'Card 8', 'image_url'=>'/assets/cards/8.webp', 'description'=>'Broken ankle: movement speed reduced to 5 ft. until splinted.'],
            ['id'=>9, 'name'=>'Card 9', 'image_url'=>'/assets/cards/9.webp', 'description'=>'Lost eye: permanent -5 to ranged attack rolls and Percetion checks.'],
            ['id'=>10,'name'=>'Card 10','image_url'=>'/assets/cards/10.webp','description'=>'Dislocated shoulder: cannot use that arm for attacks or actions this combat.'],
        ];

        foreach ($cards as $c) {
            Card::updateOrCreate(['id' => $c['id']], $c);
        } }
}
