<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['pet_id', 'person_id', 'requested_at', 'approved_at', 'delivered_at', 'notes', 'document', 'adoption_status_id', 'user_id'])]
class Adoption extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'requested_at' => 'date:Y-m-d',
            'approved_at' => 'date:Y-m-d',
            'delivered_at' => 'date:Y-m-d',
        ];
    }

    /**
     * @return BelongsTo<Pet, $this>
     */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    /**
     * @return BelongsTo<Person, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @return BelongsTo<AdoptionStatus, $this>
     */
    public function adoptionStatus(): BelongsTo
    {
        return $this->belongsTo(AdoptionStatus::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<AdoptionFollowUp, $this>
     */
    public function followUps(): HasMany
    {
        return $this->hasMany(AdoptionFollowUp::class);
    }

    /**
     * @return HasMany<Contract, $this>
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
