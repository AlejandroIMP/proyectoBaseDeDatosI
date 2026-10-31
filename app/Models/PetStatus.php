<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'is_adoptable'])]
class PetStatus extends Model
{
    /** Status a released pet returns to; which statuses are offered to adopters is set by is_adoptable. */
    public const AVAILABLE = 'Disponible';

    public const RESERVED = 'Reservado';

    public const ADOPTED = 'Adoptado';

    public const DECEASED = 'Fallecido';

    /** Statuses only the adoption workflow can assign. */
    public const WORKFLOW = [self::RESERVED, self::ADOPTED];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['is_adoptable' => 'boolean'];
    }

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
