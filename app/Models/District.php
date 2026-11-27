<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends BaseModel
{
    protected $fillable = ['state_id', 'source_id', 'name', 'name_local', 'slug', 'latitude', 'longitude'];

    public function state(): BelongsTo { return $this->belongsTo(State::class); }
    public function townships(): HasMany { return $this->hasMany(Township::class); }
}
