<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookmarkFolderRequest;
use App\Models\Folder;
use App\Models\FolderBookmark;
use Illuminate\Support\Facades\Auth;

class FolderBookmarkController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(BookmarkFolderRequest $request, Folder $folder)
    {
        $user = Auth::user();

        FolderBookmark::upsert([
            ['is_starred' => $request->is_starred, 'folder_id' => $folder->id, 'user_id' => $user->id],
        ], uniqueBy: ['folder_id', 'user_id'], update: ['is_starred']);

        if ($request->is_starred) {
            activity()
                ->causedBy($user)
                ->performedOn($folder)
                ->event('updated')
                ->log("Bookmark folder {$folder->name}");

            return back()->with('success', 'Folder bookmarked successfully.');
        } else {
            activity()
                ->causedBy($user)
                ->performedOn($folder)
                ->event('updated')
                ->log("Unbookmark folder {$folder->name}");

            return back()->with('success', 'Folder unbookmarked successfully.');
        }
    }
}
