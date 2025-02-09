@extends('layouts.app')

@section('content')

    <!-- Project Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-dark">Folder Saya</h6>

            @can('modifyContent', $folder)
                <button class="btn btn-outline-dark d-flex align-items-center" data-bs-toggle="modal"
                    data-bs-target="#createFolderModal">
                    <i class="bx bx-plus-circle me-2"></i> Folder Baru
                </button>
                <!-- Modal for Creating New Folder -->
                <div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content" style="border-radius: 10px; padding: 20px;">
                            <!-- Modal Header -->
                            <div class="modal-header" style="border-bottom: none;">
                                <h5 class="modal-title" id="createFolderModalLabel" style="font-size: 24px; font-weight: bold;">
                                    Buat Folder Baru</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body">
                                <form id="createFolderForm" action="{{ route('folders.store', $folder) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama Folder</label>
                                        <input type="text" name="name" id="name" required class="form-control"
                                            placeholder="Masukkan nama folder">
                                        <x-input-error :messages="$errors->folder_creation->get('name')" class="mt-2" />
                                    </div>
                                </form>
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer"
                                style="display: flex; justify-content: space-between; align-items: center;">
                                <!-- Create Button -->
                                <button type="submit" class="btn btn-primary" form="createFolderForm">Buat Folder</button>
                                <!-- Cancel Button -->
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-2 m-0">
                @if ($parents)
                    <li class="breadcrumb-item">
                        <a href="{{ route('folders.show', $parents[0]) }}"
                            class="text-secondary text-decoration-none">..</a>
                    </li>
                @endif

                @foreach (array_slice($parents, 1) as $parent)
                    <li class="breadcrumb-item">
                        <a href="{{ route('folders.show', $parent) }}" class="text-secondary text-decoration-none">
                            {{ $parent->name }}
                        </a>
                    </li>
                @endforeach

                @if (!$folder->is_root)
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $folder->name }}
                    </li>
                @endif
            </ol>
        </nav>

        <div class="folder-container d-flex gap-3 overflow-auto p-2" style="white-space: nowrap; cursor: pointer">
            @if ($folder->children)
                @foreach ($folder->children as $child)
                    <div class="folder-item cursor-pointer" data-url="{{ route('folders.show', $child) }}"
                        style="min-width: 200px; flex-shrink: 0;">
                        <div class="card shadow-sm folder-file-card"
                            style="height: 240px; position: relative; transition: transform 0.2s, box-shadow 0.2s; border-radius: 8px; min-width: 150px; text-align: center;">
                            <div
                                class="card-body text-center d-flex flex-column justify-content-between align-items-center">
                                <!-- Folder Icon -->
                                <div class="d-flex justify-content-center align-items-center mb-2" style="flex: 1;">
                                    <i class="fas fa-folder fa-3x text-warning"></i>
                                </div>

                                <!-- Folder Name -->
                                <h6 class="mt-2 text-center text-truncate"
                                    style="max-width: 50px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $child->name }}
                                </h6>

                                <!-- Folder Options (Dropdown) -->
                                <div class="file-options" style="position: absolute; top: 10px; right: 10px;">
                                    <button class="btn btn-light btn-sm action-menu">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu"
                                        style="position: absolute; top: 20px; right: 0; z-index: 1050;">

                                        <!-- Rename Folder Option -->
                                        @can('update', $child)
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#renameFolderModal{{ $child->id }}">
                                                <i class="fas fa-file-pen me-2"></i>Ganti Nama
                                            </a>
                                        @endcan

                                        <!-- Share Folder Option -->
                                        @can('grantAccess', $child)
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#shareModalFolder{{ $child->id }}">
                                                <i class="fas fa-share-nodes me-2"></i>Bagikan
                                            </a>
                                        @endcan

                                        @can('view', $child)
                                            <a href="{{ route('folders.download', $child) }}" class="dropdown-item">
                                                <i class="fa-solid fa-file-zipper"></i>Download
                                            </a>
                                        @endcan

                                        <!-- Bookmark Option -->
                                        @can('view', $child)
                                            <form action="{{ route('folder-bookmarks.store', $child) }}" method="POST"
                                                class="m-0">
                                                @csrf
                                                <input type="hidden" name="is_starred"
                                                    value="{{ $child->folderBookmarks()->where('user_id', Auth::id())->exists() && $child->folderBookmarks()->firstWhere('user_id', Auth::id())->is_starred ? '0' : '1' }}">
                                                <button type="submit" class="dropdown-item">
                                                    <i
                                                        class="fa-solid fa-bookmark"></i>{{ $child->folderBookmarks()->where('user_id', Auth::id())->exists() && $child->folderBookmarks()->firstWhere('user_id', Auth::id())->is_starred ? 'Unbookmark' : 'Bookmark' }}
                                                </button>
                                            </form>
                                        @endcan

                                        @can('delete', $child)
                                            <form action="{{ route('folders.destroy', $child) }}" method="POST"
                                                class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fa-solid fa-trash"></i>Hapus
                                                </button>
                                            </form>
                                        @endcan
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal untuk Rename Folder -->
                    @can('update', $child)
                        <div class="modal fade" id="renameFolderModal{{ $child->id }}" tabindex="-1"
                            aria-labelledby="renameModalLabel{{ $child->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content" style="border-radius: 10px; padding: 20px;">
                                    <div class="modal-header" style="border-bottom: none;">
                                        <h5 class="modal-title" id="renameModalLabel{{ $child->id }}"
                                            style="font-size: 24px; font-weight: bold;">Ganti Nama Folder</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Rename Form inside Modal -->
                                        <form id="renameForm{{ $child->id }}"
                                            action="{{ route('folders.update', $child) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div>
                                                <label for="folder-name">Nama Folder</label>
                                                <input type="text" name="name" id="folder-name"
                                                    value="{{ $child->name }}" required class="form-control">
                                                <x-input-error :messages="$errors->getBag('rename_folder_' . $child->id)->get('name')" class="mt-2" />
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <!-- Save Button -->
                                        <button type="submit" class="btn btn-primary"
                                            form="renameForm{{ $child->id }}">Simpan</button>
                                        <!-- Cancel Button -->
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan

                    <!-- Modal Share Folder -->
                    @can('grantAccess', $child)
                        <div class="modal fade" id="shareModalFolder{{ $child->id }}" tabindex="-1"
                            aria-labelledby="shareModalFolderLabel{{ $child->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="shareModalFolderLabel{{ $child->id }}">Bagikan Folder
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="container mt-4">
                                            <!-- Nama Folder -->
                                            <div class="folder-name mb-3">
                                                <strong> {{ $child->name }}</strong>
                                            </div>
                                            <!-- Grant Access to Specific User -->
                                            <form action="{{ route('folder-accesses.store', $child) }}" method="POST"
                                                class="mb-4">
                                                @csrf
                                                <div class="mb-3 d-flex">
                                                    <div class="me-3" style="flex-grow: 1;">
                                                        <input type="email" name="email" id="email"
                                                            class="form-control" placeholder="User Email"
                                                            value="{{ old('email') }}"
                                                            style="width: 100%; max-width: 500px;">
                                                        <x-input-error :messages="$errors
                                                            ->getBag('grant_access_user_folder_' . $child->id)
                                                            ->get('email')" class="mt-2" />
                                                    </div>
                                                    <div class="me-3">
                                                        <select class="form-select" name="permission_type"
                                                            id="permission_type">
                                                            <option value="{{ \App\Enums\PermissionType::READ->value }}"
                                                                {{ old('permission_type') === \App\Enums\PermissionType::READ ? 'selected' : '' }}>
                                                                Pelihat
                                                            </option>
                                                            <option value="{{ \App\Enums\PermissionType::READ_WRITE->value }}"
                                                                {{ old('permission_type') === \App\Enums\PermissionType::READ_WRITE ? 'selected' : '' }}>
                                                                Editor
                                                            </option>
                                                        </select>
                                                        <x-input-error :messages="$errors
                                                            ->getBag('grant_access_user_folder_' . $child->id)
                                                            ->get('permission_type')" class="mt-2" />
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Berikan Akses</button>
                                            </form>

                                            <p>Orang yang memiliki akses:</p>
                                            <ul class="list-group mb-2"
                                                style="max-height: 200px; overflow-y: auto; border: none;">
                                                @foreach ($child->userFolderAccesses as $access)
                                                    <li class="list-group-item" style="border: none;">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span><strong>{{ $access->owner->email }}</strong></span>
                                                            <div class="d-flex flex-column align-items-end">
                                                                <!-- Update Access Form -->
                                                                <form action="{{ route('folder-accesses.update', $access) }}"
                                                                    method="POST" class="d-inline-block mb-1">
                                                                    @csrf
                                                                    @method('patch')
                                                                    <select class="form-select form-select-sm w-auto"
                                                                        name="permission_type" onchange="this.form.submit()">
                                                                        <option
                                                                            value="{{ \App\Enums\PermissionType::READ->value }}"
                                                                            {{ old('permission_type', $access->permission_type) === \App\Enums\PermissionType::READ ? 'selected' : '' }}>
                                                                            Pelihat
                                                                        </option>
                                                                        <option
                                                                            value="{{ \App\Enums\PermissionType::READ_WRITE->value }}"
                                                                            {{ old('permission_type', $access->permission_type) === \App\Enums\PermissionType::READ_WRITE ? 'selected' : '' }}>
                                                                            Editor
                                                                        </option>
                                                                    </select>
                                                                </form>

                                                                <!-- Delete Access Form -->
                                                                <form action="{{ route('folder-accesses.destroy', $access) }}"
                                                                    method="POST" class="d-inline-block mt-1">
                                                                    @csrf
                                                                    @method('delete')
                                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus
                                                                        Akses</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <!-- Grant Access for Everyone -->
                                            <form action="{{ route('folders.grantPermission', $child) }}" method="POST"
                                                class="mb-4">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="permission_type" class="form-label">Izin Akses:</label>
                                                    <select class="form-select" name="permission_type" id="permission_type">
                                                        <option value=""
                                                            {{ old('permission_type', $child->permission_type) === null ? 'selected' : '' }}>
                                                            No Access</option>
                                                        <option value="{{ \App\Enums\PermissionType::READ->value }}"
                                                            {{ old('permission_type', $child->permission_type) === \App\Enums\PermissionType::READ ? 'selected' : '' }}>
                                                            Pelihat
                                                        </option>
                                                        <option value="{{ \App\Enums\PermissionType::READ_WRITE->value }}"
                                                            {{ old('permission_type', $child->permission_type) === \App\Enums\PermissionType::READ_WRITE ? 'selected' : '' }}>
                                                            Editor
                                                        </option>
                                                    </select>
                                                    <x-input-error :messages="$errors
                                                        ->getBag('grant_access_everyone_folder_' . $child->id)
                                                        ->get('permission_type')" class="mt-2" />
                                                </div>
                                                <button type="submit" class="btn btn-primary">Berikan Akses</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-between align-items-center">
                                        <button id="copyFolderLinkBtn-{{ $child->id }}"
                                            class="btn btn-outline-dark rounded-3 px-4 py-2 mb-1 fw-bold">🔗 Salin
                                            Link</button>
                                        <button class="btn btn-dark rounded-3 px-4 py-2" type="button"
                                            data-bs-dismiss="modal">Selesai</button>
                                    </div>
                                    <input type="text" id="urlFolderToCopy-{{ $child->id }}"
                                        value="{{ route('folders.show', $child) }}"
                                        style="position: absolute; top: -9999px;">
                                </div>
                            </div>
                        </div>
                    @endcan
                @endforeach
            @endif
        </div>

    </div>

    <ul>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">File</h1>

            <!-- Button Container -->
            <div class="d-inline-block">
                <!-- Upload File Button -->
                @can('modifyContent', $folder)
                    <button id="uploadFileBtn" class="btn btn-success btn-sm shadow-sm" onclick="triggerFileInput()">
                        <i class="fa-solid fa-upload fa-sm text-white"></i> | Upload File
                    </button>
                @endcan

                <!-- Existing Icon Button -->
                <button id="toggleViewBtn" class="btn btn-success btn-sm shadow-sm me-2">
                    <i class="fa-solid fa-border-all fa-sm text-white"></i> | Icon
                </button>
            </div>
        </div>

        <!-- Hidden File Input and Form -->
        @can('modifyContent', $folder)
            <form id="uploadForm" action="{{ route('folders.files.store', $folder) }}" method="POST"
                enctype="multipart/form-data" class="mt-2">
                @csrf
                <!-- Hidden file input -->
                <input type="file" name="files[]" id="fileInput" multiple style="display: none;" />
                <!-- Hidden submit button -->
                <button type="submit" style="display: none;"></button>
            </form>
        @endcan

        @if ($folder->files)
            <!-- Grid View untuk Files -->
            <div class="custom-row grid-view">
                @foreach ($folder->files as $file)
                    <div class="col-md-3">
                        <div class="file-card">
                            <!-- File Card with Ellipsis Icon -->
                            <div class="d-flex justify-content-between">
                                <i class="{{ getFileIconClass($file) }}"></i>
                                <!-- Dropdown Button (Ellipsis) -->
                                <div class="dropdown">
                                    <button class="btn btn-link p-0" id="gridDropdownMenuButton{{ $file->id }}"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v text-dark"></i>
                                    </button>
                                    <!-- Dropdown Menu -->
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $file->id }}">
                                        <!-- Download Option -->
                                        @can('view', $file)
                                            <li>
                                                <a class="dropdown-item" href="{{ route('files.download', $file) }}">
                                                    <i class="fas fa-download me-2"></i>Download
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- Rename Option -->
                                        @can('update', $file)
                                            <li>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#renameModal{{ $file->id }}">
                                                    <i class="fas fa-file-pen me-2"></i>Ganti Nama
                                                </a>
                                            </li>
                                        @endcan

                                        <hr class="dropdown-divider">

                                        @can('grantAccess', $file)
                                            <li>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#shareModal" data-file-name="{{ $file->name }}">
                                                    <i class="fas fa-share-nodes me-2"></i> Bagikan
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- File Information Option -->
                                        <li>
                                            <a class="dropdown-item" href="#" data-bs-toggle="offcanvas"
                                                data-bs-target="#fileInfoOffcanvas" data-file-name="{{ $file->name }}"
                                                data-file-type="{{ getFileType($file) }}"
                                                data-file-size="{{ number_format($file->size / 1024, 2) }} KB"
                                                data-file-location="{{ $folder->is_root ? 'Berkas Saya' : $folder->name }}"
                                                data-file-owner="{{ $file->owner->name }}"
                                                data-file-date="{{ $file->created_at->format('Y-m-d') }}">
                                                <i class="fas fa-info-circle me-2"></i>Informasi File
                                            </a>
                                        </li>

                                        <hr class="dropdown-divider">

                                        <!-- Delete Option -->
                                        @can('delete', $file)
                                            <li>
                                                <form action="{{ route('files.destroy', $file) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash-alt me-2"></i>Hapus
                                                    </button>
                                                </form>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </div>

                            <div class="file-view">
                                <i class="{{ getFileIconClass($file) }}"></i>
                            </div>
                            <div class="mt-2">
                                <div class="file-title">{{ $file->name }}</div>
                                <div class="file-type">
                                    <span>{{ getFileType($file) }}</span> | <span>{{ formatFileSize($file->size) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <div class="file-actions">
                                        <!-- Ikon Share dengan pointer -->
                                        @can('grantAccess', $file)
                                            <i class="fa-solid fa-share-nodes st" data-bs-toggle="modal"
                                                data-bs-target="#shareModal" data-file-name="{{ $file->name }}"
                                                style="cursor: pointer;"></i>
                                        @endcan

                                        <!-- Open Modal to Rename -->
                                        @can('update', $file)
                                            <i class="fa-solid fa-file-pen edit-file" data-bs-toggle="modal"
                                                data-bs-target="#renameModal{{ $file->id }}"
                                                style="cursor: pointer;"></i>
                                        @endcan

                                        <!-- Bookmark/Unbookmark Button -->
                                        @can('view', $file)
                                            <form action="{{ route('file-bookmarks.store', $file) }}" method="POST"
                                                class="d-inline" id="bookmarkForm{{ $file->id }}">
                                                @csrf
                                                <input type="hidden" name="is_starred"
                                                    value="{{ $file->fileBookmarks()->where('user_id', Auth::id())->exists() && $file->fileBookmarks()->firstWhere('user_id', Auth::id())->is_starred ? '0' : '1' }}">
                                                <button type="submit" class="btn btn-link p-0">
                                                    <i
                                                        class="fa-star {{ $file->fileBookmarks()->where('user_id', Auth::id())->exists() && $file->fileBookmarks()->firstWhere('user_id', Auth::id())->is_starred ? 'fa-solid' : 'fa-regular' }} file-star"></i>
                                                </button>
                                            </form>
                                        @endcan

                                        @can('view', $file)
                                            <a href="{{ route('files.download', $file) }}"><i
                                                    class="fas fa-download"></i></a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Share -->
                    @can('grantAccess', $file)
                        <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="shareModalLabel">Bagikan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Tempat untuk menampilkan nama file -->
                                        <div class="file-title" id="fileNameModal"></div>

                                        <div class="container mt-4">
                                            <!-- Form Grant Access to Specific User -->
                                            <form action="{{ route('file-accesses.store', $file) }}" method="POST"
                                                class="mb-4">
                                                @csrf
                                                <div class="mb-3 d-flex">
                                                    <div class="me-3" style="flex-grow: 1;">
                                                        <input type="email" name="email" id="email"
                                                            class="form-control" placeholder="User Email"
                                                            value="{{ old('email') }}"
                                                            style="width: 100%; max-width: 500px;">
                                                        <x-input-error :messages="$errors
                                                            ->getBag('grant_access_user_file_' . $file->id)
                                                            ->get('email')" class="mt-2" />
                                                    </div>
                                                    <div class="me-3">
                                                        <select class="form-select" name="permission_type"
                                                            id="permission_type">
                                                            <option value="{{ \App\Enums\PermissionType::READ->value }}"
                                                                {{ old('permission_type') === \App\Enums\PermissionType::READ ? 'selected' : '' }}>
                                                                Pelihat
                                                            </option>
                                                            <option value="{{ \App\Enums\PermissionType::READ_WRITE->value }}"
                                                                {{ old('permission_type') === \App\Enums\PermissionType::READ_WRITE ? 'selected' : '' }}>
                                                                Editor
                                                            </option>
                                                        </select>
                                                        <x-input-error :messages="$errors
                                                            ->getBag('grant_access_user_file_' . $file->id)
                                                            ->get('permission_type')" class="mt-2" />
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn btn-primary">Berikan Akses</button>
                                            </form>

                                            <p>Orang yang memiliki akses:</p>
                                            <ul class="list-group mb-2"
                                                style="max-height: 200px; overflow-y: auto; border: none;">
                                                @foreach ($file->userFileAccesses as $access)
                                                    <li class="list-group-item" style="border: none;">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span>
                                                                <strong>{{ $access->owner->email }}</strong>
                                                            </span>
                                                            <div class="d-flex flex-column align-items-end">
                                                                <!-- Update Access Form (No Update Button) -->
                                                                <form action="{{ route('file-accesses.update', $access) }}"
                                                                    method="POST" class="d-inline-block mb-1">
                                                                    @csrf
                                                                    @method('patch')
                                                                    <select class="form-select form-select-sm w-auto"
                                                                        name="permission_type" onchange="this.form.submit()">
                                                                        <option
                                                                            value="{{ \App\Enums\PermissionType::READ->value }}"
                                                                            {{ old('permission_type', $access->permission_type) === \App\Enums\PermissionType::READ ? 'selected' : '' }}>
                                                                            Pelihat
                                                                        </option>
                                                                        <option
                                                                            value="{{ \App\Enums\PermissionType::READ_WRITE->value }}"
                                                                            {{ old('permission_type', $access->permission_type) === \App\Enums\PermissionType::READ_WRITE ? 'selected' : '' }}>
                                                                            Editor
                                                                        </option>
                                                                    </select>
                                                                </form>

                                                                <!-- Delete Access Form -->
                                                                <form action="{{ route('file-accesses.destroy', $access) }}"
                                                                    method="POST" class="d-inline-block mt-1">
                                                                    @csrf
                                                                    @method('delete')
                                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus
                                                                        Akses</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <!-- List of Access -->
                                            @if ($file->permission_type)
                                                <div class="alert alert-info">
                                                    Semua Orang:
                                                    {{ $file->permission_type === \App\Enums\PermissionType::READ ? 'Pelihat' : 'Editor' }}
                                                </div>
                                            @endif

                                            <!-- Form Grant Access to Everyone -->
                                            <form action="{{ route('files.grantPermission', $file) }}" method="POST"
                                                class="mb-4">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="permission_type" class="form-label">Izin
                                                        Akses:</label>
                                                    <select class="form-select" name="permission_type" id="permission_type">
                                                        <option value=""
                                                            {{ old('permission_type', $file->permission_type) === null ? 'selected' : '' }}>
                                                            No Access</option>
                                                        <option value="{{ \App\Enums\PermissionType::READ->value }}"
                                                            {{ old('permission_type', $file->permission_type) === \App\Enums\PermissionType::READ ? 'selected' : '' }}>
                                                            Pelihat
                                                        </option>
                                                        <option value="{{ \App\Enums\PermissionType::READ_WRITE->value }}"
                                                            {{ old('permission_type', $file->permission_type) === \App\Enums\PermissionType::READ_WRITE ? 'selected' : '' }}>
                                                            Editor
                                                        </option>
                                                    </select>
                                                    <x-input-error :messages="$errors
                                                        ->getBag('grant_access_everyone_file_' . $file->id)
                                                        ->get('permission_type')" class="mt-2" />
                                                </div>

                                                <button type="submit" class="btn btn-primary">Berikan Akses</button>
                                            </form>
                                        </div>

                                    </div>

                                    <div class="modal-footer d-flex justify-content-between align-items-center">
                                        <button id="copyFileLinkBtn-{{ $file->id }}"
                                            class="btn btn-outline-dark rounded-3 px-4 py-2 mb-1 fw-bold">🔗 Salin
                                            Link</button>
                                        <button class="btn btn-dark rounded-3 px-4 py-2" type="button"
                                            data-bs-dismiss="modal">Selesai</button>
                                    </div>
                                    <input type="text" id="urlFileToCopy-{{ $file->id }}"
                                        value="{{ route('files.download', $file) }}"
                                        style="position: absolute; top: -9999px;">
                                </div>
                            </div>
                        </div>
                    @endcan
                @endforeach
            </div>

            @foreach ($folder->files as $file)
                @can('update', $file)
                    @include('components.rename-file-modal', ['file' => $file])
                @endcan
                <x-informasi-file :file="$file" />
            @endforeach

            <!-- List View untuk Files -->
            <div class="list-view d-none">
                <table class="table table-borderless table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" class="text-center">
                                <i class="fa-solid fa-file-image file-icon"></i>
                            </th>
                            <th scope="col">Nama</th>
                            <th scope="col">Tanggal Akses</th>
                            <th scope="col">Pemilik</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($folder->files as $file)
                            <tr>
                                <!-- Ikon file -->
                                <td class="text-center" style="border-bottom: 2px solid #000000;">
                                    <i class="{{ getFileIconClass($file) }}"></i>
                                </td>
                                <!-- Nama file -->
                                <td class="fw-semibold text-dark" style="border-bottom: 2px solid #000000;">
                                    {{ Str::limit($file->name, 20, '...') }}</td>
                                <!-- Tanggal akses -->
                                <td style="border-bottom: 2px solid #000000;">{{ $file->created_at->format('Y-m-d') }}
                                </td>
                                <!-- Pemilik -->
                                <td style="border-bottom: 2px solid #000000;">
                                    {{ strlen($file->owner->name) > 10 ? Str::limit($file->owner->name, 10, '...') : $file->owner->name }}
                                </td>
                                <!-- Aksi -->
                                <td class="text-center" style="border-bottom: 2px solid #000000;">
                                    <div class="d-flex justify-content-center align-items-center gap-3">
                                        <!-- Ikon share -->
                                        @can('grantAccess', $file)
                                            <i class="fas fa-share-alt text-primary"></i>
                                        @endcan

                                        <!-- Tombol edit -->
                                        @can('update', $file)
                                            <button class="btn btn-link p-0" data-bs-toggle="modal"
                                                data-bs-target="#renameModal{{ $file->id }}">
                                                <i class="fa-solid fa-file-pen text-success"></i>
                                            </button>
                                        @endcan

                                        <!-- Bookmark -->
                                        @can('view', $file)
                                            <i class="fa-regular fa-star text-warning"></i>
                                        @endcan

                                        <!-- Tombol download -->
                                        @can('view', $file)
                                            <a href="{{ route('files.download', $file) }}"
                                                class="text-decoration-none text-info">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endcan
                                        <!-- Dropdown -->
                                        <div class="dropdown">
                                            <button class="btn btn-link p-0"
                                                id="listDropdownMenuButton{{ $file->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v text-dark"></i>
                                            </button>
                                            <ul class="dropdown-menu"
                                                aria-labelledby="listDropdownMenuButton{{ $file->id }}">

                                                @can('view', $file)
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('files.download', $file) }}">
                                                            <i class="fas fa-download me-2"></i>Download
                                                        </a>
                                                    </li>
                                                @endcan

                                                @can('update', $file)
                                                    <li>
                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                            data-bs-target="#renameModal{{ $file->id }}">
                                                            <i class="fas fa-file-pen me-2"></i>Ganti Nama
                                                        </a>
                                                    </li>
                                                @endcan
                                                <hr class="dropdown-divider">

                                                @can('grantAccess', $file)
                                                    <li>
                                                        <a class="dropdown-item" href="#">
                                                            <i class="fas fa-share-nodes me-2"></i>Bagikan
                                                        </a>
                                                    </li>
                                                @endcan

                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="offcanvas"
                                                        data-bs-target="#fileInfoOffcanvas"
                                                        data-file-name="{{ $file->name }}"
                                                        data-file-type="{{ getFileType($file) }}"
                                                        data-file-size="{{ number_format($file->size / 1024, 2) }} KB"
                                                        data-file-location="{{ $folder->is_root ? 'Berkas Saya' : $folder->name }}"
                                                        data-file-owner="{{ $file->owner->name }}"
                                                        data-file-date="{{ $file->created_at->format('Y-m-d') }}">
                                                        <i class="fas fa-info-circle me-2"></i>Informasi File
                                                    </a>
                                                </li>
                                                <hr class="dropdown-divider">
                                                <li>
                                                    <form action="{{ route('files.destroy', $file) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash-alt me-2"></i>Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </ul>

    <script>
        @foreach ($folder->children as $child)
            // Adding event listener for each copy button
            document.getElementById('copyFolderLinkBtn-{{ $child->id }}').addEventListener('click', function() {
                var urlField = document.getElementById('urlFolderToCopy-{{ $child->id }}');

                // Use the Clipboard API to copy the URL
                navigator.clipboard.writeText(urlField.value).then(function() {
                    alert('Link copied to clipboard!');
                }).catch(function(err) {
                    console.error('Failed to copy: ', err);
                    alert('Failed to copy link!');
                });
            });
        @endforeach

        @foreach ($folder->files as $file)
            // Adding event listener for each copy button
            document.getElementById('copyFileLinkBtn-{{ $file->id }}').addEventListener('click', function() {
                var urlField = document.getElementById('urlFileToCopy-{{ $file->id }}');

                // Use the Clipboard API to copy the URL
                navigator.clipboard.writeText(urlField.value).then(function() {
                    alert('Link copied to clipboard!');
                }).catch(function(err) {
                    console.error('Failed to copy: ', err);
                    alert('Failed to copy link!');
                });
            });
        @endforeach
    </script>
@endsection
