<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\FileBookmarkController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FileFolderSearchController;
use App\Http\Controllers\FolderBookmarkController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StorageRequestController;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\UserFileAccessController;
use App\Http\Controllers\UserFolderAccessController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [FileController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/avatar/{filename}', [ProfileController::class, 'showAvatar'])->name('profile.avatar.show');
});

Route::middleware(['auth', 'verified', 'can:manage-files-folders'])->group(function () {
    Route::get('/search', [FileFolderSearchController::class, 'index'])->name('file-folder-search.index');
});

Route::middleware(['auth', 'verified', 'can:manage-files-folders'])->group(function () {
    Route::get('/folders', [FolderController::class, 'index'])->name('folders.index');
    Route::get('/folders/{folder}', [FolderController::class, 'show'])->whereUuid('folder')->name('folders.show');
    Route::get('/folders/{folder}/download', [FolderController::class, 'download'])->whereUuid('folder')->name('folders.download');
    Route::post('/folders/{folder}', [FolderController::class, 'store'])->whereUuid('folder')->name('folders.store');
    Route::patch('/folders/{folder}', [FolderController::class, 'update'])->whereUuid('folder')->name('folders.update');
    Route::delete('/folders/{folder}', [FolderController::class, 'destroy'])->whereUuid('folder')->name('folders.destroy');
    Route::post('folders/{folder}/grant-permission', [FolderController::class, 'grantPermission'])->whereUuid('folder')->name('folders.grantPermission');
});

Route::middleware(['auth', 'verified', 'can:manage-files-folders'])->group(function () {
    Route::post('folder-accesses/{folder}', [UserFolderAccessController::class, 'store'])->whereUuid('folder')->name('folder-accesses.store');
    Route::patch('folder-accesses/{userFolderAccess}', [UserFolderAccessController::class, 'update'])->whereNumber('userFolderAccess')->name('folder-accesses.update');
    Route::delete('folder-accesses/{userFolderAccess}', [UserFolderAccessController::class, 'destroy'])->whereNumber('userFolderAccess')->name('folder-accesses.destroy');
});

Route::middleware(['auth', 'verified', 'can:manage-files-folders'])->group(function () {
    Route::post('/folders/{folder}/files', [FileController::class, 'store'])->whereUuid('folder')->name('folders.files.store');
    Route::get('/files/{file}/download', [FileController::class, 'download'])->whereUuid('file')->name('files.download');
    Route::patch('/files/{file}', [FileController::class, 'update'])->whereUuid('file')->name('files.update');
    Route::delete('/files/{file}', [FileController::class, 'destroy'])->whereUuid('file')->name('files.destroy');
    Route::post('/files/{file}/grant-permission', [FileController::class, 'grantPermission'])->whereUuid('file')->name('files.grantPermission');
});

Route::middleware(['auth', 'verified', 'can:manage-files-folders'])->group(function () {
    Route::post('file-accesses/{file}', [UserFileAccessController::class, 'store'])->whereUuid('file')->name('file-accesses.store');
    Route::patch('file-accesses/{userFileAccess}', [UserFileAccessController::class, 'update'])->whereNumber('userFileAccess')->name('file-accesses.update');
    Route::delete('file-accesses/{userFileAccess}', [UserFileAccessController::class, 'destroy'])->whereNumber('userFileAccess')->name('file-accesses.destroy');
});

Route::middleware(['auth', 'verified', 'can:view-bookmarks'])->group(function () {
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
});

Route::middleware(['auth', 'verified', 'can:manage-files-folders'])->group(function () {
    Route::post('/folder-bookmarks/{folder}', [FolderBookmarkController::class, 'store'])->whereUuid('folder')->name('folder-bookmarks.store');
});

Route::middleware(['auth', 'verified', 'can:manage-files-folders'])->group(function () {
    Route::post('/file-bookmarks/{file}', [FileBookmarkController::class, 'store'])->whereUuid('file')->name('file-bookmarks.store');
});

Route::middleware(['auth', 'verified', 'can:manage-files-folders'])->group(function () {
    Route::get('/trash', [TrashController::class, 'index'])->name('trash.index');
    Route::post('/trash/folders/{folder}/restore', [TrashController::class, 'restoreFolder'])->whereUuid('folder')->withTrashed()->name('trash.folders.restore');
    Route::post('/trash/files/{file}/restore', [TrashController::class, 'restoreFile'])->whereUuid('file')->withTrashed()->name('trash.files.restore');
    Route::delete('/trash/folders/{folder}/force-delete', [TrashController::class, 'forceDeleteFolder'])->whereUuid('folder')->withTrashed()->name('trash.folders.force-delete');
    Route::delete('/trash/files/{file}/force-delete', [TrashController::class, 'forceDeleteFile'])->whereUuid('file')->withTrashed()->name('trash.files.force-delete');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/storage-requests', [StorageRequestController::class, 'index'])->name('storage-requests.index');
    Route::get('/storage-requests/create', [StorageRequestController::class, 'create'])->middleware('can:create,App\Models\StorageRequest')->name('storage-requests.create');
    Route::post('/storage-requests', [StorageRequestController::class, 'store'])->name('storage-requests.store');
    Route::put('/storage-requests/{storageRequest}', [StorageRequestController::class, 'update'])->whereNumber('storageRequest')->name('storage-requests.update');
});

Route::middleware(['auth', 'verified', 'can:manage-users'])->name('superadmin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->whereNumber('user')->name('users.edit');
    Route::patch('/users/{user}', [UserController::class, 'update'])->whereNumber('user')->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->whereNumber('user')->name('users.destroy');
});

Route::middleware(['auth', 'verified', 'can:view-logs'])->group(function () {
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

require __DIR__.'/auth.php';
