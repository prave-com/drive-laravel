@extends('layouts.app')

@section('content')

    <div class="container">
        @can('view-bookmarks')
            <h1 class="my-4">{{ __('Trash') }}</h1>
            <p>{{ __('The files and folders in the trash will be automatically deleted after 30 days.') }}</p>
        @endcan

        @can('manage-users')
            <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
                <h3>{{ __('Trash') }}</h3>

                <form method="GET" action="{{ route('trash.index') }}" class="d-flex">
                    <div class="input-group" style="border: 2px solid #7f7f7f; border-radius: 20px; overflow: hidden;">
                        <!-- Button on the left -->
                        <button class="btn border-none bg-transparent" type="submit"
                            style="border: none; border-radius: 20px 0 0 20px;">
                            <i class='bx bx-search-alt file-icon text-secondary fw-bold'></i>
                        </button>

                        <!-- Input on the right -->
                        <input type="text" class="form-control border-none" name="search" placeholder="Cari"
                            value="{{ $search ?? '' }}"
                            style="max-width: 300px; border: none; outline: none; box-shadow: none;">

                        <!-- Clear button, only visible if there is a search query -->
                        @if (!empty($search))
                            <a href="{{ route('trash.index') }}" class="btn btn-link text-secondary align-middle"
                                style="border-radius: 20px 0 0 20px;">
                                <i class='bx bx-x'></i> <!-- Close icon to clear the search -->
                            </a>
                        @endif
                    </div>
                </form>

            </div>
        @endcan

        {{-- Deleted Folders Section --}}
        <div class="row mb-4">
            @if ($deletedFolders->isEmpty())
                <p class="text-center">Tidak ada folder yang dihapus.</p>
            @else
                @foreach ($deletedFolders as $folder)
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card shadow-sm folder-file-card"
                            style=" height: 240px; position: relative; transition: transform 0.2s, box-shadow 0.2s;">
                            <div class="card-body text-center d-flex flex-column justify-content-between">
                                <div class="file-icon-container"
                                    style="flex: 1; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                                    <i class="fas fa-folder fa-3x text-warning"></i>
                                </div>
                                <h6 class="mt-2 text-truncate">{{ $folder->name }}</h6>
                                @if ($isSuperAdmin)
                                    <p class="small text-muted">Pemilik: {{ $folder->owner->name }}</p>
                                @endif
                                <div class="file-options position-absolute top-0 end-0 p-2">
                                    <button class="btn btn-light btn-sm action-menu">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu position-absolute display-none top-20 right-0"
                                        style="z-index: 1050;">
                                        <form id="restoreFolderForm{{ $folder->id }}"
                                            action="{{ route('trash.folders.restore', $folder) }}" method="POST">
                                            @csrf
                                            <button type="button" class="restoreButton dropdown-item">
                                                <i class="fa-solid fa-reply"></i>{{ __('Restore') }}
                                            </button>
                                        </form>
                                        <form class="deleteForm"
                                            action="{{ route('trash.folders.force-delete', $folder) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="deleteButton dropdown-item text-danger">
                                                <i class="fa-solid fa-trash"></i>{{ __('Permanently Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="row">
            @if ($deletedFiles->isEmpty())
                <p class="text-center">{{ __('No files have been deleted.') }}</p>
            @else
                <div class="custom-row grid-view">
                    @foreach ($deletedFiles as $file)
                        <div class="col-md-3">
                            <div class="file-card">
                                <div class="d-flex justify-content-between">
                                    <i class="{{ getFileIconClass($file) }}"></i>
                                    <div class="dropdown">
                                        <button class="btn btn-link p-0 text-dark"
                                            id="dropdownMenuButton{{ $file->id }}" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <form id="restoreFileForm{{ $file->id }}"
                                                action="{{ route('trash.files.restore', $file) }}" method="POST">
                                                @csrf
                                                <button type="button" class="restoreFileButton dropdown-item">
                                                    <i class="fa-solid fa-reply"></i>{{ __('Restore') }}
                                                </button>
                                            </form>
                                            <form class="deleteForm"
                                                action="{{ route('trash.files.force-delete', $file) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="deleteButton dropdown-item text-danger">
                                                    <i class="fa-solid fa-trash"></i>{{ __('Permanently Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="file-view">
                                    <i class="{{ getFileIconClass($file) }}"></i>
                                </div>
                                <div class="mt-2">
                                    <div class="file-title">{{ $file->name }}</div>
                                    <div class="file-type">
                                        <span>{{ getFileType($file) }}</span> |
                                        <span>{{ formatFileSize($file->size) }}</span>
                                    </div>
                                </div>

                                @if ($isSuperAdmin)
                                    <p class="small text-muted">{{ __('Owner') }}: {{ $file->owner->name }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if ($isSuperAdmin)
            <div class="d-flex justify-content-center mt-4">
                {{ $deletedFiles->links() }}
            </div>
        @endif
    </div>

@endsection
