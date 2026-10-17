<?php

namespace App\Http\Controllers\Api;

use App\Models\Adoption;
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
        return ['user_id' => $request->user()?->id];
    }

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'pet_id' => ['required', 'integer', 'exists:pets,id'],
            'person_id' => ['required', 'integer', 'exists:people,id'],
            'requested_at' => ['required', 'date'],
            'approved_at' => ['nullable', 'date'],
            'delivered_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'document' => ['nullable', 'string', 'max:255'],
            'adoption_status_id' => ['required', 'integer', 'exists:adoption_statuses,id'],
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
        ];
    }

    public function publicApply(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'pet_id' => ['required', 'integer', 'exists:pets,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'document_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

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
            'notes' => $notes ?: null,
        ]);

        return response()->json([
            'message' => 'Solicitud de adopción recibida con éxito.',
            'adoption' => $adoption->load(['pet', 'person', 'adoptionStatus']),
        ], 201);
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

