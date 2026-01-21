<?php

namespace App\Observers;

use App\Enums\Enums\OrganisactionVisibilityEnum;
use App\Models\Organisation;

class OrganisactionObserver
{
    /**
     * Handle the Organisation "created" event.
     */
    public function created(Organisation $organisation): void
    {
        $organisation->channels()->create([
            'visibility' => OrganisactionVisibilityEnum::PUBLIC->value,
            'name' => 'General',
        ]);
        $organisation->channels()->create([
            'visibility' => OrganisactionVisibilityEnum::PUBLIC->value,
            'name' => 'Town Hall',
        ]);
    }
 
    public function updated(Organisation $organisation): void
    {
        //
    }

    /**
     * Handle the Organisation "deleted" event.
     */
    public function deleted(Organisation $organisation): void
    {
        //
    }

    /**
     * Handle the Organisation "restored" event.
     */
    public function restored(Organisation $organisation): void
    {
        //
    }

    /**
     * Handle the Organisation "force deleted" event.
     */
    public function forceDeleted(Organisation $organisation): void
    {
        //
    }
}
