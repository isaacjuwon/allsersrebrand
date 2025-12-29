<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\User::where('is_admin', true)->first();
        $artisan = \App\Models\User::where('role', 'artisan')->first();

        if (!$admin || !$artisan)
            return;

        // Official Challenge
        \App\Models\Challenge::create([
            'creator_id' => $admin->id,
            'title' => 'Master of Carpentry 2024',
            'hashtag' => 'CarpentryMaster',
            'guidelines' => 'Showcase your best woodworking project. Must be original work with progress shots.',
            'prizes' => '$500 Voucher + Golden Badge',
            'start_at' => now()->subDays(2),
            'end_at' => now()->addDays(10),
            'is_admin_challenge' => true,
        ]);

        // Community Challenge
        \App\Models\Challenge::create([
            'creator_id' => $artisan->id,
            'title' => 'The Perfect Weld',
            'hashtag' => 'WeldingLife',
            'guidelines' => 'Post a video of your cleanest weld. No filters allowed.',
            'prizes' => 'Shoutout on social media + Custom Badge',
            'start_at' => now()->subDays(1),
            'end_at' => now()->addDays(5),
            'is_admin_challenge' => false,
        ]);

        // Past Challenge
        $pastChallenge = \App\Models\Challenge::create([
            'creator_id' => $admin->id,
            'title' => 'Pottery Throwdown',
            'hashtag' => 'Pottery2023',
            'guidelines' => 'Build a functional clay pot in under 30 minutes.',
            'prizes' => 'Pottery Kit',
            'start_at' => now()->subMonths(2),
            'end_at' => now()->subMonths(1),
            'is_admin_challenge' => true,
            'winner_id' => $artisan->id,
        ]);

        $badge = \App\Models\Badge::create([
            'challenge_id' => $pastChallenge->id,
            'name' => 'Master of Pottery',
            'description' => 'Awarded for winning the Pottery Throwdown 2024.',
        ]);

        $artisan->badges()->attach($badge->id, ['awarded_at' => now()]);
    }
}
