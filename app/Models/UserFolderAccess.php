<?php

namespace App\Models;

use App\Enums\PermissionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFolderAccess extends Model
{
    use HasFactory;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'permission_type' => PermissionType::READ,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
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
        return ['permission_type' => PermissionType::class];
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
