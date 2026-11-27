<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ward extends BaseModel
{
    protected $fillable = ['township_id', 'source_id', 'name', 'name_local', 'slug', 'postal_code', 'latitude', 'longitude'];

    public function township(): BelongsTo { return $this->belongsTo(Township::class); }
    public function streets(): HasMany { return $this->hasMany(Street::class); }
}
