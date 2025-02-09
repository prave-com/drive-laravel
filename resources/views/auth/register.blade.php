<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="input-box" style="width: 330px; box-sizing: border-box;">
            <h2 class="title text-center mb-4" style="color: #10453A;">DAFTAR</h2>

            <!-- Nama -->
            <div class="input-field d-flex position-relative" style="flex-direction: column; padding: 0 10px 0 10px;">
                <input type="text" class="input" id="name" name="name" :value="old('name')" required
                    autocomplete="name" autocomplete="off"
                    style="height: 45px; width: 100%; background: transparent; border: none; margin-bottom: 20px; color: #40414a; outline: none; border-bottom: 1px solid rgba(0, 0, 0, 0.2);">
                <label for="nama" class="position-absolute"
                    style="top: -7px; left: 10px; pointer-events: none; transition: 0.5s; color: #10453A;">Nama</label>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="input-field d-flex position-relative" style="flex-direction: column; padding: 0 10px 0 10px;">
                <input type="email" class="input" id="email" name="email" :value="old('email')" required
                    autofocus autocomplete="username"
                    style="height: 45px; width: 100%; background: transparent; border: none; margin-bottom: 20px; color: #40414a; outline: none; border-bottom: 1px solid rgba(0, 0, 0, 0.2);">
                <label for="email" class="position-absolute"
                    style="top: -7px; left: 10px; pointer-events: none; transition: 0.5s; color: #10453A;">Email</label>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="input-field d-flex position-relative" style="flex-direction: column; padding: 0 10px 0 10px;">
                <input type="password" class="input" id="password" name="password" required
                    autocomplete="new-password"
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

            <!-- Confirm Password -->
            <div class="input-field d-flex position-relative" style="flex-direction: column; padding: 0 10px 0 10px;">
                <input type="password" class="input" id="password_confirmation" name="password_confirmation" required
                    autocomplete="new-pass word"style="height: 45px; width: 100%; background: transparent; border: none; margin-bottom: 20px; color: #40414a; outline: none; border-bottom: 1px solid rgba(0, 0, 0, 0.2);">
                <label for="password_confirmation" class="position-absolute"
                    style="top: -7px; left: 10px; pointer-events: none; transition: 0.5s; color: #10453A;">Konfirmasi
                    Password</label>
                <img src="assets/img/hidden.png" alt="Show Password" class="toggle-password" id="toggleConfirmPass"
                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; cursor: pointer;"
                    onmouseover="this.style.transform='translateY(-50%) scale(1.2)';"
                    onmouseout="this.style.transform='translateY(-50%) scale(1)';">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Link Masuk -->
            <div class="d-flex justify-content-end mt-2 mb-2" style="font-size: small;">
                <a href="{{ route('login') }}" style="text-decoration: none; font-weight: 700; color: #9D9D9D;"
                    onmouseover="this.style.textDecoration='underline'; this.style.color='#000000'"
                    onmouseout="this.style.textDecoration='none'; this.style.color='#9D9D9D'">Masuk</a>
            </div>

            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn"
                    style="background-color: white; border: 1px solid #083D30; border-radius: 20px; padding: 10px 20px; font-weight: 500; color: #083D30; width: 100%; max-width: 200px;"
                    onmouseover="this.style.backgroundColor='#083D30'; this.style.color='white'"
                    onmouseout="this.style.backgroundColor='white'; this.style.color='#083D30'">DAFTAR</button>
            </div>
        </div>
    </form>
</x-guest-layout>
