<?php

namespace App\Services;

use App\Contracts\MasterDataItemInterface;

class MasterDataItems extends Common
{
    protected $masterDataItem;

    public function __construct(MasterDataItemInterface $masterDataItem)
    {
        parent::__construct($masterDataItem);

        $this->masterDataItem = $masterDataItem;
    }
}
