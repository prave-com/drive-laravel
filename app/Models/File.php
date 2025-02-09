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

class File extends Model
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
            Storage::disk('local')->delete($path);
        }
    }

    public function getFullPathAttribute()
    {
        return $this->parent->full_path.'/'.$this->name;
    }

    public function getSizeAttribute()
    {
        $path = 'drive'.$this->full_path;

        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->size($path);
        }

        return null;
    }

    public function getExtensionAttribute()
    {
        return pathinfo($this->name, PATHINFO_EXTENSION);
    }

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
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
