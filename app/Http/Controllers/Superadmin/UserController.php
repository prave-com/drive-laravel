<?php

namespace App\Http\Controllers\Superadmin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Folder;
use App\Models\Storage as StorageModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Make sure to import Request

class UserController extends Controller
{
    public function index(Request $request) // Accept the search query
    {
        // Get the search term from the query string
        $search = $request->get('search');

        // Modify the query to search by name or email
        $users = User::query()
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->with('storage') // Include the storage relationship
            ->get();

        return view('admin.users.index', compact('users', 'search')); // Pass the search term to the view
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $fileName = null;
        if ($request->hasFile('avatar')) {
            $fileName = Str::uuid()->toString().'.'.$request->avatar->extension();
            $request->file('avatar')->storeAs('avatars', $fileName);
        }

        $user = User::create([
            'email' => $request->email,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'avatar' => $fileName,
            'role' => $request->enum('role', UserRole::class),
            'is_active' => $request->boolean('is_active'),
            'email_verified_at' => now(),
        ]);

        // Menambahkan direktori untuk penyimpanan file pengguna
        $path = 'drive'.'/'.$user->id;
        if (! Storage::disk('local')->exists($path)) {
            Storage::makeDirectory($path);
        }

        // Membuat folder untuk pengguna jika belum ada
        if (! Folder::find($user->id)) {
            Folder::create([
                'name' => $user->id,
                'user_id' => $user->id,
            ]);
        }

        // Menambahkan storage untuk pengguna jika belum ada
        if (! StorageModel::find($user->id)) {
            $totalStorage = (int) env('TOTAL_STORAGE');
            $usedQuota = StorageModel::all()->sum('total_quota');
            $remainingQuota = $totalStorage - $usedQuota;
            $quotaToAllocate = max(0, min(3221225472, $remainingQuota));

            StorageModel::create([
                'total_quota' => $quotaToAllocate,
                'user_id' => $user->id,
            ]);
        }

        return redirect()->route('superadmin.users.index')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $oldAvatar = $user->avatar;

        $validated = $request->validated();

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($oldAvatar && Storage::exists('avatars/'.$oldAvatar)) {
                Storage::delete('avatars/'.$oldAvatar);
            }

            $fileName = Str::uuid()->toString().'.'.$request->avatar->extension();
            $request->file('avatar')->storeAs('avatars', $fileName);

            $validated['avatar'] = $fileName;
        }

        $user->update($validated);

        return redirect()->route('superadmin.users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('superadmin.users.index')->with('success', 'User deleted successfully');
    }
}
