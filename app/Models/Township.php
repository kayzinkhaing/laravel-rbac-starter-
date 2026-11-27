<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Township extends BaseModel
{
    protected $fillable = ['district_id', 'source_id', 'name', 'name_local', 'slug', 'latitude', 'longitude'];

    public function district(): BelongsTo { return $this->belongsTo(District::class); }
    public function wards(): HasMany { return $this->hasMany(Ward::class); }
    public function streets(): HasMany { return $this->hasMany(Street::class); }
}
