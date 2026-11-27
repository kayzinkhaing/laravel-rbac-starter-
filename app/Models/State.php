<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends BaseModel
{
    protected $fillable = ['country_id', 'source_id', 'name', 'name_local', 'slug', 'latitude', 'longitude'];

    public function country(): BelongsTo { return $this->belongsTo(Country::class); }
    public function districts(): HasMany { return $this->hasMany(District::class); }
}
