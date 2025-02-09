<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookmarkFileRequest;
use App\Models\File;
use App\Models\FileBookmark;
use Illuminate\Support\Facades\Auth;

class FileBookmarkController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(BookmarkFileRequest $request, File $file)
    {
        $user = Auth::user();

        FileBookmark::upsert([
            ['is_starred' => $request->is_starred, 'file_id' => $file->id, 'user_id' => $user->id],
        ], uniqueBy: ['file_id', 'user_id'], update: ['is_starred']);

        if ($request->is_starred) {
            activity()
                ->causedBy($user)
                ->performedOn($file)
                ->event('updated')
                ->log("Bookmark dokumen {$file->name}");

            return back()->with('success', 'File bookmarked successfully.');
        } else {
            activity()
                ->causedBy($user)
                ->performedOn($file)
                ->event('updated')
                ->log("Unbookmark dokumen {$file->name}");

            return back()->with('success', 'File unbookmarked successfully.');
        }
    }
}
