<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage as StorageFacade;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, Prunable;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'role' => UserRole::USER,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'name',
        'password',
        'avatar',
        'role',
        'is_active',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            $avatar = $user->avatar;
            $avatarPath = 'avatars/'.$avatar;

            if ($avatar && StorageFacade::disk('local')->exists($avatarPath)) {
                StorageFacade::disk('local')->delete($avatarPath);
            }

            $path = 'drive'.'/'.$user->id;

            if (StorageFacade::disk('local')->exists($path)) {
                StorageFacade::disk('local')->deleteDirectory($path);
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Specify the conditions that determine which models should be pruned.
     */
    public function prunable(): Builder
    {
        return static::whereNull('email_verified_at')
            ->where('created_at', '<', now()->subDay());
    }

    /**
     * Prepare the model for pruning.
     */
    protected function pruning(): void
    {
        $avatar = $this->avatar;
        $avatarPath = 'avatars/'.$avatar;

        if ($avatar && StorageFacade::disk('local')->exists($avatarPath)) {
            StorageFacade::disk('local')->delete($avatarPath);
        }
    }

    public function storage()
    {
        return $this->hasOne(Storage::class);
    }

    public function storageRequests()
    {
        return $this->hasMany(StorageRequest::class);
    }

    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    public function userFolderAccesses()
    {
        return $this->hasMany(UserFolderAccess::class);
    }

    public function folderBookmarks()
    {
        return $this->hasMany(FolderBookmark::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function userFileAccesses()
    {
        return $this->hasMany(UserFileAccess::class);
    }

    public function fileBookmarks()
    {
        return $this->hasMany(FileBookmark::class);
    }
}
