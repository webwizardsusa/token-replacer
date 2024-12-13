<?php

namespace Filapress\Media\Types;

use Arr;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filapress\Media\Actions\ResponsiveSizeGenerator;
use Filapress\Media\Actions\ThumbnailGenerator;
use Filapress\Media\Contracts\GeneratesFakeMedia;
use Filapress\Media\Dev\ImageFaker;
use Filapress\Media\Filament\Components\Form\MediaUpload;
use Filapress\Media\Images\ImageFactory;
use Filapress\Media\ImageVariants;
use Filapress\Media\MediaType;
use Filapress\Media\Models\FilapressMedia;
use Filapress\Media\Models\FilapressMediaVariant;
use Filapress\Media\Support\FileUtils;
use Filapress\Media\Traits\MediaHasVariations;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Image;
use Storage;
use Webwizardsusa\TokenReplace\TokenReplacer;
use Webwizardsusa\TokenReplace\Transformers\ArrayTransformer;
use Webwizardsusa\TokenReplace\Transformers\DateTransformer;
use Webwizardsusa\TokenReplace\Transformers\FileTransformer;

abstract class ImageType extends MediaType implements GeneratesFakeMedia
{
    use MediaHasVariations;
    public static function configure(?string           $disk = null,
                                     string|array|null $path = null,
                                     array             $types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
                                     ?string           $maxSize = null,
                                     ?int              $jpegQuality = null,
                                     array             $responsiveSizes = [],
                                     array             $variants = []
    ): array
    {
        return [
            'class' => static::class,
            'disk' => $disk,
            'path' => $path,
            'types' => $types,
            'maxSize' => $maxSize,
            'jpegQuality' => $jpegQuality,
            'responsiveSizes' => $responsiveSizes,
            'variants' => $variants,
        ];
    }

    public function label(): string
    {
        return 'Image';
    }

    protected function makePathGenerator(string $token, string|UploadedFile $file): TokenReplacer
    {
        return TokenReplacer::from($token)
            ->with('file', new FileTransformer($file instanceof UploadedFile ? $file->getClientOriginalName() : $file))
            ->with('date', new DateTransformer());

    }

    public function handleAttach(FilapressMedia $media, string|UploadedFile $file): FilapressMedia
    {
        $path = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $data = getimagesize($path);
        $media->width = $data[0];
        $media->height = $data[1];
        $media->mime = $data['mime'];
        $media->filesize = filesize($path);
        if ($media->disk && $media->path) {
            if (Storage::disk($media->disk)->exists($media->path)) {
                Storage::disk($media->disk)->delete($media->path);
            }
            $savePath = pathinfo($media->path, PATHINFO_DIRNAME);
        } else {
            $media->disk = $this->disk;
            $savePath = $this->makePathGenerator($this->config('path', config('filapress.media.path')), $file)
                ->transform();
        }

        $media->filename = $file instanceof UploadedFile ? $file->getClientOriginalName() : pathinfo($file, PATHINFO_FILENAME);

        if (Storage::disk($media->disk)->exists($savePath)) {
            $savePath = FileUtils::appendFileName($savePath, '-' . uniqid());
        }
        $media->path = $savePath;
        Storage::disk($media->disk)->put($savePath, file_get_contents($path));
        $this->generateThumbnail($media);
        $this->generateResponsive($media);
        $this->generateVariants($media);
        return $media;
    }


    public function generateThumbnail(FilapressMedia $media, ?Image $source = null): FilapressMedia {
        if (!$source) {
            $source = ImageFactory::make()->fromStorage($media->disk, $media->path);
        }
        ThumbnailGenerator::make($media)->generate();

        return $media;
    }

    public function generateResponsive(FilapressMedia $media, ?Image $source = null): FilapressMedia
    {
        if (!$source) {
            $source = ImageFactory::make()->fromStorage($media->disk, $media->path);
        }

        $media->sizes->each(function ($size) use ($media) {
            if (Storage::disk($media->disk)->exists($size['path'])) {
                Storage::disk($media->disk)->delete($size['path']);
            }
        });
        $newSizes = [];

        foreach ($this->responsiveSizes() as $size) {
            $newPath = FileUtils::appendFileName($media->path, '-' . $size);
            $results = ResponsiveSizeGenerator::fromImage($source, $size)
                ->saveTo($media->disk, $newPath)
                ->getResults();
            if ($results) {
                $newSizes[$size] = $results->toArray();
            }
        }
        $media->sizes = $newSizes;
        return $media;
    }

