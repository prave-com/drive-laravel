<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\ForceDeleteFileRequest;
use App\Http\Requests\ForceDeleteFolderRequest;
use App\Http\Requests\RestoreFileRequest;
use App\Http\Requests\RestoreFolderRequest;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TrashController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search'); // Get the search query from the request

        $isSuperAdmin = $user->role === UserRole::SUPERADMIN;

        // Superadmin: Fetch soft-deleted folders and files and apply search conditions
        if ($isSuperAdmin) {
            $deletedFolders = Folder::onlyTrashed()
                ->where(function ($query) use ($search) {
                    // Only apply search if there is a search term
                    if ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhereHas('owner', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                    }
                })
                ->latest()
                ->paginate(50);

            $deletedFiles = File::onlyTrashed()
                ->where(function ($query) use ($search) {
                    if ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhereHas('owner', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                    }
                })
                ->latest()
                ->paginate(50);
        } else {
            // Non-superadmin: Fetch soft-deleted folders and files and apply search conditions
            $deletedFolders = $user->folders()
                ->onlyTrashed()
                ->where(function ($query) use ($search) {
                    if ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhereHas('owner', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                    }
                })
                ->latest()
                ->get();

            $deletedFiles = $user->files()
                ->onlyTrashed()
                ->where(function ($query) use ($search) {
                    if ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhereHas('owner', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                    }
                })
                ->latest()
                ->get();
        }

        return view('trash.index', [
            'deletedFolders' => $deletedFolders,
            'deletedFiles' => $deletedFiles,
            'isSuperAdmin' => $isSuperAdmin,
            'search' => $search, // Pass the search term to the view
        ]);
    }

    public function restoreFolder(RestoreFolderRequest $request, Folder $folder)
    {
        $folder->restore();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($folder)
            ->event('restored')
            ->log("Restore folder {$folder->name} dari tempat sampah");

        return back()->with('success', 'Folder restored successfully.');
    }

    public function restoreFile(RestoreFileRequest $request, File $file)
    {
        $file->restore();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($file)
            ->event('restored')
            ->log("Restore file {$file->name} dari tempat sampah");

        return back()->with('success', 'File restored successfully.');
    }

    public function forceDeleteFolder(ForceDeleteFolderRequest $request, Folder $folder)
    {
        Storage::deleteDirectory('drive'.$folder->full_path);

        $folder->forceDelete();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($folder)
            ->event('deleted')
            ->log("Delete permanen folder {$folder->name} dari tempat sampah");

        return back()->with('success', 'Folder permanently deleted.');
    }

    public function forceDeleteFile(ForceDeleteFileRequest $request, File $file)
    {
        Storage::delete('drive'.$file->full_path);

        $file->forceDelete();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($file)
            ->event('deleted')
            ->log("Delete permanen file {$file->name} dari tempat sampah");

        return back()->with('success', 'File permanently deleted.');
    }
}
