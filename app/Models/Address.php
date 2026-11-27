<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends BaseModel
{
    protected $fillable = [
        'country_id', 'state_id', 'district_id', 'township_id', 'ward_id', 'street_id',
        'address_line_1', 'address_line_2', 'building_name', 'unit_number',
        'postal_code', 'landmark', 'latitude', 'longitude', 'is_default',
    ];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function addressable(): MorphTo { return $this->morphTo(); }
    public function country(): BelongsTo { return $this->belongsTo(Country::class); }
    public function state(): BelongsTo { return $this->belongsTo(State::class); }
    public function district(): BelongsTo { return $this->belongsTo(District::class); }
    public function township(): BelongsTo { return $this->belongsTo(Township::class); }
    public function ward(): BelongsTo { return $this->belongsTo(Ward::class); }
    public function street(): BelongsTo { return $this->belongsTo(Street::class); }
}
