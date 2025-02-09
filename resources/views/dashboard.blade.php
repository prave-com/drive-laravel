<x-app-layout>

    @section('content')
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Semua File</h1>
            <button id="toggleViewBtn" class="btn btn-success btn-sm shadow-sm">
                <i class="fa-solid fa-border-all fa-sm text-white"></i> | Icon
            </button>
        </div>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <a class="nav-item text-decoration-none" role="presentation"
                href="{{ route('dashboard', ['filter' => 'latest']) }}">
                <button class="nav-link {{ $filter === 'latest' ? 'active' : '' }}" id="terbaru-tab" data-bs-toggle="tab"
                    data-bs-target="#terbaru" type="button" role="tab" aria-controls="terbaru"
                    aria-selected="{{ $filter === 'latest' ? 'true' : 'false' }}">
                    Terbaru
                </button>
            </a>
            <a class="nav-item text-decoration-none" role="presentation"
                href="{{ route('dashboard', ['filter' => 'shared']) }}">
                <button class="nav-link {{ $filter === 'shared' ? 'active' : '' }}" id="dibagikan-tab" data-bs-toggle="tab"
                    data-bs-target="#dibagikan" type="button" role="tab" aria-controls="dibagikan"
                    aria-selected="{{ $filter === 'shared' ? 'true' : 'false' }}">
                    Dibagikan
                </button>
            </a>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="myTabContent">
            <!-- Tab untuk Terbaru -->
            <div class="tab-pane fade show active" id="terbaru" role="tabpanel" aria-labelledby="terbaru-tab">

                <!-- Grid View untuk Files -->
                <div class="custom-row grid-view">
                    @foreach ($files as $file)
                        <div class="col-md-3">
                            <div class="file-card">
                                <!-- File Card with Ellipsis Icon -->
                                <div class="d-flex justify-content-between">
                                    <i class="{{ getFileIconClass($file) }}"></i>
                                    <!-- Grid View Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-link p-0" id="gridDropdownMenuButton{{ $file->id }}"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v text-dark"></i>
                                        </button>
                                        <!-- Dropdown Menu -->
                                        <ul class="dropdown-menu"
                                            aria-labelledby="gridDropdownMenuButton{{ $file->id }}">
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

                                            <!-- Share Option -->
                                            @can('grantAccess', $file)
                                                <li>
                                                    <a class="dropdown-item" href="#">
                                                        <i class="fas fa-share-nodes me-2"></i>Bagikan
                                                    </a>
                                                </li>
                                            @endcan

                                            <!-- File Information Option -->
                                            <li>
                                                <a class="dropdown-item" href="#" data-bs-toggle="offcanvas"
                                                    data-bs-target="#fileInfoOffcanvas"
                                                    data-file-name="{{ $file->name }}"
                                                    data-file-type="{{ getFileType($file) }}"
                                                    data-file-size="{{ number_format($file->size / 1024, 2) }} KB"
                                                    data-file-location="{{ $currentLocation }} / {{ isset($folder) ? $folder->name : '' }}"
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
                                        {{ getFileType($file) }} | {{ number_format($file->size / 1024, 2) }} MB
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <div class="file-actions">
                                            @can('grantAccess', $file)
                                                <i class="fa-solid fa-share-nodes"></i>
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
                    @endforeach
                </div>

                <!-- Modal untuk Renaming File -->
                @foreach ($files as $file)
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
                            @foreach ($files as $file)
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
                                                <form action="{{ route('file-bookmarks.store', $file) }}" method="POST"
                                                    class="d-inline" id="bookmarkFormList{{ $file->id }}">
                                                    @csrf
                                                    <input type="hidden" name="is_starred"
                                                        value="{{ $file->fileBookmarks()->where('user_id', Auth::id())->exists() && $file->fileBookmarks()->firstWhere('user_id', Auth::id())->is_starred ? '0' : '1' }}">
                                                    <button type="submit" class="btn btn-link p-0">
                                                        <i
                                                            class="fa-star {{ $file->fileBookmarks()->where('user_id', Auth::id())->exists() && $file->fileBookmarks()->firstWhere('user_id', Auth::id())->is_starred ? 'fa-solid text-warning' : 'fa-regular text-warning' }}"></i>
                                                    </button>
                                                </form>
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
                                                    id="listDropdownMenuButton{{ $file->id }}"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
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
                                                        <a class="dropdown-item" href="#"
                                                            data-bs-toggle="offcanvas" data-bs-target="#fileInfoOffcanvas"
                                                            data-file-name="{{ $file->name }}"
                                                            data-file-type="{{ getFileType($file) }}"
                                                            data-file-size="{{ number_format($file->size / 1024, 2) }} KB"
                                                            data-file-location="{{ $file->parent->name }}"
                                                            data-file-owner="{{ $file->owner->name }}"
                                                            data-file-date="{{ $file->created_at->format('Y-m-d') }}">
                                                            <i class="fas fa-info-circle me-2"></i>Informasi File
                                                        </a>
                                                    </li>

                                                    <hr class="dropdown-divider">

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
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="tab-pane fade" id="dibagikan" role="tabpanel" aria-labelledby="dibagikan-tab">
                <!-- Konten untuk tab Dibagikan -->
                <!-- Grid View untuk Files -->
                <div class="custom-row grid-view">
                    @foreach ($files as $file)
                        <div class="col-md-3">
                            <div class="file-card">
                                <!-- File Card with Ellipsis Icon -->
                                <div class="d-flex justify-content-between">
                                    <i class="{{ getFileIconClass($file) }}"></i>
                                    <!-- Grid View Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-link p-0" id="gridDropdownMenuButton{{ $file->id }}"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v text-dark"></i>
                                        </button>
                                        <!-- Dropdown Menu -->
                                        <ul class="dropdown-menu"
                                            aria-labelledby="gridDropdownMenuButton{{ $file->id }}">
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

                                            <!-- Share Option -->
                                            @can('grantAccess', $file)
                                                <li>
                                                    <a class="dropdown-item" href="#">
                                                        <i class="fas fa-share-nodes me-2"></i>Bagikan
                                                    </a>
                                                </li>
                                            @endcan

                                            <!-- File Information Option -->
                                            <li>
                                                <a class="dropdown-item" href="#" data-bs-toggle="offcanvas"
                                                    data-bs-target="#fileInfoOffcanvas"
                                                    data-file-name="{{ $file->name }}"
                                                    data-file-type="{{ getFileType($file) }}"
                                                    data-file-size="{{ number_format($file->size / 1024, 2) }} KB"
                                                    data-file-location="{{ $currentLocation }} / {{ isset($folder) ? $folder->name : '' }}"
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
                                        {{ getFileType($file) }} | {{ number_format($file->size / 1024, 2) }} MB
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <div class="file-actions">
                                            @can('grantAccess', $file)
                                                <i class="fa-solid fa-share-nodes"></i>
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
                    @endforeach
                </div>

                <!-- Modal untuk Renaming File -->
                @foreach ($files as $file)
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
                            @foreach ($files as $file)
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
                                                <form action="{{ route('file-bookmarks.store', $file) }}" method="POST"
                                                    class="d-inline" id="bookmarkFormList{{ $file->id }}">
                                                    @csrf
                                                    <input type="hidden" name="is_starred"
                                                        value="{{ $file->fileBookmarks()->where('user_id', Auth::id())->exists() && $file->fileBookmarks()->firstWhere('user_id', Auth::id())->is_starred ? '0' : '1' }}">
                                                    <button type="submit" class="btn btn-link p-0">
                                                        <i
                                                            class="fa-star {{ $file->fileBookmarks()->where('user_id', Auth::id())->exists() && $file->fileBookmarks()->firstWhere('user_id', Auth::id())->is_starred ? 'fa-solid text-warning' : 'fa-regular text-warning' }}"></i>
                                                    </button>
                                                </form>
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
                                                    id="listDropdownMenuButton{{ $file->id }}"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
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
                                                        <a class="dropdown-item" href="#"
                                                            data-bs-toggle="offcanvas" data-bs-target="#fileInfoOffcanvas"
                                                            data-file-name="{{ $file->name }}"
                                                            data-file-type="{{ getFileType($file) }}"
                                                            data-file-size="{{ number_format($file->size / 1024, 2) }} KB"
                                                            data-file-location="{{ $file->parent->name }}"
                                                            data-file-owner="{{ $file->owner->name }}"
                                                            data-file-date="{{ $file->created_at->format('Y-m-d') }}">
                                                            <i class="fas fa-info-circle me-2"></i>Informasi File
                                                        </a>
                                                    </li>

                                                    <hr class="dropdown-divider">

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
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">
            {{ $files->appends(request()->query())->links() }}
        </div>
    @endsection

</x-app-layout>
