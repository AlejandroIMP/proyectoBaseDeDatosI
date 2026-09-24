<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class ResourceController extends Controller
{
    /** @var class-string<Model> */
    protected string $model;

    /** @var list<string> */
    protected array $with = [];

    /**
     * Validation rules for the resource; `$record` is set when updating.
     *
     * @return array<string, array<int, mixed>>
     */
    abstract protected function rules(Request $request, ?Model $record): array;

    /**
     * Attributes filled on create when the client does not send them.
     *
     * @return array<string, mixed>
     */
    protected function defaults(Request $request): array
    {
        return [];
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 15), 1), 100);

        return response()->json(
            $this->model::query()->with($this->with)->orderBy('id')->paginate($perPage)
        );
    }

    public function show(string $id): JsonResponse
    {
        return response()->json($this->find($id));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules($request, null));

        $record = $this->model::query()->create([...$this->defaults($request), ...$data]);

        return response()->json($record->refresh()->load($this->with), 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $record = $this->find($id);

        $rules = array_map(
            fn (array $fieldRules) => ['sometimes', ...$fieldRules],
            $this->rules($request, $record)
        );

        $record->update($request->validate($rules));

        return response()->json($record->refresh()->load($this->with));
    }

    public function destroy(string $id): Response
    {
        $this->find($id)->delete();

        return response()->noContent();
    }

    protected function find(string $id): Model
    {
        return $this->model::query()->with($this->with)->findOrFail($id);
    }
}
