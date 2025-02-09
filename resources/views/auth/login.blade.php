<x-guest-layout>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="input-box" style="width: 330px; box-sizing: border-box;">
            <h2 class="title text-center mb-5" style="color: #10453A;">MASUK</h2>
            <p class="description text-center mb-5" style="color: #10453A;">Masuk dengan email kepegawaian Laravel</p>

            <!-- Email Address -->
            <div class="input-field d-flex position-relative" style="flex-direction: column; padding: 0 10px 0 10px;">
                <input type="email" class="input" id="email" name="email" placeholder="@example.com"
                    :value="old('email')" required autofocus autocomplete="username"
                    style="height: 45px; width: 100%; background: transparent; border: none; margin-bottom: 20px; color: #40414a; outline: none; border-bottom: 1px solid rgba(0, 0, 0, 0.2);">
                <label for="email" class="position-absolute"
                    style="top: -7px; left: 10px; pointer-events: none; transition: 0.5s; color: #10453A;">Email</label>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="input-field d-flex position-relative" style="flex-direction: column; padding: 0 10px 0 10px;">
                <input type="password" class="input" id="password" name="password" required
                    autocomplete="current-password"
                    style="height: 45px; width: 100%; background: transparent; border: none; margin-bottom: 20px; color: #40414a; outline: none; border-bottom: 1px solid rgba(0, 0, 0, 0.2);">
                <label for="password" class="position-absolute"
                    style="top: -7px; left: 10px; pointer-events: none; transition: 0.5s; color: #10453A;">
                    Password
                </label>
                <img src="assets/img/hidden.png" alt="Show Password" class="toggle-password" id="togglePassword"
                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; cursor: pointer;"
                    onmouseover="this.style.transform='translateY(-50%) scale(1.2)';"
                    onmouseout="this.style.transform='translateY(-50%) scale(1)';">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <!-- Links for Forgot Password and Register -->
            <div class="d-flex justify-content-end mt-2 mb-2" style="font-size: small;">
                {{-- Daftar --}}
                <a href="{{ route('register') }}" style="text-decoration: none; font-weight: 700; color: #9D9D9D;"
                    onmouseover="this.style.textDecoration='underline'; this.style.color='#000000'"
                    onmouseout="this.style.textDecoration='none'; this.style.color='#9D9D9D'">Daftar</a>

                <span class="text-gray-600" style="margin-left: 5px; margin-right: 5px;">|</span>

                {{-- Lupa Password --}}
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        style="text-decoration: none; font-weight: 700; color: #9D9D9D;"
                        onmouseover="this.style.textDecoration='underline'; this.style.color='#000000'"
                        onmouseout="this.style.textDecoration='none'; this.style.color='#9D9D9D'">Lupa Password?</a>
                @endif

            </div>
            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn"
                    style="background-color: white; border: 1px solid #083D30; border-radius: 20px; padding: 10px 20px; font-weight: 500; color: #083D30; width: 100%; max-width: 200px;"
                    onmouseover="this.style.backgroundColor='#083D30'; this.style.color='white'"
                    onmouseout="this.style.backgroundColor='white'; this.style.color='#083D30'">MASUK</button>
            </div>
        </div>
    </form>

</x-guest-layout>
