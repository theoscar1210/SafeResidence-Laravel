<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExitRecord extends Model
{
    protected $table = 'exits';

    protected $fillable = [
        'entry_id',
        'exited_at',
        'exited_by',
        'observations',
    ];

    protected function casts(): array
    {
        return [
            'exited_at' => 'datetime',
        ];
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }
}
