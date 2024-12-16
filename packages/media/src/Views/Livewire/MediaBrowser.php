<?php

namespace Filapress\Media\Views\Livewire;

use Arr;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filapress\Media\Filament\FilapressMediaResource;
use Filapress\Media\MediaType;
use Filapress\Media\MediaTypes;
use Filapress\Media\Models\FilapressMedia;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class MediaBrowser extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public array $types;

    public ?FilapressMedia $selected = null;

    protected $listeners = [
        'example' => '$refresh',
    ];

    public ?string $createType = null;

    public array $files = [];

    public string $title = 'Media Browser';

    public ?string $collection = null;

    public array $data = [
        'sort' => '-created_at',
    ];

    /**
     * @var array|mixed
     */
    public array $options;

    public function mount(array $options): void
    {
        $selected = Arr::get($options, 'selected.media');
        $this->collection = Arr::get($options, 'collection');
        if ($selected) {
            $this->selected = FilapressMedia::find($selected);
        }
        $this->options = $options;
        $filteredTypes = $options['types'] ?? [];
        $this->types = collect(app(MediaTypes::class)->all())
            ->filter(fn (MediaType $type) => $type->userCan('list'))
            ->filter(fn (MediaType $type) => empty($filteredTypes) || in_array($type->name(), $filteredTypes))
            ->mapWithKeys(fn (MediaType $type) => [$type->name() => $type->label()])
            ->toArray();
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form->statePath('data')
            ->schema([
                TextInput::make('search')
                    ->placeholder('Search')
                    ->type('search')
                    ->live()
                    ->debounce(250)
                    ->hiddenLabel(),
                Select::make('sort')
                    ->default('-created_at')
                    ->hiddenLabel()
                    ->selectablePlaceholder(false)
                    ->live()
                    ->options([
                        '-created_at' => 'Newest',
                        'created_at' => 'Oldest',
                    ]),
            ]);
    }

    public function getTypes(): Collection
    {
        return collect(app(MediaTypes::class)->all())
            ->filter(fn (MediaType $type) => $type->userCan('list'));
    }

    #[On('media-browser-create')]
    public function onCreate(string $mediaId): void
    {
        $this->createType = null;
        $this->selected = FilapressMedia::find($mediaId);
    }

    #[On('media-browser-close-create')]
    public function closeCreate(): void
    {
        $this->createType = null;
    }

    public function setCreate(string $type): void
    {
        $this->createType = $type;
    }

    public function createTypes(): Collection
    {
        return $this->getTypes()
            ->filter(fn (MediaType $type) => $type->userCan('create'));
    }

    public function closeModal(): void
    {
        $this->dispatch('close-filapress-media-browser');
    }

    #[On('media-browser-cancel')]
    public function cancel(): void
    {
        $this->selected = null;
    }

    public function typeLabel(string $type): string
    {
        return app(MediaTypes::class)->get($type)->label();
    }

    public function getItems(): array|LengthAwarePaginator
    {
        $query = FilapressMedia::query();
        if ($this->collection) {
            $query->where('collection', $this->collection);
        }
        $query->whereIn('type', array_keys($this->types));
        $sort = Arr::get($this->data, 'sort', '-created_at');
        if (str_starts_with($sort, '-')) {
            $query->orderBy(substr($sort, 1), 'desc');
        } else {
            $query->orderBy($sort, 'asc');
        }
        if (isset($this->data['search'])) {
            $query->where('title', 'like', '%'.$this->data['search'].'%');
        }

        return $query
            ->paginate(12);
    }

    public function table(Table $table): Table
    {

        $table->query(FilapressMedia::query());
        FilapressMediaResource::table($table);
        $table->bulkActions([]);
        $table->actions([

            Action::make('select')
                ->action(fn (FilapressMedia $record) => $this->select($record)),
        ]);
        $table->contentGrid([
            'md' => 2,
            'xl' => 6,
            '2xl' => 6,
        ]);

        $table->recordClasses(function (FilapressMedia $record) {
            $class = ['filapress-media-table-record'];
            if ($record->id === $this->selected?->id) {
                $class[] = 'media-browser-selected';
            }

            return $class;
        });

        $table->recordAction('select');

        return $table;

    }

    public function select($record): void
    {
        $this->selected = FilapressMedia::find($record);
        //$this->dispatch('example')->self();
    }

    public function showCreate(string $name)
    {
        dd($name);
    }

    public function getTitle(): string
    {
        if ($this->createType) {
            return 'Create New '.$this->typeLabel($this->createType);
        }

        return 'Media Browser';
    }

    public function render()
    {
        return view('filapress-media::components.browser.window');
    }
}
