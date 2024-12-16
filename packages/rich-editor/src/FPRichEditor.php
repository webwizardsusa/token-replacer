<?php

namespace Filapress\RichEditor;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filapress\Core\Assets\FilapressJs;
use Filapress\RichEditor\Contracts\PluginAsExternalAssets;
use Filapress\RichEditor\Contracts\PluginHasDialog;
use Filapress\RichEditor\Plugins\AbstractPlugin;
use Filapress\RichEditor\Support\ServerRequestEvent;
use Illuminate\Support\Facades\Vite;

class FPRichEditor extends Field
{
    protected string $view = 'filapress-rich-editor::editor';

    protected array $plugins = [];

    protected array $buttons = [];

    protected string $placeholder = '';

    protected string $maxHeight = '';

    protected string $minHeight = '';

    protected bool $stickyToolbar = true;

    protected bool $withHelp = true;

    protected string $helpView = 'filapress-rich-editor::help';

    public static ?Js $scriptSrc = null;

    protected function setUp(): void
    {
        $this->registerListeners([
            'fp-rich-editor::showModal' => [
                fn (
                    FPRichEditor $component,
                    string $statePath,
                    array $arguments
                ) => $this->openDialog($component, $statePath, $arguments),
            ],
            'fp-rich-editor::serverRequest' => [
                function (
                    FPRichEditor $component,
                    string $statePath,
                    array $arguments
                ) {
                    if ($statePath === $this->getStatePath()) {
                        $this->handleServerRequest($arguments);

                    }

                },
            ],
            'fp-rich-editor::showHelp' => [
                function (
                    FPRichEditor $component,
                    string $statePath,
                    array $arguments
                ) {
                    $this->getCustomListener('help', $component, $statePath, []);

                },
            ],
        ]
        );
        $this->registerActions([
            Action::make('help')
                ->label('Open Modal')
                ->modalHeading('Editor Help')
                ->modalFooterActions([])
                ->modalActions([])
                ->modalSubmitAction(false)            //Remove Submit Button
                ->modalCancelAction(false)            // Remove Cancel Button
                ->modalContent(function () {
                    return view($this->helpView, [
                        'items' => collect($this->plugins)
                            ->filter(fn ($plugin) => $plugin instanceof AbstractPlugin)
                            ->map(fn (AbstractPlugin $plugin) => $plugin->getHelp())
                            ->filter(),
                    ]);
                }),
        ]);

    }

    public function showHelpModal(): void {}

    public function handleServerRequest(array $arguments): void
    {
        $event = new ServerRequestEvent($arguments['method'], is_array($arguments['args']) ? $arguments['args'] : []);
        $response = [];
        foreach ($this->plugins as $plugin) {
            if ($plugin instanceof AbstractPlugin) {
                foreach ($plugin->getServerRequestListener($event->getMethod()) as $callback) {
                    if (! $event->shouldStop() && $results = $callback($event)) {
                        $response = $results;
                    }
                }
            }
        }

        $this->getLivewire()->dispatch(
            event: 'fpServerResponse',
            statePath: $this->getStatePath(),
            ident: $arguments['ident'],
            response: $response,
        );
    }

    public function verifyListener(FPRichEditor $component, string $statePath): bool
    {
        return $component->isDisabled() || $statePath !== $component->getStatePath();
    }

    public function getCustomListener(string $name, FPRichEditor $component, string $statePath, array $arguments = []): void
    {

        if ($this->verifyListener($component, $statePath)) {
            return;
        }
        $component
            ->getLivewire()
            ->mountFormComponentAction($statePath, $name, $arguments);
    }

    public function openDialog(FPRichEditor $component, string $statePath, array $arguments = []): void
    {
        $this->getCustomListener($arguments['name'], $component, $statePath, $arguments['args']);

    }

    public function plugins(array $plugins): static
    {
        collect($plugins)->each(function ($plugin) {
            if ($plugin instanceof PluginHasDialog) {
                $this->registerActions([$plugin->dialog()]);
            }
            if ($plugin instanceof AbstractPlugin) {
                $plugin->setup($this);
            }
        });

        $this->plugins = $plugins;

        return $this;
    }

    public function getPlugins(): array
    {
        return collect($this->plugins)
            ->map(function ($plugin) {
                if ($plugin instanceof AbstractPlugin) {
                    return $plugin->toArray();
                }

                return $plugin;
            })->toArray();
    }

    public function buttons(array $buttons): static
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getButtons(): array
    {
        return $this->buttons;
    }

    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    public function getMaxHeight(): string
    {
        return $this->maxHeight;
    }

    public function maxHeight(string $maxHeight): static
    {
        $this->maxHeight = $maxHeight;

        return $this;
    }

    public function getMinHeight(): string
    {
        return $this->minHeight;
    }

    public function minHeight(string $minHeight): static
    {
        $this->minHeight = $minHeight;

        return $this;
    }

    public function getStickyToolbar(): bool
    {
        return $this->stickyToolbar;
    }

    public function stickyToolbar(bool $stickyToolbar): static
    {
        $this->stickyToolbar = $stickyToolbar;

        return $this;
    }

    public function sendActionToEditor(string $action, array $arguments, array $data = []): void
    {
        $coordinates = [];
        if (\Arr::has($arguments, '_coordinates')) {
            $coordinates = \Arr::get($arguments, '_coordinates', []);
        }
        $this->getLivewire()->dispatch(
            event: 'modalAction',
            statePath: $this->getStatePath(),
            action: $action,
            coordinates: $coordinates,
            args: $data,
        );

        $this->state($this->getState());
    }

    public function getIsHot(): bool
    {
        return Vite::isRunningHot();
    }

    public function callAfterStateHydrated(): static
    {
        foreach ($this->plugins as $plugin) {
            if ($plugin instanceof AbstractPlugin) {
                $this->evaluate($plugin->stateHydratedCallback());
            }
        }

        return parent::callAfterStateHydrated();
    }

    public function getStateToDehydrate(): array
    {
        if ($callback = $this->dehydrateStateUsing) {
            $state = $this->evaluate($callback);
        } else {
            $state = $this->getState();
        }
        foreach ($this->plugins as $plugin) {
            if ($plugin instanceof AbstractPlugin) {
                $state = $plugin->stateDehydrate($state);
            }
        }

        return [$this->getStatePath() => $state];
    }

    public function withHelp(bool $with): static
    {
        $this->withHelp = $with;

        return $this;
    }

    public function hasHelp(): bool
    {
        return $this->withHelp;
    }

    public function getScriptSrc(): string|Js
    {
        return self::$scriptSrc ?? FilamentAsset::getAlpineComponentSrc('editor', 'filapress/rich-editor');
    }

    public function getExternals(): array
    {
        $externals = [];
        foreach ($this->plugins as $plugin) {
            if ($plugin instanceof PluginAsExternalAssets) {
                $externals = array_merge($externals, $plugin->externalAssets());
            }
        }

        return collect($externals)
            ->map(function ($asset) {
                if ($asset instanceof FilapressJs) {
                    return [
                        'src' => $asset->getSrc(),
                        'type' => 'script',
                        'module' => $asset->fileIsHot(),
                    ];
                }

                return $asset;
            })->toArray();
    }
}
