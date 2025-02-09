<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileFolderSearchRequest;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Support\Facades\Auth;

class FileFolderSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FileFolderSearchRequest $request)
    {
        $user = Auth::user();
        $query = $request->input('query');

        $folders = Folder::where('name', 'like', '%'.$query.'%')->get()->filter(function ($folder) use ($user) {
            return $user->can('view', $folder);
        });

        $files = File::where('name', 'like', '%'.$query.'%')->get()->filter(function ($file) use ($user) {
            return $user->can('view', $file);
        });

        return view('search.index', compact('folders', 'files', 'query'));
    }
}
