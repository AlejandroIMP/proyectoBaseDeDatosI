<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class AdoptionStatus extends Model
{
    public const PENDING = 'Pendiente';

    public const CANCELLED = 'Cancelada';

    /** Statuses of a request that is still in progress: an adopter may only have one of these per pet. */
    public const OPEN = [self::PENDING, 'En revisión', 'Aprobada'];

    /**
     * @return HasMany<Adoption, $this>
     */
    public function adoptions(): HasMany
    {
        return $this->hasMany(Adoption::class);
    }
}
