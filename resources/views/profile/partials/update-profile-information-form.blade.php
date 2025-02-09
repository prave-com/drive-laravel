<section>
    <header class="mb-10">
        <h2 class="text-2xl font-bold text-white">
            {{ __('Profile Information') }}
        </h2>

    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="d-flex justify-content-center align-items-center" style="min-height: 20vh;">
            <div class="d-flex flex-column align-items-center w-50 rounded-3">
                <!-- Gambar preview -->
                <div class="position-relative" style="width: 150px; height: 150px;">
                    <img id="avatar-preview"
                        src="{{ $user->avatar ? route('profile.avatar.show', ['filename' => $user->avatar]) : asset('/images/avatar.png') }}"
                        alt="Avatar" class="w-100 h-100 rounded-circle border-4 border-white"
                        style="object-fit: cover;" />

                    <!-- Icon edit -->
                    <label for="avatar" class="position-absolute"
                        style="bottom: -10px; right: -10px; cursor: pointer;">
                        <i class="bi bi-pencil-fill text-[#1e1e1e] text-base hover:text-white"></i>
                    </label>

                    <!-- Input file -->
                    <input id="avatar" name="avatar" type="file" accept="image/*" onchange="previewAvatar(event)"
                        style="display: none;">
                </div>

                <!-- Nama user -->
                <p class="mt-4 text-white text-sm md:text-xl font-bold">Halo, {{ $user->name }}</p>

                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            </div>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full text-gray-800"
                :value="old('email', $user->email)" required autocomplete="email" disabled />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full text-gray-800"
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <script>
            function previewAvatar(event) {
                const input = event.target;
                const preview = document.getElementById('avatar-preview');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.src = e.target.result; // Update src with selected file
                    };

                    reader.readAsDataURL(input.files[0]); // Read file as data URL
                }
            }
        </script>

        <div class="flex justify-end items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
