<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'document_type', 'document_number', 'email', 'current_pets_count', 'estado_civil', 'cantidad_hijos', 'occupation_id', 'residence_id', 'income'])]
class Person extends Model
{
    /**
     * @return BelongsTo<Occupation, $this>
     */
    public function occupation(): BelongsTo
    {
        return $this->belongsTo(Occupation::class);
    }

    /**
     * @return BelongsTo<Residence, $this>
     */
    public function residence(): BelongsTo
    {
        return $this->belongsTo(Residence::class);
    }

    /**
     * @return HasMany<Adoption, $this>
     */
    public function adoptions(): HasMany
    {
        return $this->hasMany(Adoption::class);
    }

    /**
     * @return HasMany<Appointment, $this>
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * @return HasMany<Donor, $this>
     */
    public function donors(): HasMany
    {
        return $this->hasMany(Donor::class);
    }
}
