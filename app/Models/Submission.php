<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'user_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'status',
        'submitted_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'file_size'    => 'integer',
            'submitted_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function grade(): HasOne
    {
        return $this->hasOne(Grade::class);
    }

    public function isLate(): bool
    {
        return $this->submitted_at->gt($this->assignment->due_at);
    }
}