<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage as StorageFacade;

class Storage extends Model
{
    use HasFactory;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'total_quota' => 3221225472,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'total_quota',
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
            'total_quota' => 'integer',
        ];
    }

    /**
     * Get the total bytes used.
     */
    public function getUsedQuotaAttribute(): int
    {
        $fileSize = 0;

        foreach (StorageFacade::allFiles('drive'.$this->owner->folders()->root()->first()->full_path) as $file) {
            $fileSize += StorageFacade::disk('local')->size($file);
        }

        return $fileSize;
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
