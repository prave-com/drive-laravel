<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Enums\UserRole;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\UpdateFolderPermissionRequest;
use App\Http\Requests\UpdateFolderRequest;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use STS\ZipStream\Builder;
use STS\ZipStream\Facades\Zip;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search'); // Capture the search query

        if ($user->role === UserRole::SUPERADMIN) {
            $folders = Folder::query()
                ->when($search, function ($query, $search) {
                    return $query->whereHas('owner', function ($query) use ($search) {
                        $query->where('email', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
                })
                ->root()
                ->get();
        } else {
            return redirect()->route('folders.show', ['folder' => $user->folders()->root()->first()]);
        }

        // Determine the current folder location
        $currentLocation = 'Berkas Saya'; // Default root folder
        // Assuming you have a way to get the folder path
        if ($folders->isNotEmpty()) {
            $currentLocation = $this->getFolderPath($folders->first()); // Get the folder path for the first folder
        }

        return view('folders.index', [
            'folders' => $folders,
            'search' => $search,
            'currentLocation' => $currentLocation, // Pass the folder location
        ]);
    }

    public function getFolderPath($folder)
    {
        // Construct folder path (adjust this to your actual folder hierarchy)
        return $folder->is_root ? 'Berkas Saya' : 'Berkas Saya / '.$folder->name;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFolderRequest $request, Folder $folder)
    {
        $user = Auth::user();
        $newFolder = Folder::create([
            'name' => $request->name,
            'folder_id' => $folder->id,
            'user_id' => $user->id,
        ]);

        $path = 'drive'.$newFolder->full_path;
        Storage::makeDirectory($path);

        activity()
            ->causedBy($user)
            ->performedOn($newFolder)
            ->event('created')
            ->log("Create folder {$newFolder->name}");

        return redirect()->route('folders.show', $folder)->with('success', 'Folder created successfully.');
    }

    /**
     * Update permission of folder
     */
    public function grantPermission(UpdateFolderPermissionRequest $request, Folder $folder)
    {
        $permissionType = $request->enum('permission_type', PermissionType::class);
        $folder->update([
            'permission_type' => $permissionType,
        ]);

        if ($permissionType) {
            activity()
                ->causedBy(Auth::user())
                ->performedOn($folder)
                ->withProperties(['permission_type' => $permissionType])
                ->event('updated')
                ->log("Grant semua pengguna permission {$permissionType->name} untuk folder {$folder->name}");
        } else {
            activity()
                ->causedBy(Auth::user())
                ->performedOn($folder)
                ->event('updated')
                ->log("Delete permission semua pengguna untuk folder {$folder->name}");
        }

        return back()->with('success', 'Permission updated successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Folder $folder)
    {
        $user = Auth::user();

        if ($user->cannot('view', $folder)) {
            abort(403);
        }

        $folder->load([
            'children' => function ($query) {
                $query->orderBy('name')->with('folderBookmarks');
            },
            'files' => function ($query) {
                $query->orderBy('name')->with('fileBookmarks');
            },
        ]);

        return view('folders.show', [
            'folder' => $folder,
            'parents' => $folder->getAllParents(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFolderRequest $request, Folder $folder)
    {
        $oldFolderName = $folder->name;
        $oldFolderFullPath = $folder->full_path;

        $folder->update([
            'name' => $request->name,
        ]);

        Storage::move('drive'.$oldFolderFullPath, 'drive'.$folder->full_path);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($folder)
            ->event('updated')
            ->log("Rename folder {$oldFolderName} menjadi {$folder->name}");

        return back()->with('success', 'Folder updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Folder $folder)
    {
        $user = Auth::user();

        if ($user->cannot('delete', $folder)) {
            abort(403);
        }

        $parentFolder = $folder->parent;
        $folder->delete();

        activity()
            ->causedBy($user)
            ->performedOn($folder)
            ->event('deleted')
            ->log("Delete folder {$folder->name} ke tempat sampah");

        return redirect()->route('folders.show', $parentFolder)->with('success', 'Folder deleted successfully.');
    }

    public function download(Folder $folder)
    {
        $user = Auth::user();

        if ($user->cannot('view', $folder)) {
            abort(403);
        }

        $zip = Zip::create($folder->name.'.zip');

        $this->addFolderToZip($zip, $folder);

        activity()
            ->causedBy($user)
            ->performedOn($folder)
            ->log("Download folder {$folder->name}");

        return $zip;
    }

    private function addFolderToZip(Builder $zip, Folder $folder, string $basePath = ''): void
    {
        foreach ($folder->files as $file) {
            $zipPath = $basePath.$folder->name.'/'.$file->name;

            $zip->addFromDisk('local', 'drive'.$file->full_path, $zipPath);
        }

        foreach ($folder->children as $childFolder) {
            $this->addFolderToZip($zip, $childFolder, $basePath.$folder->name.'/');
        }
    }
}
