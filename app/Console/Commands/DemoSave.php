<?php

namespace App\Console\Commands;

use App\Models\OEmbed;
use App\Models\Post;
use App\Models\User;
use Filapress\Media\Models\FilapressMedia;
use Filapress\Media\Models\FilapressMediaUsage;
use Illuminate\Console\Command;

class DemoSave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:save';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new set of demo data';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {

        $this->createPostsDump();
        $this->createOEmbedsDump();
        $this->createUsersDump();
        $this->createMediaDump();
        $this->output->success('Done');
    }



    public function createPostsDump(): void
    {
        file_put_contents(base_path('docs/assets/posts.json'), json_encode(Post::all()->toArray()));
    }

    public function createUsersDump(): void
    {
        $users = User::all()
            ->map(function (User $user) {
                $data = $user->toArray();
                $data['password'] = $user->password;
                $data['remember_token'] = $user->remember_token;

                return $data;
            });

        file_put_contents(base_path('docs/assets/users.json'), json_encode($users->toArray()));

    }


    public function createMediaDump(): void
    {
        $media = FilapressMedia::all();
        foreach ($media as $item) {
            $target = base_path('docs/assets/media/'.$item->path);
            $directory = pathinfo($target, PATHINFO_DIRNAME);
            \File::ensureDirectoryExists($directory);
            \File::copy(storage_path('app/public/'.$item->path), $target);
        }
        file_put_contents(base_path('docs/assets/media.json'), json_encode($media->toArray()));
        file_put_contents(base_path('docs/assets/media-usage.json'), json_encode(FilapressMediaUsage::all()->toArray()));
    }


    protected function createOEmbedsDump(): void
    {
        $dir = base_path('docs/assets/oembeds.json');
        $oembeds = OEmbed::all();
        file_put_contents($dir, json_encode($oembeds->toArray()));
    }
}
