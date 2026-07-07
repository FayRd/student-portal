<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSession extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'module_id',
        'resource_folder_id',
        'title',
        'location',
        'starts_at',
        'ends_at',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at'   => 'datetime',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function resourceFolder(): BelongsTo
    {
        return $this->belongsTo(ResourceFolder::class);
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }
}