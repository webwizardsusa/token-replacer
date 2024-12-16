<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Filapress\Media\Models\FilapressMedia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@example.com',
            'password' => Hash::make('123456'),
        ]);

        $this->call(MediaSeeder::class);

        Post::create([
            'title' => 'Editor examples',
            'description' => 'Filapress ships with a rich text editor built on Tiptap. It provides an easily extensible and configurable system to fit your needs',
            'image_id' => FilapressMedia::first()->id,
            'user_id' => 1,
            'published' => true,
            'body' => <<<'BODY'
<p>We support <strong>bold</strong>, <em>italic,</em> <u>underline</u> and <s>strikethrough</s>. We also allow <a href="https://google.com" target="_blank">links</a>. </p><p>For lists we have:</p><ol><li><p>Ordered Lists</p></li><li><p>For example</p></li></ol><p>as well as:</p><ul><li><p>Unordered lists</p></li><li><p>or ul</p></li></ul><p>We also allow the following block levels:</p><h3>Heading 1</h3><h4>Heading 2</h4><h5>Heading 3</h5><blockquote><p>As well as allowing blockquotes</p></blockquote><p></p><p>For embeds, we support a few providers via our OEmbed package. More can be added later.</p><p><strong>YouTube</strong></p><oembed src="https://www.youtube.com/watch?v=hXWeiQYxT6k&amp;t=1s" title="Laravel's New Cache Token &amp; Dynamic Build Features in v11.31" provider="youtube"></oembed><p></p><p><strong>Bluesky:</strong></p><oembed src="https://bsky.app/profile/taylorotwell.bsky.social/post/3lazbivf46r2j" title="" provider="bluesky"></oembed><p><strong>Twitter:</strong></p><oembed src="https://twitter.com/laravelphp/status/1854344708186898815" title="" provider="twitter"></oembed><p>Plus about 10 more....</p>
BODY
        ]);

        for ($r = 0; $r < 20; $r++) {
            Post::create([
                'title' => fake()->words(rand(5, 12), true),
                'description' => fake()->paragraph,
                'image_id' => FilapressMedia::inRandomOrder()->first()->id,
                'user_id' => 1,
                'published' => true,
                'body' => '<p>'.fake()->paragraph.'</p>',
            ]);
        }
    }
}