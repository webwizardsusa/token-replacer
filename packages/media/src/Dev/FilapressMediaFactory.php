<?php

namespace Filapress\Media\Dev;

use App\Models\User;
use Filapress\Media\Contracts\GeneratesFakeMedia;
use Filapress\Media\MediaCollections;
use Filapress\Media\MediaType;
use Filapress\Media\MediaTypes;
use Filapress\Media\Models\FilapressMedia;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

class FilapressMediaFactory extends Factory
{
    protected $model = FilapressMedia::class;

    public function definition(): array
    {

        $collection = app(MediaCollections::class)->all()->random()?->name();
        return [
            'user_id' => (int) User::inRandomOrder()->first()?->id,
            'type' => $this->getType(),
            'collection' => $collection,
            'status' => true,
            'title' => $this->faker->sentence,
        ];
    }

    protected function callAfterMaking(Collection $instances): void
    {
        $instances->each(function (FilapressMedia $instance) {
            $instance->getType()->fake($instance);
        });
        parent::callAfterMaking($instances);
    }

    protected function getType(): ?string
    {
        return collect(app(MediaTypes::class)->all())
            ->filter(fn (MediaType $type) => $type instanceof GeneratesFakeMedia)
            ->random()?->name();
    }
}
