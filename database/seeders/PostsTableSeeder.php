<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create("ru_RU");

        // теги
        $tags = ["PHP", "Laravel", "JavaScript", "Vue", "React",
            "CSS", "HTML"];

        // Создаем статьи
        for ($i = 0; $i < 20; $i++) {
            $title = $faker->sentence(6);
            $postId = DB::table("posts")->insertGetId([
                "title" => $title,
                "slug" => Str::slug($title),
                "content" => $faker->realText(2000),
                "excerpt" => $faker->sentence(12),
                "user_id" => rand(1, 10),
                "views" => rand(0, 1000),
                "published" => true,
                "published_at" => $faker->dateTimeThisYear,
                "created_at" => now(),
                "updated_at" => now(),
            ]);
            // Добавляем теги к статье
            $postTags = [];
            $tagCount = rand(2, 5);
            $randomTags = array_rand($tags, $tagCount);
            if (!is_array($randomTags)) {
                $randomTags = [$randomTags];
            }
            foreach ($randomTags as $tagIndex) {
                $postTags[] = [
                    "post_id" => $postId,
                    "tag_id" => $tagIndex + 1, // +1 потому что
                    // array_rand возвращает индексы с 0
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
            }
            DB::table("post_tag")->insert($postTags);
        }
    }
}
