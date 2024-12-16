<?php

namespace App\Console\Commands;

use App\Models\OEmbed;
use App\Models\Post;
use App\Models\User;
use Filapress\Media\Models\FilapressMedia;
use Filapress\Media\Models\FilapressMediaUsage;
use Illuminate\Console\Command;

class DemoReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the demo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Resetting demo');
        $this->call('migrate:fresh');
        \Storage::disk('public')->deleteDirectory('images');
        $this->info('Creating users');
        $this->createUsers();
        $this->info('Creating Media');
        $this->createMedia();
        $this->info('Creating oembeds');
        $this->createOEmbeds();
        $this->info('Creating posts');
        $this->createPosts();

    }

    public function createPosts(): void
    {
        Post::truncate();
        $data = json_decode(file_get_contents(base_path('docs/assets/posts.json')), true);
        foreach ($data as $item) {
            Post::create($item);
        }
    }



    public function createUsers(): void
    {
        User::truncate();
        $data = json_decode(file_get_contents(base_path('docs/assets/users.json')), true);
        foreach ($data as $item) {
            User::create($item);
        }
    }

    public function createMedia(): void
    {
        FilapressMedia::all()
            ->each(fn ($item) => $item->forceDelete());
        FilapressMediaUsage::truncate();
        \File::copyDirectory(base_path('docs/assets/media/'), storage_path('app/public/'));
        $data = json_decode(file_get_contents(base_path('docs/assets/media.json')), true);
        foreach ($data as $item) {
            $media = FilapressMedia::create($item);
            $media->getType()->generateThumbnail($media);
            $media->getType()->generateResponsive($media);
            //   $media->getType()->generateVariants($media);
        }

        $data = json_decode(file_get_contents(base_path('docs/assets/media-usage.json')), true);
        foreach ($data as $item) {
            FilapressMediaUsage::create($item);
        }
    }


    protected function createOEmbeds(): void
    {
        OEmbed::truncate();
        $dir = base_path('docs/assets/oembeds.json');
        $data = file_get_contents($dir);
        $data = json_decode($data, true);
        foreach ($data as $item) {
            OEmbed::create($item);
        }
    }

}
