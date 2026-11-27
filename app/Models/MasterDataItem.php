<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterDataItem extends BaseModel
{
    protected $fillable = [
        'master_data_category_id',
        'key',
        'value',
        'label',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'master_data_category_id' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(MasterDataCategory::class, 'master_data_category_id');
    }
}
