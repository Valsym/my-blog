<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create("ru_RU");

        // Создаем комментарии
        for ($i = 0; $i < 50; $i++) {
            DB::table("comments")->insert([
                "body" => $faker->paragraph(3),
                "user_id" => rand(1, 10),
                "post_id" => rand(1, 20),
                "parent_id" => null,
                "created_at" => $faker->dateTimeThisYear,
                "updated_at" => now(),
            ]);
        }
    }
}
