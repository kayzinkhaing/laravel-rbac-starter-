<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\MasterDataItems;

class MasterDataItemController extends Controller
{
    protected $masterDataItems;

    public function __construct(MasterDataItems $masterDataItems)
    {
        $this->masterDataItems = $masterDataItems;

        parent::__construct($this->masterDataItems);
    }
}
