<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Street extends BaseModel
{
    protected $fillable = ['township_id', 'ward_id', 'name', 'name_local'];

    public function township(): BelongsTo { return $this->belongsTo(Township::class); }
    public function ward(): BelongsTo { return $this->belongsTo(Ward::class); }
}
