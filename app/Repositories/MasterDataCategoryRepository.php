<?php

namespace App\Repositories;

use App\Contracts\MasterDataCategoryInterface;

class MasterDataCategoryRepository extends BaseRepository implements MasterDataCategoryInterface
{
    public function __construct()
    {
        parent::__construct('MasterDataCategory');
    }
}
