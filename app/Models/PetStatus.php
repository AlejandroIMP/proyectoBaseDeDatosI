<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class PetStatus extends Model
{
    /**
     * @return HasMany<Pet, $this>
     */
    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }

    /**
     * @return HasMany<PetHistory, $this>
     */
    public function histories(): HasMany
    {
        return $this->hasMany(PetHistory::class);
    }

    /**
     * @return HasMany<Rescue, $this>
     */
    public function rescues(): HasMany
    {
        return $this->hasMany(Rescue::class);
    }
}
