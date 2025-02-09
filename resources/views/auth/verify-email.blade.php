<x-guest-layout>

    @section('title', 'verify-email')

    @section('content')

        <!-- Form mulai disini -->
        <div>
            <div class="input-box">
                <p class="description" style=" text-align: justify; color:#0B4F3F">
                    {{ __('Terima kasih telah mendaftar! Sebelum memulai, bisakah Anda memverifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan ke Anda? Jika Anda tidak menerima email tersebut, kami dengan senang hati akan mengirimkan yang lain.') }}
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ __('*Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.') }}
                    </div>
                @endif

                <div class="button-container" style="justify-content: flex-start;">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf

                        <button type="submit" class="btn-cs">KIRIM ULANG EMAIL VERIFIKASI</button>
                    </form>
                </div>

                <!-- Form logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-cs" style="background-color: #D9534F; color: #fff;">Keluar</button>
                </form>

            </div>

        </div>
        <!-- Form berakhir disini -->

        <style>
            .row-cs {
                width: 900px;
                height: 400px;
                display: flex;
                border-radius: 10px;
                background: #fff;
                padding: 0px;
                box-shadow: 5px 5px 10px 1px rgba(0, 0, 0, 0.2);
            }

            @media only screen and (max-width: 768px) {
                .row-cs {
                    height: 420px;
                }
            }

            .input-box-cs {
                width: 88%;
                height: 50px;
                box-sizing: content-box;
                margin-left: 60px;
                margin-top: 60px;
            }

            @media only screen and (max-width: 768px) {
                .input-box-cs {
                    width: 88%;
                    height: 50px;
                    box-sizing: content-box;
                    margin-top: 30px;
                    margin-left: auto;
                    margin-right: auto;
                }
            }

            .input-box-cs {
                width: 88%;
                height: 50px;
                box-sizing: content-box;
                margin-left: 60px;
                margin-top: 60px;
            }

            @media only screen and (max-width: 768px) {
                .input-box-cs {
                    width: 88%;
                    height: 50px;
                    box-sizing: content-box;
                    margin-top: 30px;
                    margin-left: auto;
                    margin-right: auto;
                }
            }

            .form-text {
                color: #7D7D7D;
                font-size: 20px;
                margin-bottom: 15px;
            }

            .form-label {
                font-size: 20px;
                font-weight: bold;
            }

            .form-control {
                border-radius: 5px;
                border: 1px solid #ddd;
                padding: 10px 15px;
                font-size: 1rem;
                background-color: #F8F8F8;
                color: #333;
                width: 99%;
            }

            .button-container {
                display: flex;
                justify-content: flex-end;
                padding: 20px 0;
            }

            .btn-cs {
                background-color: #0B4F3F;
                color: white;
                border: none;
                padding: 12px 20px;
                font-size: 0.95rem;
                font-weight: 600;
                border-radius: 5px;
                transition: background-color 0.3s;
            }

            .btn-cs:hover {
                background-color: #083D30;
            }

            .logout-container {
                width: 88%;
                box-sizing: content-box;
                margin-left: 60px;
                margin-top: 70px;
            }

            @media only screen and (max-width: 768px) {
                .logout-container {
                    margin-left: 17px;
                    margin-right: auto;
                    margin-top: auto;
                    margin-bottom: 10px;
                }
            }

            .logout-button {
                background-color: #D9534F;
                color: white;
                border: none;
                padding: 12px 20px;
                font-size: 0.95rem;
                font-weight: 600;
                border-radius: 5px;
                transition: background-color 0.3s;
            }

            .logout-button:hover {
                background-color: #B52A1A;
            }
        </style>
    </x-guest-layout>
