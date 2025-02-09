@extends('layouts.app')

@section('content')
    <style>
        .hover-effect {
            transition: all 0.3s ease-in-out;
            /* Efek transisi halus pada hover */
        }

        .hover-effect:hover {
            background-color: #10453A !important;
            /* Warna hijau yang lebih cerah */
            color: white !important;
            /* Pastikan teks tetap putih */
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            /* Tambahkan efek bayangan lebih kuat untuk efek timbul */
            transform: translateY(-3px);
            /* Sedikit angkat tombol agar terlihat timbul */
        }

        .hover-effect:hover .bg-white {
            background-color: #e0ffe6 !important;
            /* Lingkaran berubah warna hijau pucat */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Lingkaran dengan efek bayangan */
        }

        .hover-effect:hover .fa-user-plus {
            transform: scale(1.2);
            /* Ikon sedikit membesar */
            transition: transform 0.3s ease-in-out;
            /* Efek transisi yang halus */
        }
    </style>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
            <h3>Manajemen Pengguna</h3>

            <form method="GET" action="{{ route('superadmin.users.index') }}" class="d-flex">
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
                        <a href="{{ route('superadmin.users.index') }}" class="btn btn-link text-secondary align-middle"
                            style="border-radius: 20px 0 0 20px;">
                            <i class='bx bx-x'></i> <!-- Close icon to clear the search -->
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <a href="{{ route('superadmin.users.create') }}"
            class="btn bg-success bg-opacity-100 text-white rounded-pill d-flex align-items-center justify-content-between p-0 mb-3 hover-effect"
            style="width: 160px; height: 40px;">
            <!-- Text "Buat User" -->
            <span class="fw-semibold ms-3">Buat User</span>

            <!-- Ikon dalam Lingkaran -->
            <span class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center me-0"
                style="width: 35px; height: 35px;">
                <i class="fa-solid fa-user-plus"></i>
            </span>
        </a>

        <table class="table table-borderless table-hover">
            <thead
                style="border-bottom: 3px solid #ddd; position: relative; box-shadow: 0 6px 4px -4px rgba(0, 0, 0, 0.3); z-index: 1;">
                <tr>
                    <th class="fw-semibold text-dark" style="width: 6%;">Profil</th>
                    <th class="fw-semibold text-dark" style="width: 15%;">Nama</th>
                    <th class="fw-semibold text-dark" style="width: 20%;">Email</th>
                    <th class="fw-semibold text-dark" style="width: 8%;">Role</th>
                    <th class="fw-semibold text-dark" style="width: 40%;">Kuota</th>
                    <th class="fw-semibold text-dark">Status</th>
                    <th class="fw-semibold text-dark"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <!-- Kolom Profil -->
                        <td class="text-center align-middle" style="border-bottom: 1px solid #ddd;">
                            <img src="{{ $user->avatar ? route('profile.avatar.show', ['filename' => $user->avatar]) : asset('/images/avatar.png') }}"
                                class="rounded-circle d-block mx-auto" alt="{{ $user->name }}"
                                style="width: 40px; height: 40px; object-fit: cover;">
                        </td>
                        <!-- Kolom Nama -->
                        <td class="text-truncate"
                            style="border-bottom: 1px solid #ddd; max-width: 45px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                            title="{{ $user->name }}">
                            {{ $user->name }}
                        </td>

                        <!-- Kolom Email -->
                        <td class="text-truncate"
                            style="border-bottom: 1px solid #ddd; max-width: 20px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                            title="{{ $user->email }}">
                            {{ $user->email }}
                        </td>
                        <!-- Kolom Role -->
                        <td style="border-bottom: 1px solid #ddd;" class="align-middle">
                            @switch($user->role)
                                @case(App\Enums\UserRole::SUPERADMIN)
                                    <span class="badge bg-danger text-white">SuperAdmin</span>
                                @break

                                @case(App\Enums\UserRole::ADMIN)
                                    <span class="badge bg-warning text-white">Admin</span>
                                @break

                                @case(App\Enums\UserRole::USER)
                                    <span class="badge bg-primary text-white">User</span>
                                @break

                                @default
                                    <span class="badge bg-secondary text-white">Unknown Role</span>
                            @endswitch
                        </td>
                        <!-- Kolom Progress Bar untuk Kuota -->
                        <td style="border-bottom: 1px solid #ddd; width: 30%;" class="align-middle">
                            <div class="d-flex align-items-center"
                                style="border: 1px solid #ccc; border-radius: 20px; padding: 5px; background-color: #fff;">

                                <div class="progress"
                                    style="flex: 1; height: 12px; background-color: #f5f5f5; border-radius: 20px; padding: 3px; margin-right: 10px;">

                                    <!-- Actual Progress Bar -->
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $user->storage ? ($user->storage->used_quota / $user->storage->total_quota) * 100 : 0 }}%; 
                                        border-radius: 16px; color: white; font-weight: bold; font-size: 5px; display: flex; align-items: center; justify-content: center;">
                                        {{ $user->storage ? round(($user->storage->used_quota / $user->storage->total_quota) * 100) : 0 }}%
                                    </div>
                                </div>

                                <!-- Storage Info (Total Storage in GB) -->
                                <div class="fw-semibold text-dark" style="font-size: 10px; margin-right: 10px">
                                    {{ $user->storage ? number_format($user->storage->total_quota / 1024 ** 3, 2) : '0.00' }}
                                    GB
                                </div>
                            </div>
                        </td>
                        <!-- Kolom Status -->
                        <td style="border-bottom: 1px solid #ddd; width: 15%;" class="align-middle">
                            @if ($user->is_active)
                                <span class="badge bg-success text-white">Aktif</span>
                            @else
                                <span class="badge bg-danger text-white">Tidak Aktif</span>
                            @endif
                        </td>
                        <!-- Kolom Aksi -->
                        <td style="border-bottom: 1px solid #ddd; width: 15%;" class="align-middle">
                            <!-- Ikon Dropdown -->
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm dropdown-toggle-split" type="button"
                                    id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <!-- Link Edit -->
                                    <li>
                                        <a class="dropdown-item" href="{{ route('superadmin.users.edit', $user) }}">
                                            <i class="fa-solid fa-user-pen"></i>Edit
                                        </a>
                                    </li>
                                    <!-- Form Delete -->
                                    <li>
                                        <form action="{{ route('superadmin.users.destroy', $user) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item btn btn-danger btn-sm text-danger">
                                                <i class="fa-solid fa-user-minus text-danger"></i>
                                                Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection
