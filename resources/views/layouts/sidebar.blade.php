<!-- SIDEBAR -->
<section id="sidebar" class="sidebar">
    <div class="brand mb-3">
        <i class='bx bx-cloud'></i>
        <div class ="text-container">
            <span class="line1">LARAVEL.</span>
            <span class="line2">CLOUD</span>
        </div>
    </div>

    <ul class="nav flex-column mb-auto">
        @can('view-logs')
            <li class="nav-item">
                <a href="{{ route('activity-logs.index') }}" class="nav-link active d-flex justify-content-start"
                    data-icon="activity">
                    <i class='bx bx-stats file-icon'></i>
                    <span class="text ms-2 text-start">Aktivitas</span>
                </a>
            </li>
        @endcan

        @can('view-logs')
            <li class="nav-item">
                <a href="{{ route('storage-requests.index') }}" class="nav-link active d-flex justify-content-start"
                    data-icon="request">
                    <i class='bx bx-message-square-dots file-icon'></i>
                    <span class="text ms-2 text-start">Permintaan</span>
                </a>
            </li>
        @endcan

        @can('view-bookmarks')
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link active d-flex justify-content-start"
                    data-icon="category">
                    <i class='bx bx-category file-icon'></i>
                    <span class="text ms-2 text-start">Beranda</span>
                </a>
            </li>
        @endcan

        @can('view-bookmarks')
            <li class="nav-item">
                <a href="{{ route('folders.index') }}" class="nav-link d-flex justify-content-start" data-icon="folder">
                    <i class='bx bx-folder file-icon'></i>
                    <span class="text ms-2 text-start">Berkas Saya</span>
                </a>
            </li>
        @endcan

        @can('view-bookmarks')
            <li class="nav-item">
                <a href="{{ route('bookmarks.index') }}" class="nav-link d-flex justify-content-start" data-icon="star">
                    <i class='bx bx-star file-icon'></i>
                    <span class="text ms-2 text-start">Berbintang</span>
                </a>
            </li>
        @endcan
    </ul>

    <hr class="sidebar-divider">

    <ul class="nav flex-column">
        @can('manage-users')
            <li class="nav-item">
                <a href="{{ route('superadmin.users.index') }}" class="nav-link d-flex" data-icon="users-magagement">
                    <i class='bx bx-user-pin file-icon'></i>
                    <span class="text ms-2">Manajemen User</span>
                </a>
            </li>
        @endcan

        @can('manage-files-folders')
            <li class="nav-item">
                <a href="{{ route('trash.index') }}" class="nav-link d-flex" data-icon="trash-alt">
                    <i class='bx bx-trash-alt file-icon'></i>
                    <span class="text ms-2">Sampah</span>
                </a>
            </li>
        @endcan

        @can('manage-users')
            <li class="nav-item">
                <a href="{{ route('folders.index') }}" class="nav-link d-flex" data-icon="user-account">
                    <i class='bx bxs-user-account file-icon'></i>
                    <span class="text ms-2">File User</span>
                </a>
            </li>
        @endcan
    </ul>

    @can('view-bookmarks')
        <!-- Storage -->
        <div class="storage mt-3">
            <div class="storage-info">
                <span class="fw-bold">Penyimpanan</span>
                <div class="progress">
                    <!-- Calculate the progress percentage dynamically -->
                    <div class="progress-bar bg-success" role="progressbar"
                        style="width: {{ (Auth::user()->storage->used_quota / Auth::user()->storage->total_quota) * 100 }}%;"
                        aria-valuenow="{{ (Auth::user()->storage->used_quota / Auth::user()->storage->total_quota) * 100 }}"
                        aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <!-- Show used and total storage -->
                <span class="terpakai">Terpakai:
                    {{ number_format(Auth::user()->storage->used_quota / 1024 ** 3, 2) }} GB dari
                    {{ number_format(Auth::user()->storage->total_quota / 1024 ** 3, 2) }} GB
                </span>

            </div>
            <a href="{{ route('storage-requests.index') }}" class="storage-icon">
                <i class='bx bx-plus-circle'></i>
            </a>
        </div>
    @endcan
</section>
<!-- SIDEBAR -->
