<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'list:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kullanıcıları listele';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        
        $this->info("👥 Mevcut Kullanıcılar ({$users->count()}):");
        
        foreach ($users as $user) {
            $this->line("   📧 {$user->email} ({$user->name})");
        }
    }
}
