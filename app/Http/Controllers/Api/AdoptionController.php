<?php

namespace App\Http\Controllers\Api;

use App\Models\Adoption;
use App\Models\AdoptionStatus;
use App\Models\Pet;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AdoptionController extends ResourceController
{
    /** @var class-string<Adoption> */
    protected string $model = Adoption::class;

    /** @var list<string> */
    protected array $with = ['pet', 'person', 'adoptionStatus', 'user'];

    protected function defaults(Request $request): array
    {
        return ['user_id' => $request->user()?->id ?? \App\Models\User::query()->first()?->id ?? 1];
    }

    protected function rules(Request $request, ?Model $record): array
    {
        $adoption = $record instanceof Adoption ? $record : null;
        $requestedAt = $request->input('requested_at') ?: $adoption?->requested_at?->toDateString();
        $approvedAt = $request->input('approved_at') ?: $adoption?->approved_at?->toDateString();
        $deliveredAt = $request->input('delivered_at') ?: $adoption?->delivered_at?->toDateString();

        return [
            'pet_id' => ['required', 'integer', 'exists:pets,id', $this->petAdoptable($adoption)],
            'person_id' => ['required', 'integer', 'exists:people,id'],
            'requested_at' => ['required', 'date', 'before_or_equal:today', ...(($approvedAt ?: $deliveredAt) ? ['before_or_equal:'.($approvedAt ?: $deliveredAt)] : [])],
            'approved_at' => ['nullable', 'date', 'before_or_equal:today', ...($requestedAt ? ['after_or_equal:'.$requestedAt] : []), ...($deliveredAt ? ['before_or_equal:'.$deliveredAt] : [])],
            'delivered_at' => ['nullable', 'date', 'before_or_equal:today', ...(($approvedAt ?: $requestedAt) ? ['after_or_equal:'.($approvedAt ?: $requestedAt)] : [])],
            'notes' => ['nullable', 'string'],
            'document' => ['nullable', 'string', 'max:255'],
            'adoption_status_id' => ['required', 'integer', 'exists:adoption_statuses,id', $this->statusReachable($request, $adoption)],
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * A request can only start on an eligible pet; an existing adoption keeps its pet.
     */
    private function petAdoptable(?Adoption $adoption): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($adoption) {
            if ((int) $value === $adoption?->pet_id) {
                return;
            }

            if (! Pet::query()->available()->whereKey($value)->exists()) {
                $fail('La mascota no es elegible para adopción.');
            } elseif ($adoption?->holds_pet && Adoption::query()->holding()->where('pet_id', $value)->exists()) {
                $fail('La mascota ya tiene otra adopción aprobada o entregada.');
            }
        };
    }

    /**
     * Same checks as the workflow; records that keep their status and pet are left as they are.
     */
    private function statusReachable(Request $request, ?Adoption $adoption): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($request, $adoption) {
            $petId = $request->integer('pet_id') ?: $adoption?->pet_id;
            $unchanged = (int) $value === $adoption?->adoption_status_id && $petId === $adoption?->pet_id;
            $status = AdoptionStatus::query()->whereKey($value)->value('name');

            if (! $unchanged && $status !== null && ($blocker = Adoption::blockerFor($status, $petId, $adoption?->getKey())) !== null) {
                $fail($blocker);
            }
        };
    }

    public function publicApply(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'pet_id' => ['required', 'integer', 'exists:pets,id', $this->petAdoptable(null)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'document_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($data, $request) {
            $defaultOccupationId = \App\Models\Occupation::query()->first()?->id ?? 1;
            $defaultResidenceId = \App\Models\Residence::query()->first()?->id ?? 1;
            $defaultUserId = $request->user()?->id ?? \App\Models\User::query()->first()?->id ?? 1;

            $person = \App\Models\Person::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'document_type' => 'DPI',
                    'document_number' => $data['document_number'] ?? ('DOC-' . rand(100000, 999999)),
                    'current_pets_count' => 0,
                    'estado_civil' => 'Soltero/a',
                    'cantidad_hijos' => 0,
                    'income' => 3000,
                    'occupation_id' => $defaultOccupationId,
                    'residence_id' => $defaultResidenceId,
                ]
            );

            $statusId = \App\Models\AdoptionStatus::query()->first()?->id ?? 1;

            $notes = ($data['notes'] ?? '');
            if (!empty($data['phone'])) {
                $notes .= ' [Teléfono: ' . $data['phone'] . ']';
            }

            $adoption = Adoption::query()->create([
                'pet_id' => $data['pet_id'],
                'person_id' => $person->id,
                'requested_at' => now(),
                'adoption_status_id' => $statusId,
                'user_id' => $defaultUserId,
                'notes' => $notes ?: null,
            ]);

            return response()->json([
                'message' => 'Solicitud de adopción recibida con éxito.',
                'adoption' => $adoption->load(['pet', 'person', 'adoptionStatus']),
            ], 201);
        });
    }

    public function publicMyApplications(Request $request): \Illuminate\Http\JsonResponse
    {
        $search = trim($request->string('search'));

        if (empty($search)) {
            return response()->json([]);
        }

        $adoptions = Adoption::query()
            ->with(['pet', 'person', 'adoptionStatus'])
            ->whereHas('person', function ($q) use ($search) {
                $q->where('email', $search)
                  ->orWhere('document_number', $search);
            })
            ->orderByDesc('id')
            ->get();

        return response()->json($adoptions);
    }
}

