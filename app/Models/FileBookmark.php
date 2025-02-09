<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileBookmark extends Model
{
    use HasFactory;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_starred' => false,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'is_starred',
        'file_id',
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
            'is_starred' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include bookmarked files.
     */
    public function scopeBookmarked(Builder $query): void
    {
        $query->where('is_starred', true);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
