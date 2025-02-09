<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Enums\UserRole;
use App\Http\Requests\FilterFilesRequest;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFilePermissionRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Models\File;
use App\Models\Folder;
use App\Rules\UniqueFileName;
use App\Rules\ValidFileName;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FilterFilesRequest $request)
    {
        $user = Auth::user();

        if ($user->role !== UserRole::USER) {
            return redirect()->route('activity-logs.index');
        }

        $filter = $request->input('filter', 'latest');

        // Get the current folder location (adjust this as per your folder structure)
        $currentLocation = 'Berkas Saya'; // Default root folder

        // If files are in specific folders, we should pass the folder location as well
        if ($filter === 'shared') {
            $files = File::whereHas('userFileAccesses', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->latest()->paginate(50);
        } else {
            $files = $user->files()->latest()->paginate(50);
        }

        // Now, add the currentLocation to be passed to the view
        return view('dashboard', compact('files', 'filter', 'currentLocation'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFileRequest $request, Folder $folder)
    {
        $user = Auth::user();
        $files = $request->file('files');
        $errors = [];

        // Validate each file using the custom rules
        foreach ($files as $index => $file) {
            $validator = Validator::make(['file' => $file], [
                'file' => [
                    'required',
                    'file',
                    new ValidFileName,
                    new UniqueFileName($folder),
                ],
            ]);

            // Handle validation failure
            if ($validator->fails()) {
                // Add errors for the specific file to the errors array
                foreach ($validator->errors()->get('file') as $message) {
                    $errors["files.$index"][] = $message;
                }
            }
        }

        // Handle validation errors
        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        // Save the files if valid
        foreach ($files as $file) {
            $fileName = $file->getClientOriginalName();

            $newFile = File::create([
                'name' => $fileName,
                'folder_id' => $folder->id,
                'user_id' => $user->id,
            ]);

            activity()
                ->causedBy($user)
                ->performedOn($newFile)
                ->event('created')
                ->log("Upload dokumen {$newFile->name}");

            $file->storeAs('drive'.$folder->full_path, $fileName);
        }

        return redirect()->route('folders.show', $folder)->with('success', 'Files uploaded successfully.');
    }

    /**
     * Update permission of file
     */
    public function grantPermission(UpdateFilePermissionRequest $request, File $file)
    {
        $permissionType = $request->enum('permission_type', PermissionType::class);
        $file->update([
            'permission_type' => $permissionType,
        ]);

        if ($permissionType) {
            activity()
                ->causedBy(Auth::user())
                ->performedOn($file)
                ->withProperties(['permission_type' => $permissionType])
                ->event('updated')
                ->log("Grant semua pengguna permission {$permissionType->name} untuk dokumen {$file->name}");
        } else {
            activity()
                ->causedBy(Auth::user())
                ->performedOn($file)
                ->event('updated')
                ->log("Delete permission semua pengguna untuk dokumen {$file->name}");
        }

        return back()->with('success', 'Permission updated successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFileRequest $request, File $file)
    {
        $oldFilename = $file->name;
        $oldFileFullPath = $file->full_path;

        $file->update([
            'name' => $request->name,
        ]);

        Storage::move('drive'.$oldFileFullPath, 'drive'.$file->full_path);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($file)
            ->event('updated')
            ->log("Rename dokumen {$oldFilename} menjadi {$file->name}");

        return back()->with('success', 'File updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(File $file)
    {
        $user = Auth::user();

        if ($user->cannot('delete', $file)) {
            abort(403);
        }

        $parentFolder = $file->parent;
        $file->delete();

        activity()
            ->causedBy($user)
            ->performedOn($file)
            ->event('deleted')
            ->log("Delete dokumen {$file->name} ke tempat sampah");

        return redirect()->route('folders.show', $parentFolder)->with('success', 'File deleted successfully.');
    }

    /**
     * Download the specified resource.
     */
    public function download(File $file): StreamedResponse
    {
        $user = Auth::user();

        if ($user->cannot('view', $file)) {
            abort(403);
        }

        $path = 'drive'.$file->full_path;

        if (Storage::disk('local')->exists($path)) {
            $headers = [
                'Content-Type' => Storage::disk('local')->mimeType($path),
            ];

            activity()
                ->causedBy($user)
                ->performedOn($file)
                ->log("Download dokumen {$file->name}");

            return Storage::download($path, $file->name, $headers);
        }
    }
}
