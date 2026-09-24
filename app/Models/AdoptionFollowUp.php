<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['adoption_id', 'followed_at', 'notes', 'user_id'])]
class AdoptionFollowUp extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'followed_at' => 'date:Y-m-d',
        ];
    }

    /**
     * @return BelongsTo<Adoption, $this>
     */
    public function adoption(): BelongsTo
    {
        return $this->belongsTo(Adoption::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
