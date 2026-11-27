<?php

namespace App\Repositories;

use App\Contracts\MasterDataItemInterface;

class MasterDataItemRepository extends BaseRepository implements MasterDataItemInterface
{
    public function __construct()
    {
        parent::__construct('MasterDataItem');
    }
}
