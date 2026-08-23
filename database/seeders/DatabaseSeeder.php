<?php

namespace Database\Seeders;

use App\Models\Link;
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
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User2',
            'email' => 'test2@example.com',
        ]);

        foreach ([
            ['Anéis do poder', 'https://www.primevideo.com/', 'prime video', 'blue'],
            ['Doctor Who', 'https://www.disneyplus.com/', 'disney +', 'green'],
            ['A Casa do Dragão', 'https://www.max.com/', 'max', 'purple'],
            ['The last of us', 'https://www.max.com/br/pt/shows/the-last-of-us', 'max', 'purple'],
            ['The white lotus', 'https://www.max.com/br/pt/shows/the-white-lotus', 'max', 'purple'],
        ] as $position => [$title, $url, $category, $categoryVariant]) {
            Link::factory()->for($user)->create([
                'title' => $title,
                'url' => $url,
                'category' => $category,
                'category_variant' => $categoryVariant,
                'position' => $position + 1,
            ]);
        }
    }
}
