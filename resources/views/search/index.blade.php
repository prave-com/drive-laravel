@extends('layouts.app')

@section('content')
    <div class="container">

        @if (isset($query))
            <h3>Results for: "{{ $query }}"</h3>

            <div class="row mb-4">
                <h4>Folders</h4>
                @if ($folders->isEmpty())
                    <p>No folders found.</p>
                @else
                    <div class="row">
                        @foreach ($folders as $folder)
                            <div class="col-md-3 col-sm-6 mb-4">
                                <!-- Folder Item -->
                                <div class="folder-item cursor-pointer card shadow-sm folder-file-card h-100"
                                    data-url="{{ route('folders.show', $folder) }}" style="cursor: pointer; height: 350px;">
                                    <div
                                        class="card-body text-center d-flex flex-column justify-content-between align-items-center">
                                        <!-- Folder Icon -->
                                        <div class="file-icon-container"
                                            style="height: 150px; display: flex; justify-content: center; align-items: center;">
                                            <i class="fas fa-folder fa-3x text-warning"></i>
                                        </div>

                                        <!-- Folder Name -->
                                        <h6 class="mt-2 text-truncate text-center"
                                            style="max-width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $folder->name }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", () => {
                    // Select all folder items
                    const folderItems = document.querySelectorAll(".folder-item");

                    folderItems.forEach(folderItem => {
                        folderItem.addEventListener("click", () => {
                            // Get the URL from the data-url attribute
                            const folderUrl = folderItem.getAttribute("data-url");

                            if (folderUrl) {
                                // Redirect to the folder URL
                                window.location.href = folderUrl;
                            }
                        });
                    });
                });
            </script>

            <div class="mt-4">
                <h4>Files</h4>
                @if ($files->isEmpty())
                    <p>No files found.</p>
                @else
                    <div class="custom-row grid-view">
                        @foreach ($files as $file)
                            <div class="col-md-3">
                                <div class="file-card">
                                    <!-- File Card with Ellipsis Icon -->
                                    <div class="d-flex justify-content-between">
                                        <i class="{{ getFileIconClass($file) }}"></i>
                                        <!-- Dropdown Button (Ellipsis) -->
                                        <div class="dropdown">
                                            <button class="btn btn-link p-0 text-dark"
                                                id="dropdownMenuButton{{ $file->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <!-- Dropdown Menu -->
                                            <ul class="dropdown-menu"
                                                aria-labelledby="dropdownMenuButton{{ $file->id }}">
                                                <!-- Download Option -->
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('files.download', $file) }}">
                                                        <i class="fas fa-download me-2"></i>Download
                                                    </a>
                                                </li>
                                                <!-- Rename Option -->
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#renameModal{{ $file->id }}">
                                                        <i class="fas fa-file-pen me-2"></i>Ganti Nama
                                                    </a>
                                                </li>
                                                <hr class="profile-divider">
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#shareModal" data-file-name="{{ $file->name }}">
                                                        <i class="fas fa-share-nodes me-2"></i> Bagikan
                                                    </a>
                                                </li>
                                                <!-- File Information Option -->
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="offcanvas"
                                                        data-bs-target="#fileInfoOffcanvas"
                                                        data-file-name="{{ $file->name }}"
                                                        data-file-type="{{ getFileType($file) }}"
                                                        data-file-size="{{ number_format($file->size / 1024, 2) }} MB"
                                                        data-file-location="{{ $file->parent->name }}"
                                                        data-file-owner="{{ $file->owner->name }}"
                                                        data-file-date="{{ $file->created_at->format('Y-m-d') }}">
                                                        <i class="fas fa-info-circle me-2"></i>Informasi File
                                                    </a>
                                                </li>

                                                <hr class="profile-divider">
                                                <!-- Delete Option -->
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

                                    <!-- Modal Share -->
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

                                                    <form action="{{ route('file-accesses.store', $file) }}" method="POST"
                                                        class="mt-2">
                                                        @csrf
                                                        <!-- Input Email -->
                                                        <div>
                                                            <input class="form-control mb-3" type="email" name="email"
                                                                id="email" value="{{ old('email') }}"
                                                                placeholder="Masukkan email" />
                                                            <x-input-error :messages="$errors
                                                                ->getBag('grant_access_user_file_' . $file->id)
                                                                ->get('email')" class="mt-2" />
                                                        </div>
                                                    </form>

                                                    <p>Orang yang memiliki akses:</p>
                                                    <div data-bs-spy="scroll" data-bs-target="#email" data-bs-offset="0"
                                                        class="scrollspy-example" tabindex="0">
                                                        <ul class="user-list">
                                                            @foreach ($file->userFileAccesses as $access)
                                                                <li>
                                                                    <!-- Menampilkan gambar profil berdasarkan email -->
                                                                    <img alt="User avatar" height="40" width="40"
                                                                        src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim($access->owner->email))) }}?d=mp" />

                                                                    <div class="user-info">
                                                                        <p class="name">{{ $access->owner->name }}</p>
                                                                        <!-- Nama pengguna -->
                                                                        <p class="email">{{ $access->owner->email }}</p>
                                                                        <!-- Email pengguna -->
                                                                    </div>
                                                                    <div class="user-access">
                                                                        <select class="form-select" id="roleSelector">
                                                                            <option value="pelihat">Pelihat</option>
                                                                            <option value="editor">Editor</option>
                                                                            <option value="hapus">Hapus</option>
                                                                        </select>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>

                                                    <!-- Akses File -->
                                                    <div class="access-file">
                                                        <p>Akses File</p>
                                                        <select class="form-select" id="accessSelect"
                                                            onchange="updateContent()">
                                                            <option value="terbatas">Terbatas</option>
                                                            <option value="semua">Semua Orang</option>
                                                        </select>

                                                        <div id="infoText" class="mt-3">
                                                            <!-- Konten informasi yang akan berubah -->
                                                        </div>

                                                        <div id="userSelector" class="mt-3" style="display: none;">
                                                            <select class="form-select" id="role">
                                                                <option value="pelihat">Pelihat</option>
                                                                <option value="editor">Editor</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button class="btn-copy-link">🔗 Salin Link</button>
                                                    <button class="btn btn-finish" type="button"
                                                        data-bs-dismiss="modal">Selesai</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Offcanvas for File Information -->
                                    <div class="offcanvas offcanvas-end" tabindex="-1" id="fileInfoOffcanvas"
                                        aria-labelledby="fileInfoOffcanvasLabel">
                                        <div class="offcanvas-header">
                                            <h4 class="offcanvas-title" id="fileInfoOffcanvasLabel"><strong>Detail
                                                    File</strong>
                                            </h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="offcanvas-body">
                                            <p><strong>Nama File:</strong> <span id="fileName"></span></p>
                                            <p><strong>Jenis File:</strong> <span id="fileType"></span></p>
                                            <p><strong>Ukuran:</strong> <span id="fileSize"></span></p>
                                            <p><strong>Lokasi:</strong> <span id="fileLocation"></span></p>
                                            <p><strong>Pemilik:</strong> <span id="fileOwner"></span></p>
                                            <p><strong>Tanggal Akses:</strong> <span id="fileDate"></span></p>
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
                                        <div class="d-flex justify-content-between mt-2">
                                            <div class="file-actions">
                                                <!-- Ikon Share dengan pointer -->
                                                <i class="fa-solid fa-share-nodes st" data-bs-toggle="modal"
                                                    data-bs-target="#shareModal" data-file-name="{{ $file->name }}"
                                                    style="cursor: pointer;"></i>

                                                <!-- Open Modal to Rename -->
                                                <i class="fa-solid fa-file-pen edit-file" data-bs-toggle="modal"
                                                    data-bs-target="#renameModal{{ $file->id }}"
                                                    style="cursor: pointer;"></i>

                                                <!-- Bookmark/Unbookmark Button -->
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
                                                <a href="{{ route('files.download', $file) }}"><i
                                                        class="fas fa-download"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal untuk Rename File -->
                            <div class="modal fade" id="renameModal{{ $file->id }}" tabindex="-1"
                                aria-labelledby="renameModalLabel{{ $file->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="renameModalLabel{{ $file->id }}">Rename File
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Rename Form inside Modal -->
                                            <form id="renameForm{{ $file->id }}"
                                                action="{{ route('files.update', $file) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" name="name" value="{{ $file->name }}"
                                                    required class="form-control">
                                                <x-input-error :messages="$errors->getBag('rename_file_' . $file->id)->get('name')" class="mt-2" />
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <!-- Save Button -->
                                            <button type="submit" class="btn btn-primary"
                                                form="renameForm{{ $file->id }}">Simpan</button>
                                            <!-- Cancel Button -->
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        @endif
    </div>
@endsection
