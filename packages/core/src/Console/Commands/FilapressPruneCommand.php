<?php

namespace Filapress\Core\Console\Commands;

use Filapress\Core\Events\FilapressPruneRegisterEvent;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Support\Collection;

class FilapressPruneCommand extends PruneCommand
{
    protected $signature = 'filapress:prune
                                {--chunk=1000 : The number of models to retrieve per chunk of models to be deleted}
                                {--pretend : Display the number of prunable records found instead of deleting them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune Filapress models that are no longer needed';

    public function models(): Collection
    {
        $event = new FilapressPruneRegisterEvent;
        event($event);

        return (new Collection($event->get()))->filter(function ($model) {
            return class_exists($model);
        })->values();
    }
}
