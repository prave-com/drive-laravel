<?php

namespace App\Listeners;

use App\Models\Storage;
use Illuminate\Auth\Events\Verified;

class CreateStorageOnEmailVerified
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        if (! Storage::find($event->user->id)) {
            $totalStorage = (int) env('TOTAL_STORAGE');
            $usedQuota = Storage::all()->sum('total_quota');
            $remainingQuota = $totalStorage - $usedQuota;
            $quotaToAllocate = max(0, min(3221225472, $remainingQuota));

            Storage::create([
                'total_quota' => $quotaToAllocate,
                'user_id' => $event->user->id,
            ]);
        }
    }
}
