<?php

namespace App\Models;

use App\Enums\PermissionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Folder extends Model
{
    use HasFactory, HasUuids, Prunable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'permission_type',
        'folder_id',
        'user_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'permission_type' => PermissionType::class,
        ];
    }

    /**
     * Specify the conditions that determine which models should be pruned.
     */
    public function prunable(): Builder
    {
        return static::onlyTrashed()
            ->where('deleted_at', '<=', now()->subMonth());
    }

    /**
     * Prepare the model for pruning.
     */
    protected function pruning(): void
    {
        $path = 'drive'.$this->full_path;

        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->deleteDirectory($path);
        }
    }

    /**
     * Scope a query to only include root folders.
     */
    public function scopeRoot(Builder $query): void
    {
        $query->whereNull('folder_id');
    }

    public function getIsRootAttribute()
    {
        return $this->parent == null;
    }

    public function getFullPathAttribute()
    {
        $path = [];

        $currentFolder = $this;
        while ($currentFolder) {
            $path[] = $currentFolder->name;
            $currentFolder = $currentFolder->parent;
        }

        return '/'.implode('/', array_reverse($path));
    }

    public function getAllParents()
    {
        $parents = [];

        $currentFolder = $this;
        while ($currentFolder->parent) {
            $parents[] = $currentFolder->parent;
            $currentFolder = $currentFolder->parent;
        }

        return array_reverse($parents);
    }

    public function getSizeAttribute()
    {
        $fileSize = 0;

        foreach (Storage::allFiles('drive'.$this->full_path) as $file) {
            $fileSize += Storage::disk('local')->size($file);
        }

        return $fileSize;
    }

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    public function children()
    {
        return $this->hasMany(Folder::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function userFolderAccesses()
    {
        return $this->hasMany(UserFolderAccess::class);
    }

    public function folderBookmarks()
    {
        return $this->hasMany(FolderBookmark::class);
    }
}
