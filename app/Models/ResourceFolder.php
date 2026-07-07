<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResourceFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'parent_id',
        'name',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ResourceFolder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ResourceFolder::class, 'parent_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(ModuleResource::class, 'folder_id');
    }

    public function classSessions(): HasMany
    {
        return $this->hasMany(ClassSession::class, 'resource_folder_id');
    }

    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }
}