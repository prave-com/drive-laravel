@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h2 class="mb-4 text-center text-uppercase fw-bold" style="color: #004d40; font-family: 'Arial', sans-serif;">Edit
            User</h2>

        <form action="{{ route('superadmin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <!-- Name Field -->
            <div class="form-group mb-4">
                <label for="name" class="fw-bold" style="color: #004d40">Nama</label>
                <input type="text" name="name" id="name" class="form-control shadow-lg border-success"
                    value="{{ old('name', $user->name) }}" required>
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger" />
            </div>

            <!-- Email Field -->
            <div class="form-group mb-4">
                <label for="email" class="fw-bold" style="color: #004d40">Email</label>
                <input type="email" name="email" id="email" class="form-control shadow-lg border-info"
                    value="{{ old('email', $user->email) }}" required>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
            </div>

            <!-- Password Field (Optional) -->
            <div class="form-group mb-4">
                <label for="password" class="fw-bold" style="color: #004d40">Password</label>
                <input type="password" name="password" id="password" class="form-control shadow-lg border-danger">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password</small>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
            </div>

            <!-- Password Confirmation Field -->
            <div class="form-group mb-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="fw-bold" style="color: #004d40" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full form-control shadow-lg border-danger"
                    type="password" name="password_confirmation" autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger" />
            </div>

            <!-- Role Field -->
            <div class="form-group mb-4">
                <label for="role" class="fw-bold" style="color: #004d40">Role</label>
                <select name="role" id="role" class="form-control shadow-lg border-primary" required>
                    <option value="{{ App\Enums\UserRole::USER->value }}"
                        {{ $user->role === App\Enums\UserRole::USER ? 'selected' : '' }}>User</option>
                    <option value="{{ App\Enums\UserRole::ADMIN->value }}"
                        {{ $user->role === App\Enums\UserRole::ADMIN ? 'selected' : '' }}>Admin</option>
                    <option value="{{ App\Enums\UserRole::SUPERADMIN->value }}"
                        {{ $user->role === App\Enums\UserRole::SUPERADMIN ? 'selected' : '' }}>Superadmin</option>
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-2 text-danger" />
            </div>

            <!-- Active Status -->
            <div class="form-group mb-4">
                <label for="is_active" class="fw-bold" style="color: #004d40">Status</label><br>

                <!-- Radio Button untuk Active -->
                <input type="radio" name="is_active" value="1" id="active"
                    {{ old('is_active', $user->is_active) == 1 ? 'checked' : '' }}
                    class="form-check-input me-2 active-status">
                <label for="active" class="me-3" style="color: #004d40; font-weight: bold;">Aktif</label>

                <!-- Radio Button untuk Inactive -->
                <input type="radio" name="is_active" value="0" id="inactive"
                    {{ old('is_active', $user->is_active) == 0 ? 'checked' : '' }}
                    class="form-check-input me-2 inactive-status">
                <label for="inactive" style="color: #dc3545; font-weight: bold;">Tidak Aktif</label>

                <x-input-error :messages="$errors->get('is_active')" class="mt-2 text-danger" />
            </div>

            <!-- Avatar Upload -->
            <div class="form-group mb-4">
                <label for="avatar" class="fw-bold" style="color: #004d40">Avatar</label>
                <input type="file" name="avatar" id="avatar" class="form-control shadow-lg border-info">

                @if ($user->avatar)
                    <div class="mt-2">
                        <label>Current Avatar</label><br>
                        <img src="{{ route('profile.avatar.show', ['filename' => $user->avatar]) }}" alt="Avatar"
                            class="img-thumbnail" width="100">
                    </div>
                @endif

                <x-input-error :messages="$errors->get('avatar')" class="mt-2 text-danger" />
            </div>

            <!-- Submit Button -->
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-gradient btn-lg px-4 py-2"
                    style="background-color: #004d40; color: white; border: none; border-radius: 5px;">Update</button>
            </div>
        </form>
    </div>

    <style>
        .btn-gradient:hover {
            background-color: #00332e;
            /* Warna lebih gelap saat hover */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .form-control {
            border-radius: 10px;
            transition: all 0.3s ease-in-out;
        }

        .form-control:focus {
            border-color: #004d40;
            box-shadow: 0 0 8px rgba(0, 77, 64, 0.8);
        }

        .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
        }

        .form-check-input:checked+label {
            color: #28a745;
        }

        /* Custom Radio Button Colors */
        .active-status:checked {
            background-color: #004d40;
            border-color: #004d40;
        }

        .inactive-status:checked {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .active-status:checked+label {
            color: #004d40;
        }

        .inactive-status:checked+label {
            color: #dc3545;
        }
    </style>
@endsection
