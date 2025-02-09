<?php

namespace App\Models;

use App\Enums\StorageRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageRequest extends Model
{
    use HasFactory;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => StorageRequestStatus::PENDING,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'request_quota',
        'reason',
        'status',
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
            'request_quota' => 'integer',
            'status' => StorageRequestStatus::class,
        ];
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
