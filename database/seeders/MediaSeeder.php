<?php

namespace Database\Seeders;

use Filapress\Media\Models\FilapressMedia;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        FilapressMedia::factory()->count(12)->create();
    }
}
