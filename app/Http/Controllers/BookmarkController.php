<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $fileBookmarks = $user->fileBookmarks()->bookmarked()->get();
        $folderBookmarks = $user->folderBookmarks()->bookmarked()->get();

        return view('bookmarks.index', [
            'fileBookmarks' => $fileBookmarks,
            'folderBookmarks' => $folderBookmarks,
        ]);
    }
}
