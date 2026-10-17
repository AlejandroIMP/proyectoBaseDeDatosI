<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'species_id', 'breed_id', 'sex', 'birth_date', 'estimated_age', 'color_id', 'weight_kg', 'notes', 'pet_status_id', 'registered_at', 'status', 'shelter_id'])]
class Pet extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date:Y-m-d',
            'registered_at' => 'date:Y-m-d',
            'weight_kg' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Species, $this>
     */
    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    /**
     * @return BelongsTo<Breed, $this>
     */
    public function breed(): BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }

    /**
     * @return BelongsTo<Color, $this>
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    /**
     * @return BelongsTo<PetStatus, $this>
     */
    public function petStatus(): BelongsTo
    {
        return $this->belongsTo(PetStatus::class);
    }

    /**
     * @return BelongsTo<Shelter, $this>
     */
    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

    /**
     * @return HasMany<PetHistory, $this>
     */
    public function histories(): HasMany
    {
        return $this->hasMany(PetHistory::class);
    }

    /**
     * @return HasMany<PetTreatment, $this>
     */
    public function petTreatments(): HasMany
    {
        return $this->hasMany(PetTreatment::class);
    }

    /**
     * @return HasMany<Appointment, $this>
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * @return HasMany<Donation, $this>
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * @return HasMany<Rescue, $this>
     */
    public function rescues(): HasMany
    {
        return $this->hasMany(Rescue::class);
    }

    /**
     * @return HasMany<Adoption, $this>
     */
    public function adoptions(): HasMany
    {
        return $this->hasMany(Adoption::class);
    }

    /**
     * Active pets whose status is the one offered to adopters.
     *
     * @param  Builder<Pet>  $query
     */
    public function scopeAvailable(Builder $query): void
    {
        $query->where('status', 'active')
            ->whereHas('petStatus', fn (Builder $query) => $query->where('name', PetStatus::AVAILABLE));
    }

    /**
     * Human readable age from the birth date, e.g. "2 años" or "5 meses".
     */
    public function ageLabel(): string
    {
        $months = (int) $this->birth_date->diffInMonths(now(), absolute: true);

        if ($months >= 12) {
            $years = intdiv($months, 12);

            return $years.' '.($years === 1 ? 'año' : 'años');
        }

        return $months.' '.($months === 1 ? 'mes' : 'meses');
    }
}
