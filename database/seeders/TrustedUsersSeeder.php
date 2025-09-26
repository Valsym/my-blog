<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrustedUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Сделать администратора доверенным
        User::where('email', 'admin@example.com')->update([
            'is_trusted' => true,
            'is_moderator' => true
        ]);

        // Можно добавить других доверенных пользователей
//        User::whereIn('email', [
//            'trusted_user@example.com',
//            'moderator@example.com'
//        ])->update(['is_trusted' => true]);

        // Или по ID
        User::whereIn('id', [1, 2, 3])->update(['is_trusted' => true]);
    }
}
