<?php

namespace Filapress\Media\Listeners;

use Filapress\Core\Events\RegisterPermissionsEvent;
use Filapress\Media\MediaTypes;

class MediaPermissionsListener
{
    public function handle(RegisterPermissionsEvent $event): void
    {
        foreach (app(MediaTypes::class)->all() as $mediaType) {
            $event->permissions->add('filapress.media.list-'.$mediaType->name(), 'Can list '.$mediaType->label().' media type', 'media');
            $event->permissions->add('filapress.media.create-'.$mediaType->name(), 'Can create '.$mediaType->label().' media type', 'media');
            $event->permissions->add('filapress.media.view-'.$mediaType->name(), 'Can list '.$mediaType->label().' media type', 'media');
            $event->permissions->add('filapress.media.update-any'.$mediaType->name(), 'Can update any '.$mediaType->label().' media type', 'media');
            $event->permissions->add('filapress.media.update-'.$mediaType->name(), 'Can update '.$mediaType->label().' media type', 'media');
            $event->permissions->add('filapress.media.delete-'.$mediaType->name(), 'Can delete '.$mediaType->label().' media type', 'media');
            $event->permissions->add('filapress.media.delete-any-'.$mediaType->name(), 'Can delete any '.$mediaType->label().' media type', 'media');
            $event->permissions->add('filapress.media.restore-'.$mediaType->name(), 'Can restore '.$mediaType->label().' media type', 'media');
            $event->permissions->add('filapress.media.restore-any-'.$mediaType->name(), 'Can restore any '.$mediaType->label().' media type', 'media');
            $event->permissions->add('filapress.media.force-delete-'.$mediaType->name(), 'Can force delete '.$mediaType->label().' media type', 'media');
            $event->permissions->add('filapress.media.force-delete-any-'.$mediaType->name(), 'Can force delete any '.$mediaType->label().' media type', 'media');
        }
    }
}
