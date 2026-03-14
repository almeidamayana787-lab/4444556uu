<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);

        // Seed default global settings
        $defaults = [
            'home_background' => '/founde/eu7.png',
            'home_banners' => json_encode(['/banner/banner.avif']),
            'category_icon_popular' => '/casino_icons/popular.avif',
            'category_icon_slot' => '/casino_icons/slots.avif',
            'category_icon_retro' => '/casino_icons/retro.png',
            'support_telegram' => 'https://t.me/suporte',
            'support_whatsapp' => 'https://wa.me/5500000000000',
            'support_facebook' => 'https://facebook.com',
            'support_instagram' => 'https://instagram.com',
            'invite_bonus_tiers' => json_encode([
                ['people' => 1, 'bets' => 300, 'reward' => 50],
                ['people' => 20, 'bets' => 300, 'reward' => 500]
            ]),
        ];

        foreach ($defaults as $key => $value) {
            \App\Models\GlobalSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
