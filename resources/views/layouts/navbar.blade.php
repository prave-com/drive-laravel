<!-- NAVBAR -->
<nav class="navbar navbar-light bg-light px-3" style="height: 75px;">
    <div class="d-flex align-items-center">
        <i class='bx bx-menu'></i>
        <span class="nav-link ms-3">
            Halo,
            @auth
                {{ Auth::user()->name }} <!-- Menampilkan nama pengguna yang login -->
            @else
                Tamu <!-- Teks default untuk pengguna yang tidak login -->
            @endauth
        </span>
    </div>

    @can('view-bookmarks')
        <!-- Form for Search -->
        <form class="d-flex ms-auto" action="{{ route('file-folder-search.index') }}" method="get">
            <div class="input-group">
                <input type="search" name="query" class="form-control" placeholder="Search..."
                    value="{{ old('query', $query ?? '') }}">
                <button type="submit" class="btn search-btn">
                    <i class='bx bx-search'></i>
                </button>
            </div>
        </form>
    @endcan

    <!-- Profile Dropdown -->
    <div class="d-flex justify-content-end">
        <div class="profile ms-3">
            <div class="dropdown">
                <!-- Trigger Button -->
                <a class="profile cursor-pointer" id="dropdownMenuButton" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <img src="{{ Auth::user()->avatar ? route('profile.avatar.show', ['filename' => Auth::user()->avatar]) : asset('/images/avatar.png') }}"
                        class="rounded-circle" alt="{{ Auth::user()->name }}" width="32" height="32">
                </a>
                <!-- Dropdown Menu -->
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                    <li>
                        <!-- Profile Link -->
                        <a class="dropdown-item d-flex justify-content-between align-items-center"
                            href="{{ route('profile.edit') }}">
                            <span class="ps-2">Profil</span>
                            <i class="fas fa-user"></i>
                        </a>
                    </li>
                    <hr class="profile-divider">
                    <li>
                        <!-- Logout Form -->
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit"
                                class="dropdown-item d-flex justify-content-between align-items-center">
                                Keluar
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
