<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    @section('content')
        <div class="py-12 bg-[#828282] text-white min-h-screen">
            <div class="container d-flex flex-column justify-content-center align-items-center">
                <div class="p-4 sm:p-8 bg-[#828282] rounded-lg w-full md:w-1/2 mb-4">
                    @include('profile.partials.update-profile-information-form')
                </div>

                <div class="p-4 sm:p-8 bg-[#828282] rounded-lg w-full md:w-1/2 mb-4">
                    @include('profile.partials.update-password-form')
                </div>

                <div class="p-4 sm:p-8 bg-[#828282] rounded-lg w-full md:w-1/2 mb-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    @endsection('content')
</x-app-layout>
