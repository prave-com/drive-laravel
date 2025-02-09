<?php

namespace App\Listeners;

use App\Models\Folder;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Storage;

class CreateRootFolderOnEmailVerified
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
        $path = 'drive'.'/'.$event->user->id;
        if (! Storage::disk('local')->exists($path)) {
            Storage::makeDirectory($path);
        }

        if (! Folder::find($event->user->id)) {
            Folder::create([
                'name' => $event->user->id,
                'user_id' => $event->user->id,
            ]);
        }
    }
}
