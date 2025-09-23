<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем теги
        $tags = ["PHP", "Laravel", "JavaScript", "Vue", "React",
            "CSS", "HTML"];
        foreach ($tags as $tag) {
            DB::table("tags")->insert([
                "name" => $tag,
                "slug" => Str::slug($tag),
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }
    }
}
