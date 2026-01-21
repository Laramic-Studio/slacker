<?php

namespace App\Enums\Enums;

enum OrganisactionVisibilityEnum: string
{
    case PUBLIC =  'public';
    case PRIVATE = 'private';

    public function label(): string
    {
        return  match ($this) {
            Self::PUBLIC => 'Public',
            Self::PRIVATE => 'Private',
        };
    }
}
