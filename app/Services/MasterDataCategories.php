<?php

namespace App\Services;

use App\Contracts\MasterDataCategoryInterface;

class MasterDataCategories extends Common
{
    protected $masterDataCategory;

    public function __construct(MasterDataCategoryInterface $masterDataCategory)
    {
        parent::__construct($masterDataCategory);

        $this->masterDataCategory = $masterDataCategory;
    }
}
