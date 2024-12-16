# Filapress Media

**This package is under active development and should not yet be used in production.**

This package provides media support for Filapress. It includes:

- An extensible system to easily add additional media types.
- Image manipulation to automatically generated responsive sizes.
- Image manipulations to create "variants"
- Tracking of media usage
- Media segregation via collections
- A Filament filed to browse or upload media
- Integration with the Filapress rich editor
- Image captioning.

Out of the box, we provide an image type, but additional types can be easily added via the robust API.

## Installation

```
composer require filapress/media
```

We follow the recommended practice of Filament, so you will need to make sure to publish your panel theme. If you have
not done so yet, please refer to
the [Filament documentation](https://filamentphp.com/docs/3.x/panels/themes#creating-a-custom-theme) on publishing your
theme. Once that is complete, follow the following steps:

1. Import the plugin's style sheet into your theme's css:

```php
@import 'vendor/filapress/resources/css/plugin.css';
```

2. Add the plugin's views to your theme's tailwind.config.js:

```javascript
content: [
    ...
        'vendor/filapress/resources/views/**/*.blade.php',
]
```

3. Make sure you have `tailwindcss/nesting` in your `postcss.config.js` file:

```javascript
module.exports = {
    plugins: {
        'tailwindcss/nesting': {},
        tailwindcss: {},
        autoprefixer: {},
    },
}
```

4. Rebuild your custom theme:

```php
npm run build
```

Next, for displaying the images properly on your main site, we provide CSS you can either copy or include in your build
pipeline. See `resources/css/media.css` in the package directory.

Optionally, if you would like a separate resource page in your admin panel for media, you can include the resource in
your panel service provider:

```php
->resources([
    \Filapress\Media\Filament\FilapressMediaResource::class,
])
```

**Highly Recommended:**

Publish the configuration:

```
php artisan vendor:publish --tag=filapress-media-config
```

The configuration should be self-explanatory. For the paths, we
use [webwizardsusa/token-replacer](https://github.com/webwizardsusa/token-replacer) to help generate paths.

While this package comes with image support, it is highly recommended to override the ImageType shipped with the package
to create your own and add your own authorization logic.

## Authorization

This package uses an included Laravel policy for authorization. That policy passes the authorization logic to the
requested type and collection (if used). If a collection is used and the permission returns null, then they media type's
permission handling will be used.

## Image Generation

This package currently generates responsive sizes, thumbnails and variants when the image is created or the file
updated. Considering this is all done via livewire, and generation is rather quick, this usually works out best, but we
do have future plans to include queue support for image generation.

## Collections

Collections are simply a way to organize media assets. For example, let's say you have two collections:

1. User profile
2. Post content

For User Profile, you want more strict permissions applied, such as only the user or an admin can update/delete it,
however for post content, you may want anyone who is a contributor to be able to update/delete an item.

Collections also have the ability to define their own variations. Using our example above, you may want to create a
rounded version of the image for users, while creating a set aspect-ratio image for a "card" when an image is in the
post content collection.

Creating a collection is a simple matter of creating the collection class, then registering it in the configuration.

Creating the class

```php
namespace App\Media\Collections;

use Filapress\Media\MediaCollection;

class ContentCollection extends MediaCollection
{
    public function label(): string
    {
        return 'Content';
    }

    public function variants(): array
    {
        return ['card'];
    }
}

```

Once that is complete, register the new collection in your config:

```php
    'collections' => [
        \App\Media\Collections\ContentCollection::class,
    ],
```

## Variants

Simply put, variants are different sizes or effects of uploaded images. Under the hood we
use [Intervention Image](https://image.intervention.io/v3). While no variants are supplied by default, they are easy to
add.

First, create your new variant class. Here's a simple example to create a card type variant.

```php
namespace App\Media\Variants

use Filapress\Media\ImageVariant;
use Intervention\Image\Image;

class Card extends ImageVariant
{
    public function name(): string
    {
        return 'card';
    }

    protected function process(Image $image): static
    {
        $this->generated = $image->cover(1200, 800, $this->option('focal_point', 'center'));

        return $this;
    }
}

```

Once that is created, simple register the variant in the variants section of your config.

```php
'image_variants' => [
    \App\Media\Variants\Card::class,
],
```

## Creating New Types

**NOTE: This feature is still in development. Documentation coming soon. If you can't wait, then please review
the `Filapress\Media\MediaType `abstract class**

## Soft Deletes

This package utilizes Laravel's soft-deletes under the hood, with the Eloquent Prunable trait. The pruning is handled
via the Filapress Core modules, `filapress:prune` command and is automatically scheduled hourly.

You can adjust the timeframe in which Media models are automatically pruned, or disable it all together via the
configuration value of `filapress.media.prune_after`.

## Media Field

This package provides a media field for your Filapres forms:

```php
\Filapress\Media\Filament\Components\Form\MediaBrowserField::make('image_id')
    ->types(['image'])
    ->collection('content')
    ->label('Image'),
```

The collection is optional. If omitted then media with no collections are shown/created. The field does not do an actual
upload via the form, but rather through the media browser.

## Rich Editor Support

You can add rich editor support via the plugin this package provides:

```php
               FPRichEditor::make('body')
                    ->plugins([
                        MediaPlugin::make()
                            ->collection('content'),
                    ])
                    ->buttons([
                        'media',
                    ]),
```

When media is inserted into the rich editor, it is done so via a custom HTML tag called fp-media:

```html

<fp-media media="9db52d45-ed1d-48bc-b7df-2a14ac3ae377">And images support captions.</fp-media>
```

When you run that through Webwizards HTML Refiner, it will transform that tag to:

```html

<div class="filapress-media filapress-media-image ">
    <figure>
        <picture>
            <source srcset="http://127.0.0.1:8000/storage/images/2024/12/sit_id_nihil-400.jpg"
                    media="(max-width: 600px)">

            <img srcset="http://127.0.0.1:8000/storage/images/2024/12/sit_id_nihil.jpg"
                 alt="Rem quam non fuga ea quod non doloremque." width="640" height="480">

        </picture>
        <figcaption>And images support captions.</figcaption>
    </figure>
</div>
```

You can also manually render images by calling the `->render()` method on the media model.

```php
    /**
     * Renders the actual media item. 
     * 
     * @param string|null $variant The name of the variant to render, or null for the original.
     * @param array $attributes Attributes to apply to the rendered output. 
     * @param bool $preview True is this is an admin preview.
     * @return RenderAction
     */
    public function render(?string $variant = null, array $attributes = [], bool $preview = false): RenderAction
```

The RenderAction is a chainable action to make configuring the final, rendered output customizable. Finally, the
RenderAction calls render() on the media type class for your model.

## Tracking Media Usage

There's an optional feature included to track what models is using your media items. Adding tracking is very simple.
First, add the `Filapress\Media\Traits\ModelInteractsWithMedia` trait to your model. Next, define the
`public function getMediaItems(): array` method on your model. This model simply returns an array of media IDs used in
your model.

For example, here's a simple blog post class that has an image field on the model, plus allows images inside the post
body:

```php
<?php

namespace App\Models;

use App\Html\PostDefinition;
use Filapress\Media\Elements\MediaElement;
use Filapress\Media\Models\FilapressMedia;
use Filapress\Media\Traits\ModelInteractsWithMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\HtmlString;

class Post extends Model
{
    use ModelInteractsWithMedia;

    public function image(): BelongsTo
    {
        return $this->belongsTo(FilapressMedia::class);
    }

    public function renderBody(): HtmlString
    {
        return new HtmlString(PostDefinition::for($this->body)
            ->parse());
    }

    public function getMediaItems(): array
    {
        $items = [];
        if ($this->image_id) {
            $items[] = $this->image_id;
        }

        $elements = MediaElement::make()
            ->extract($this->body);

        foreach ($elements as $element) {
            if ($id = $element->getAttribute('media')) {
                $items[] = $id;
            }
        }

        return $items;
    }
}

```

Behind the scenes, this will update the FilapressMediaUsage relations whenever a model is saved.

## Future Plans

As stated above, this package is currently under heavy development. Future plans include:

1. Ability to filter by width and height.
2. Artisan commands to automatically regenerate responsive images and variants
3. Artisan commands to bootstrap variants, collections and types.
4. Add drag and drop support for media field
5. Add drag and drop support for Rich Editor
6. Add paste support for Rich Editor.
7. Write tests!!!!





