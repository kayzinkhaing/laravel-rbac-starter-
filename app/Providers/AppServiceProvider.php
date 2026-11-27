<?php

namespace App\Providers;

use App\Contracts\ClientProfileInterface;
use App\Contracts\CommonTableInterface;
use App\Contracts\MasterDataCategoryInterface;
use App\Contracts\MasterDataItemInterface;
use App\Contracts\MediaInterface;
use App\Contracts\messageInterface;
use App\Contracts\OrganizationInterface;
use App\Contracts\permissionInterface;
use App\Contracts\roleInterface;
use App\Contracts\UserInterface;
use App\Repositories\ClientProfileRepository;
use App\Repositories\CommonTableRepository;
use App\Repositories\MasterDataCategoryRepository;
use App\Repositories\MasterDataItemRepository;
use App\Repositories\MediaRepository;
use App\Repositories\messageRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\permissionRepository;
use App\Repositories\UserRepository;
use App\Repositories\roleRepository;
use App\Services\configFiles;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(roleInterface::class, roleRepository::class);
        $this->app->bind(permissionInterface::class, permissionRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(messageInterface::class, messageRepository::class);
        $this->app->bind(OrganizationInterface::class, OrganizationRepository::class);
        $this->app->bind(ClientProfileInterface::class, ClientProfileRepository::class);
        $this->app->bind(CommonTableInterface::class, CommonTableRepository::class);
        $this->app->bind(MasterDataCategoryInterface::class, MasterDataCategoryRepository::class);
        $this->app->bind(MasterDataItemInterface::class, MasterDataItemRepository::class);
        $this->app->bind(MediaInterface::class, MediaRepository::class);
        $this->app->bind(configFiles::class);
    }

    public function boot(): void
    {
        require_once app_path('Support/helpers.php');
    }
}
