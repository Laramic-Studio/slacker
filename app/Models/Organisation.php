<?php

namespace App\Models;

use App\Enums\Enums\OrganisactionVisibilityEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Organisation
 *
 * @property string $id
 * @property string $name
 * @property bool $is_private
 * @property string|null $description
 * @property string|null $logo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class Organisation extends Model
{
    use HasUlids;

    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class);
    }

      protected function casts(): array
    {
        return [
            'visibility' => OrganisactionVisibilityEnum::class,
        ];
    }
}
