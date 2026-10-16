<?php

namespace App\Livewire;

use App\Http\Controllers\Api\ResourceController;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

/**
 * Base for the CRUD Volt pages: list with search and pagination, plus a modal form for create/edit.
 *
 * Validation reuses the rules of the matching API controller so web and API stay in sync.
 */
abstract class CrudComponent extends Component
{
    use WithPagination;

    /** @var class-string<Model> */
    protected string $model;

    /** @var class-string<ResourceController> */
    protected string $controller;

    /** @var list<string> */
    protected array $with = [];

    /** @var list<string> */
    protected array $searchable = ['name'];

    /**
     * Initial form values when creating a record.
     *
     * @var array<string, mixed>
     */
    protected array $defaults = [];

    /**
     * Form fields that are validated but not stored as columns (handled in afterSave()).
     *
     * @var list<string>
     */
    protected array $relationFields = [];

    /**
     * Human readable names for the validation messages, keyed by field.
     *
     * @var array<string, string>
     */
    protected array $labels = [];

    #[Url(except: '')]
    public string $search = '';

    /** @var array<string, mixed> */
    public array $form = [];

    public ?int $editingId = null;

    public bool $showForm = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->form = $this->defaults;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $record = $this->findRecord($id);

        $this->resetValidation();
        $this->editingId = $record->getKey();
        $this->form = $this->formData($record);
        $this->showForm = true;
    }

    public function save(): void
    {
        $record = $this->editingId ? $this->findRecord($this->editingId) : null;

        $this->form = array_map(fn ($value) => $value === '' ? null : $value, $this->form);

        $request = $this->formRequest();
        $rules = $this->rules($request, $record);

        $validated = $this->validate(
            Arr::prependKeysWith($rules, 'form.'),
            [],
            Arr::prependKeysWith($this->labels, 'form.'),
        )['form'] ?? [];

        $data = $this->persistableData($validated, $record);

        if ($record) {
            $record->update($data);
        } else {
            $record = $this->model::query()->create([...$this->controller()->defaultAttributes($request), ...$data]);
        }

        $this->afterSave($record, $validated);

        $this->showForm = false;
        session()->flash('status', $this->editingId ? 'Registro actualizado.' : 'Registro creado.');
    }

    public function delete(int $id): void
    {
        try {
            $this->findRecord($id)->delete();
            session()->flash('status', 'Registro eliminado.');
        } catch (QueryException $e) {
            // SQLSTATE 23000 = integrity constraint violation (the record is still referenced)
            if ((string) $e->getCode() !== '23000') {
                throw $e;
            }

            session()->flash('error', 'No se puede eliminar: el registro esta referenciado por otros registros.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function with(): array
    {
        return ['records' => $this->records(), ...$this->options()];
    }

    /**
     * Option lists for the form selects, merged into the view data.
     *
     * @return array<string, mixed>
     */
    protected function options(): array
    {
        return [];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(Request $request, ?Model $record): array
    {
        return Arr::except($this->controller()->validationRules($request, $record), ['user_id']);
    }

    /**
     * Column values to store from the validated form data.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    protected function persistableData(array $validated, ?Model $record): array
    {
        return Arr::except($validated, $this->relationFields);
    }

    /**
     * Hook for subclasses that need to persist relations (e.g. many-to-many) after saving.
     *
     * @param  array<string, mixed>  $validated
     */
    protected function afterSave(Model $record, array $validated): void
    {
        //
    }

    /**
     * @return array<string, mixed>
     */
    protected function formData(Model $record): array
    {
        $fields = array_keys($this->rules($this->formRequest(), $record));

        return [...$this->defaults, ...Arr::only($record->attributesToArray(), $fields)];
    }

    /**
     * @return LengthAwarePaginator<int, Model>
     */
    protected function records(): LengthAwarePaginator
    {
        return $this->model::query()
            ->with($this->with)
            ->when($this->search !== '', fn (Builder $query) => $query->where(function (Builder $query) {
                foreach ($this->searchable as $column) {
                    // "relation.column" searches on a related model
                    if (str_contains($column, '.')) {
                        [$relation, $relatedColumn] = explode('.', $column, 2);
                        $query->orWhereRelation($relation, $relatedColumn, 'like', '%'.$this->search.'%');
                    } else {
                        $query->orWhere($column, 'like', '%'.$this->search.'%');
                    }
                }
            }))
            ->orderBy('id')
            ->paginate(10);
    }

    protected function findRecord(int $id): Model
    {
        return $this->model::query()->findOrFail($id);
    }

    protected function controller(): ResourceController
    {
        return app($this->controller);
    }

    /**
     * Builds a request carrying the form data so the API rules/defaults can read it as usual.
     */
    protected function formRequest(): Request
    {
        $request = Request::create('/', 'POST', $this->form);
        $request->setUserResolver(fn () => auth()->user());

        return $request;
    }
}
