<?php

return [
    'models' => [
    App\Models\User::class => [
        'roles' => ['label' => 'name'],
        'media' => ['label' => 'url'],
        'clientProfiles' => ['label' => 'id'],
        'eager_load' => [
            'roles:id,name,guard_name',
            'roles.permissions:id,name,guard_name',
            'media:id,mediable_id,mediable_type,url',
            'clientProfiles:id,user_id,organization_id',
            'clientProfiles.organization:id,name',
        ],
    ],

    App\Models\Role::class => [
        'permissions' => ['label' => 'name'],
        'users' => ['label' => 'name'],
        'eager_load' => [
            'permissions:id,name,guard_name',
            'users:id,first_name,middle_name,last_name,email',
        ],
    ],

    App\Models\Permission::class => [
        'roles' => ['label' => 'name'],
        'eager_load' => [
            'roles:id,name,guard_name',
        ],
    ],

    App\Models\Organization::class => [
        'clientProfiles' => ['label' => 'user.name'],
        'eager_load' => [
            'clientProfiles:id,user_id,organization_id',
            'clientProfiles.user:id,first_name,middle_name,last_name,email',
        ],
    ],

    App\Models\ClientProfile::class => [
        'user' => ['label' => 'name'],
        'organization' => ['label' => 'name'],
        'eager_load' => [
            'user:id,first_name,middle_name,last_name,email',
            'organization:id,name',
        ],
    ],

    App\Models\Media::class => [
        'mediable' => ['label' => 'id'],
        'eager_load' => ['mediable'],
    ],

    App\Models\Message::class => [
        'eager_load' => [],
    ],

    App\Models\CommonTable::class => [
        'eager_load' => [],
    ],

    App\Models\MasterDataCategory::class => [
        'items' => ['label' => 'value'],
        'eager_load' => [
            'items:id,master_data_category_id,key,value,label,is_active,sort_order',
        ],
    ],

    App\Models\MasterDataItem::class => [
        'category' => ['label' => 'name'],
        'eager_load' => [
            'category:id,key,name',
        ],
    ],
    ],
];