    public function form(Form $form, ?FilapressMedia $record = null): array
    {
        $form->columns(1);
        return [
            MediaUpload::make('path')
                ->image()
                ->label('Image')
                ->disk($record?->disk)
                ->required(),
            TextInput::make('title')
                ->helperText('The title is displayed in the media browser and media browser search results, and is for internal use only')
                ->required(),
            TextInput::make('data.alt')
                ->required()
                ->helperText('This is the alt text for the image. It displays if the browser can not load an image, or the person is hard of seeing'),
        ];

    }


    public function fake(FilapressMedia $media, ?int $width = null, ?int $height = null, ?string $alt = null, ?string $filename = null): FilapressMedia
    {
        $tempFilePath = ImageFaker::make($width, $height, $filename)->generate();
        $alt = $alt ?? fake()->sentence;
        $this->attachFile($media, $tempFilePath);
        $media->data = [
            'alt' => $alt,
        ];
        $media->save();
        try {
            unlink($tempFilePath);
        } catch (\Exception $e) {
            // silence is golden
        }

        return $media;
    }

    protected function getResponsiveSizes(FilapressMedia|FilapressMediaVariant $media): array
    {
        $sizes = [];
        foreach ($media->sizes as $size => $sizeData) {
            $sizeUrl = Storage::disk($media->disk)->url($sizeData['path']);
            $sizes[] = [
                'url' => $sizeUrl,
                'width' => $sizeData['width'],
                'maxWidth' => (int)round($sizeData['width'] * config('filapress.media.responsive_variance', 1.5)),
                'type' => $sizeData['mime'],
            ];
        }
        uasort($sizes, function ($a, $b) {
            return $a['maxWidth'] <=> $b['maxWidth'];
        });
        return $sizes;
    }
    public function render(FilapressMedia $media, ?FilapressMediaVariant $variant = null, array $attributes = [], bool $preview = false): mixed
    {
        $url = $variant ? $variant->getUrl() : $media->getUrl();

        $sizes = $this->getResponsiveSizes($variant ?? $media);
        $link = Arr::get($attributes, 'link');
        if ($link === '__source__') {
            $link = $url;
        }

        $target = '_blank';
        if (Arr::has($attributes, 'target')) {
            $target = '_self';
        }

        return view('filapress-media::image', [
            'media' => $media,
            'src' => $url,
            'width' => $media->width,
            'height' => $media->height,
            'alt' => Arr::get($attributes, 'alt', $media->getExtra('alt')),
            'preview' => $preview,
            'sizes' => $sizes,
            'link' => $link,
            'align' => Arr::get($attributes, 'align'),
            'caption' => Arr::get($attributes, 'caption'),
            'target' => $target,
        ]);
    }

    public function mediaInfo(FIlapressMedia $media): array
    {
        return [
            'Type' => $this->label(),
            'Dimensions' => $media->width . 'x' . $media->height,
            'Filesize' => FileUtils::convertBytesToFriendly($media->filesize),
        ];
    }

    public function prepareInsertAttributes(FilapressMedia $media, array $attributes): array
    {
        if (isset($attributes['link'])) {
            if ($attributes['link'] === '__source__') {
                $attributes['link_type'] = '__source__';
            } elseif ($attributes['link']) {
                $attributes['link_type'] = 'link';
                $attributes['link_url'] = $attributes['link'];
            } else {
                $attributes['link_type'] = 'none';
            }
        }
        $attributes['link_type'] = $attributes['link_type'] ?? 'none';
        return $attributes;
    }

    public function onInsert(FilapressMedia $media, array $attributes, array $options): array
    {
        if (!$options['inline']) {
            return $attributes;
        }

        if ($attributes['link_type'] === 'link') {
            $attributes['link'] = $attributes['link_url'];
        } elseif ($attributes['link_type'] === '__source__') {
            $attributes['link'] = $media->getUrl();
        }
        $attributes['data-preview'] = $this->render($media, null, $attributes, false)->render();
        return $attributes;
    }

    public function insertForm(FIlapressMedia $media, Form $form): array
    {
        return [
            TextArea::make('caption'),
            TextInput::make('alt')
                ->placeholder($media->getExtra('alt'))
                ->helperText('This is the alt text for the image. It displays if the browser can not load an image, or the person is hard of seeing'),
            Select::make('align')
                ->placeholder('none')
                ->options([
                    'left' => 'Left',
                    'center' => 'Center',
                    'right' => 'Right'
                ]),

            Select::make('link_type')
                ->default('none')
                ->live()
                ->required()
                ->options([
                    'none' => 'None',
                    '__source__' => 'Original Image',
                    'link' => 'Web Link',
                ]),

            TextInput::make('link_url')
                ->url()
                ->required(fn(Get $get) => $get('link_type') === 'link')
                ->hidden(fn(Get $get) => $get('link_type') !== 'link'),
        ];
    }
}
