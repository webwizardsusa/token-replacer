<?php

namespace App\Console\Commands;

use App\Models\Post;
use Filapress\Media\Models\FilapressMedia;
use Illuminate\Console\Command;

class AppTester extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tester';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $post = Post::find(2);
        $post->delete();
        dd("DONE");
        $post->syncMediaUsage();
        dd("OK");
    }
}
