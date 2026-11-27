<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends BaseModel
{
    protected $fillable = ['iso2', 'iso3', 'name', 'name_local'];

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }
}
