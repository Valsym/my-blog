<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;


class UpdatePostsPublishedStatus extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::where('published', '1')->update(['published' => 'published']);
        Post::where('published', '0')->update(['published' => 'draft']);
        Post::where('published', '2')->update(['published' => 'moderation']);

        $this->command->info('Статусы постов успешно обновлены!');

    }
}
