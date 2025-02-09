@extends('layouts.app')

@section('content')
    <div class="container my-2">
        <h1 class="mb-4 text-start">Berbintang</h1> <!-- Teks Bookmark di kiri -->

        <!-- Alert -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Folders Section -->
        <h2 class="mb-3 text-start">Folder</h2> <!-- Header di kiri -->
        <div class="row row-cols-1 row-cols-md-4 g-4">
            @forelse($folderBookmarks as $folderBookmark)
                @if (!$folderBookmark->folder)
                    @continue {{-- Skip jika folder null --}}
                @endif

                <div class="folder-item cursor-pointer" data-url="{{ route('folders.show', $folderBookmark->folder->id) }}"
                    style="min-width: 200px; flex-shrink: 0;">
                    <div class="folder-item" style="min-width: 200px; flex-shrink: 0;">
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
                                    {{ $folderBookmark->folder->name }}
                                </h6>

                                <!-- Folder Options (Dropdown) -->
                                <div class="file-options" style="position: absolute; top: 10px; right: 10px;">
                                    <button class="btn btn-light btn-sm action-menu">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu"
                                        style="position: absolute; top: 20px; right: 0; z-index: 1050;">
                                        <!-- Dropdown Items -->
                                        <form action="{{ route('folder-bookmarks.store', $folderBookmark->folder) }}"
                                            method="POST" class="m-0">
                                            @csrf
                                            <input type="hidden" name="is_starred" value="0">
                                            <button type="submit" class="dropdown-item">
                                                <i class="fa-solid fa-bookmark"></i>Unbookmark
                                            </button>
                                        </form>
                                        @can('delete', $folderBookmark->folder)
                                            <form action="{{ route('folders.destroy', $folderBookmark->folder) }}"
                                                method="POST" class="m-0">
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
                </div>
            @empty
                <p class="text-muted">No bookmarked Folders.</p>
            @endforelse

        </div>

        <!-- Files Section -->
        <h2 class="mt-5 mb-3 text-start">Files</h2> <!-- Header di kiri -->
        <div class="row row-cols-1 row-cols-md-4 g-4">
            @forelse($fileBookmarks as $fileBookmark)
                @if ($fileBookmark->file)
                    <!-- Pastikan file ada -->
                    <div class="col-md-3">
                        <div class="file-card">
                            <!-- File Card with Ellipsis Icon -->
                            <div class="d-flex justify-content-between">
                                <i class="{{ getFileIconClass($fileBookmark->file) }}"></i>
                                <!-- Dropdown Button (Ellipsis) -->
                                <div class="dropdown">
                                    <button class="btn btn-link p-0 text-dark" id="dropdownMenuButton"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <!-- Dropdown Menu -->
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <!-- Download Option -->
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('files.download', $fileBookmark->file) }}">
                                                <i class="fas fa-download me-2"></i>Download
                                            </a>
                                        </li>
                                        <!-- Rename Option -->
                                        @can('update', $fileBookmark->file)
                                            <li>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#renameModal{{ optional($fileBookmark->file)->id ?? 'default' }}">
                                                    <i class="fas fa-file-pen me-2"></i>Ganti Nama
                                                </a>
                                            </li>
                                        @endcan
                                        <hr class="dropdown-divider">

                                        @can('grantAccess', $fileBookmark->file)
                                            <li>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#shareModal"
                                                    data-file-name="{{ optional($fileBookmark->file)->name }}">
                                                    <i class="fas fa-share-nodes me-2"></i> Bagikan
                                                </a>
                                            </li>
                                        @endcan
                                        <!-- File Information Option -->
                                        <li>
                                            <a class="dropdown-item" href="#" data-bs-toggle="offcanvas"
                                                data-bs-target="#fileInfoOffcanvas"
                                                data-file-name="{{ optional($fileBookmark->file)->name }}"
                                                data-file-type="{{ getFileType($fileBookmark->file) }}"
                                                data-file-size="{{ optional($fileBookmark->file)->size ? number_format($fileBookmark->file->size / 1024, 2) : 'N/A' }} KB"
                                                data-file-location="{{ $fileBookmark->file->parent->name }}"
                                                data-file-owner="{{ optional(optional($fileBookmark->file)->owner)->name ?? 'N/A' }}"
                                                data-file-date="{{ optional($fileBookmark->file)->created_at ? $fileBookmark->file->created_at->format('Y-m-d') : 'N/A' }}">
                                                <i class="fas fa-info-circle me-2"></i>Informasi File
                                            </a>
                                        </li>

                                        <hr class="dropdown-divider">
                                        <!-- Delete Option -->
                                        @can('delete', $fileBookmark->file)
                                            <li>
                                                <form action="{{ route('files.destroy', ['file' => $fileBookmark->file]) }}"
                                                    method="POST" class="d-inline">
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
                                <i class="{{ getFileIconClass($fileBookmark->file) }}"></i>
                            </div>
                            <div class="mt-2">
                                <div class="file-title">{{ optional($fileBookmark->file)->name }}</div>
                                <div class="file-type">
                                    <span>{{ getFileType($fileBookmark->file) }}</span> |
                                    <span>{{ formatFileSize(optional($fileBookmark->file)->size) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <div class="file-actions">
                                        <i class="fa-solid fa-share-nodes st" data-bs-toggle="modal"
                                            data-bs-target="#shareModal" data-file-name=""></i>
                                        <i class="fa-solid fa-file-pen edit-file" data-bs-toggle="modal"
                                            data-bs-target="#renameModal{{ optional($fileBookmark->file)->id ?? 'default' }}"
                                            data-file-id="{{ optional($fileBookmark->file)->id }}"
                                            style="cursor: pointer;"></i>
                                        <form action="{{ route('file-bookmarks.store', $fileBookmark->file) }}"
                                            method="POST" class="d-inline"
                                            id="bookmarkForm{{ $fileBookmark->file->id }}">
                                            @csrf
                                            <input type="hidden" name="is_starred"
                                                value="{{ $fileBookmark->file->fileBookmarks()->where('user_id', Auth::id())->exists() && $fileBookmark->file->fileBookmarks()->firstWhere('user_id', Auth::id())->is_starred ? '0' : '1' }}">
                                            <button type="submit" class="btn btn-link p-0">
                                                <i
                                                    class="fa-star {{ $fileBookmark->file->fileBookmarks()->where('user_id', Auth::id())->exists() && $fileBookmark->file->fileBookmarks()->firstWhere('user_id', Auth::id())->is_starred ? 'fa-solid' : 'fa-regular' }} file-star"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('files.download', ['file' => $fileBookmark->file]) }}">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <p class="text-muted">No bookmarked files.</p>
            @endforelse
        </div>

    </div>

    <!-- Modal untuk Rename File -->
    @foreach ($fileBookmarks as $fileBookmark)
        @if ($fileBookmark->file)
            @can('update', $fileBookmark->file)
                @include('components.rename-file-modal', ['file' => $fileBookmark->file])
            @endcan
            <x-informasi-file :file="$fileBookmark->file" />
        @endif
    @endforeach
@endsection
