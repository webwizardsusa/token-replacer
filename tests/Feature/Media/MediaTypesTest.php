<?php

use Filapress\Media\MediaTypes;
use Filapress\Media\Models\FilapressMedia;
use Filapress\Media\Models\FilapressMediaUsage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Webwizardsusa\TokenReplace\Transformers\DateTransformer;
use function PHPUnit\Framework\assertEquals;

uses(RefreshDatabase::class);

it('it tracks media usages', function () {

    $media = FilapressMedia::factory()->create();

    $post = \App\Models\Post::create([
        'title' => 'test',
        'body' => 'test',
        'image_id' => FilapressMedia::first()->id,
    ]);

    $this->assertEquals(1, FilapressMediaUsage::query()->count());
    $post->delete();
    $this->assertEmpty(FilapressMediaUsage::all());
});

it('it handles soft deletes', function () {
    $media = FilapressMedia::factory()->create();
    $variant = new \App\Media\ImageVariants\Card();
    $variant->generateFromStorage($media->disk, $media->path);
    if ($variant->isGenerated()) {
        $savePath = \Webwizardsusa\TokenReplace\TokenReplacer::from(config('filapress.media.variant_path'))
            ->with('file', new \Webwizardsusa\TokenReplace\Transformers\FileTransformer($media->path))
            ->with('date', new DateTransformer())
            ->with('variant', new \Webwizardsusa\TokenReplace\Transformers\ArrayTransformer([
                'name' => 'card'
            ]))
            ->transform();
        $variant->saveTo($savePath, $media->disk);
    }
    $basePath = pathinfo($media->path, PATHINFO_DIRNAME);
    $fileCount = count(Storage::disk($media->disk)->files($basePath));

    $media->delete();
    assertEquals($fileCount, count(Storage::disk($media->disk)->files($basePath)));
    $media->forceDelete();
    assertEquals(0, count(Storage::disk($media->disk)->files($basePath)));
});
