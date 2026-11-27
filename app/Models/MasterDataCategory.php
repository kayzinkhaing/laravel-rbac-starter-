<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterDataCategory extends BaseModel
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MasterDataItem::class);
    }
}
