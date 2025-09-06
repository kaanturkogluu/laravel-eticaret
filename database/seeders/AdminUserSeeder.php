<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@basital.com',
            'password' => bcrypt('admin123'),
            'is_admin' => true,
            'phone' => '0555 123 45 67',
            'address' => 'Admin Adresi',
        ]);

        $this->command->info('Admin kullanıcısı oluşturuldu: admin@basital.com / admin123');
    }
}
