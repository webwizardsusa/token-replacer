<?php

namespace Filapress\Media\Actions;

use Filapress\Media\Models\FilapressMedia;
use Illuminate\Support\Facades\DB;

class UpdateMediaAction
{
    protected FilapressMedia $media;

    protected array $data;

    public function __construct(FilapressMedia $media, array $data)
    {
        $this->media = $media;
        $this->data = $data;
    }

    public static function run(FilapressMedia $media, array $data): FilapressMedia
    {
        return app(static::class, [
            'media' => $media,
            'data' => $data,
        ])->handle();
    }

    public function handle(): FilapressMedia
    {
        DB::beginTransaction();
        try {
            $this->media->getType()->update($this->media, $this->data)
                ->save();
            DB::commit();

            return $this->media;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
