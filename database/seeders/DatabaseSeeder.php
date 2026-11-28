<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment(['local', 'testing'])) {
            $this->call(LocalRbacSeeder::class);
            $this->call(MessageSeeder::class);
            $this->call(MasterDataCategorySeeder::class);
            $this->call(MasterDataItemSeeder::class);
            $this->call(MyanmarAdministrativeDivisionSeeder::class);
        }
    }
}
