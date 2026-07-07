<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'folder_id',
        'uploaded_by',
        'title',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'last_modified_by',
        'last_modified_at',
    ];

    protected function casts(): array
    {
        return [
            'file_size'        => 'integer',
            'last_modified_at' => 'datetime',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(ResourceFolder::class, 'folder_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function lastModifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_modified_by');
    }
}