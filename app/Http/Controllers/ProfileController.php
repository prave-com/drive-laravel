<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $oldAvatar = $request->user()->avatar;
        $request->user()->fill($request->validated());

        if ($request->hasFile('avatar')) {
            $oldAvatarPath = 'avatars/'.$oldAvatar;

            // Delete the old avatar if it exists
            if ($oldAvatar && Storage::disk('local')->exists($oldAvatarPath)) {
                Storage::disk('local')->delete($oldAvatarPath);
            }

            $fileName = Str::uuid()->toString().'.'.$request->avatar->extension();
            $request->file('avatar')->storeAs('avatars', $fileName);
            $request->user()->avatar = $fileName;
        }

        $request->user()->save();

        activity()
            ->causedBy($request->user())
            ->performedOn($request->user())
            ->event('updated')
            ->log("Update profil {$request->user()->email}");

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->event('deleted')
            ->log("Delete akun {$user->email}");

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Show the user's avatar.
     */
    public function showAvatar($filename): StreamedResponse
    {
        $path = 'avatars/'.$filename;

        if (Storage::disk('local')->exists($path)) {
            $headers = [
                'Content-Type' => File::mimeType(Storage::disk('local')->path($path)),
            ];

            return Storage::download($path, $filename, $headers);
        }

        abort(404);
    }
}
