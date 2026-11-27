<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\MasterDataCategories;

class MasterDataCategoryController extends Controller
{
    protected $masterDataCategories;

    public function __construct(MasterDataCategories $masterDataCategories)
    {
        $this->masterDataCategories = $masterDataCategories;

        parent::__construct($this->masterDataCategories);
    }
}
