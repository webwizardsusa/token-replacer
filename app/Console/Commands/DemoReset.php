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
       // $this->createPostsDump();
        //$this->createUsers();
        //$this->createMedia();
       // $this->createMediaDump();
    }

    public function createPosts(): void
    {
        Post::truncate();
        $data = json_decode(file_get_contents(base_path('docs/assets/posts.json')), true);
        foreach($data as $item) {
            Post::create($item);
        }
    }
    public function createPostsDump(): void
    {
        file_put_contents(base_path('docs/assets/posts.json'), json_encode(Post::all()->toArray()));
    }
    public function createUsers(): void
    {
        User::truncate();
        $data = json_decode(file_get_contents(base_path('docs/assets/users.json')), true);
        foreach($data as $item) {
            User::create($item);
        }
    }
    public function createUsersDump(): void
    {
        $users = User::all()
        ->map(function(User $user) {
            $data = $user->toArray();
            $data['password'] = $user->password;
            $data['remember_token'] = $user->remember_token;
            return $data;
        });

        file_put_contents(base_path('docs/assets/users.json'), json_encode($users->toArray()));

    }

    public function createMedia(): void
    {
        FilapressMedia::all()
            ->each(fn($item) => $item->forceDelete());
        FilapressMediaUsage::truncate();
        \File::copyDirectory(base_path('docs/assets/media/'), storage_path('app/public/'));
        $data = json_decode(file_get_contents(base_path('docs/assets/media.json')), true);
        foreach($data as $item) {
            $media = FilapressMedia::create($item);
            $media->getType()->generateThumbnail($media);
            $media->getType()->generateResponsive($media);
         //   $media->getType()->generateVariants($media);
        }

        $data = json_decode(file_get_contents(base_path('docs/assets/media-usage.json')), true);
        foreach($data as $item) {
            FilapressMediaUsage::create($item);
        }
    }
    public function createMediaDump(): void
    {
        $media = FilapressMedia::all();
        foreach($media as $item) {
            $target = base_path('docs/assets/media/'.$item->path);
            $directory = pathinfo($target, PATHINFO_DIRNAME);
            \File::ensureDirectoryExists($directory);
            \File::copy(storage_path('app/public/' . $item->path), $target);
        }
        file_put_contents(base_path('docs/assets/media.json'), json_encode($media->toArray()));
        file_put_contents(base_path('docs/assets/media-usage.json'), json_encode(FilapressMediaUsage::all()->toArray()));
    }
    protected function createOEmbeds(): void
    {
        OEmbed::truncate();
        $dir = base_path('docs/assets/oembeds.json');
        $data = file_get_contents($dir);
        $data = json_decode($data, true);
        foreach($data as $item) {
            OEmbed::create($item);
        }
    }

    protected function createOEmbedsDump(): void
    {
        $dir = base_path('docs/assets/oembeds.json');
        $oembeds = OEmbed::all();
        file_put_contents($dir, json_encode($oembeds->toArray()));
    }
}
