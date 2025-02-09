@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-5 mt-5">
            <h3>Daftar Pengguna</h3>

            <!-- Search Form -->
            <form method="GET" action="{{ route('folders.index') }}" class="d-flex">
                <div class="input-group" style="border: 2px solid #7f7f7f; border-radius: 20px; overflow: hidden;">
                    <button class="btn border-none bg-transparent" type="submit"
                        style="border: none; border-radius: 20px 0 0 20px;">
                        <i class='bx bx-search-alt file-icon text-secondary fw-bold'></i>
                    </button>
                    <input type="text" class="form-control border-none" name="search" placeholder="Cari"
                        value="{{ $search ?? '' }}"
                        style="max-width: 300px; border: none; outline: none; box-shadow: none;">

                    <!-- Close button to clear the search -->
                    @if (!empty($search))
                        <a href="{{ route('folders.index') }}" class="btn btn-link text-secondary align-middle"
                            style="border-radius: 20px 0 0 20px;">
                            <i class='bx bx-x'></i> <!-- Close icon to clear the search -->
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <table class="table table-borderless table-hover">
            <thead class="thead-dark"
                style="border-bottom: 3px solid #ddd; position: relative; box-shadow: 0 6px 4px -4px rgba(0, 0, 0, 0.3); z-index: 1;">
                <tr>
                    <th class="fw-semibold text-dark" style="padding: 8px 12px; width: 10%;">Profil</th>
                    <th class="fw-semibold text-dark" style="padding: 8px 12px; width: 15%;">Email</th>
                    <th class="fw-semibold text-dark" style="padding: 8px 12px; width: 25%;">Jalur Root</th>
                    <th class="fw-semibold text-dark" style="padding: 8px 12px; width: 25%;">Pemakaian</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($folders as $folder)
                    <tr>
                        <td class="text-center align-middle" style="border-bottom: 1px solid #ddd; table-layout: fixed;">
                            <!-- Foto profil pengguna pemilik folder -->
                            <a href="{{ route('superadmin.users.edit', $folder->owner) }}">
                                <img src="{{ $folder->owner->avatar ? route('profile.avatar.show', ['filename' => $folder->owner->avatar]) : asset('/images/avatar.png') }}"
                                    alt="Profile" class="rounded-circle"
                                    style="width: 40px; height: 40px; object-fit: cover;">
                            </a>
                        </td>
                        <td class="align-middle"
                            style="border-bottom: 1px solid #ddd; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $folder->owner->email }}
                        </td>
                        <td class="align-middle" style="border-bottom: 1px solid #ddd;">
                            <a href="{{ route('folders.show', $folder) }}"
                                class="text-decoration-none text-dark hover-underline">
                                {{ $folder->owner->name }} /...
                            </a>
                        </td>

                        <td class="align-middle" style="border-bottom: 1px solid #ddd;">
                            {{ number_format($folder->size / 1024 / 1024 / 1024, 2) }} GB</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
