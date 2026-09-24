<?php

namespace App\Http\Controllers\Api;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class DonationController extends ResourceController
{
    /** @var class-string<Donation> */
    protected string $model = Donation::class;

    /** @var list<string> */
    protected array $with = ['donor', 'pet'];

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'donor_id' => ['required', 'integer', 'exists:donors,id'],
            'amount' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'contribution_type' => ['required', 'in:cash,transfer,in_kind'],
            'pet_id' => ['nullable', 'integer', 'exists:pets,id'],
        ];
    }
}
