<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class AdoptionStatus extends Model
{
    public const PENDING = 'Pendiente';

    public const IN_REVIEW = 'En revisión';

    public const APPROVED = 'Aprobada';

    public const REJECTED = 'Rechazada';

    public const DELIVERED = 'Entregada';

    public const CANCELLED = 'Cancelada';

    /** Statuses of a request that is still in progress: an adopter may only have one of these per pet. */
    public const OPEN = [self::PENDING, self::IN_REVIEW, self::APPROVED];

    /** Statuses that hold the pet for one adopter: a pet can only have one adoption in these. */
    public const HOLDING = [self::APPROVED, self::DELIVERED];

    /**
     * Steps the staff can take from each status, as [next status => button label].
     *
     * @var array<string, array<string, string>>
     */
    public const TRANSITIONS = [
        self::PENDING => [self::IN_REVIEW => 'Revisar', self::APPROVED => 'Aprobar', self::REJECTED => 'Rechazar'],
        self::IN_REVIEW => [self::APPROVED => 'Aprobar', self::REJECTED => 'Rechazar'],
        self::APPROVED => [self::DELIVERED => 'Entregar', self::REJECTED => 'Rechazar'],
    ];

    /**
     * @return HasMany<Adoption, $this>
     */
    public function adoptions(): HasMany
    {
        return $this->hasMany(Adoption::class);
    }
}
