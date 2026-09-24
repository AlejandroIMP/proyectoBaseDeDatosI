<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'requires_veterinarian', 'cost', 'treatment_type_id'])]
class Treatment extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'requires_veterinarian' => 'boolean',
            'cost' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<TreatmentType, $this>
     */
    public function treatmentType(): BelongsTo
    {
        return $this->belongsTo(TreatmentType::class);
    }

    /**
     * @return HasMany<PetTreatment, $this>
     */
    public function petTreatments(): HasMany
    {
        return $this->hasMany(PetTreatment::class);
    }
}
