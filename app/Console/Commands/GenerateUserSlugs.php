<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateUserSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:generate-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate unique slugs for users who do not have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = \App\Models\User::whereNull('slug')->get();

        if ($users->isEmpty()) {
            $this->info('All users already have slugs.');
            return;
        }

        $this->info("Generating slugs for {$users->count()} users...");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            $user->slug = \Illuminate\Support\Str::slug($user->name) . '-' . \Illuminate\Support\Str::random(8);
            $user->save();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Slugs generated successfully!');
    }
}
