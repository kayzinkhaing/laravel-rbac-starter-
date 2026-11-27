<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MyanmarAdministrativeDivisionSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/myanmar-administrative-divisions.json');

        if (! is_file($path)) {
            throw new RuntimeException(
                "Dataset not found at {$path}. Download the official Open Admin Data JSON before seeding."
            );
        }

        $payload = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $data = $payload['data'] ?? null;

        if (! is_array($data)) {
            throw new RuntimeException('Unexpected dataset structure: expected a top-level data object.');
        }

        $regions = $data['region'] ?? [];
        $districts = $data['district'] ?? [];
        $townships = $data['township'] ?? [];
        $wards = $data['ward'] ?? [];

        $actual = [
            'region' => count($regions),
            'district' => count($districts),
            'township' => count($townships),
            'ward' => count($wards),
        ];

        $expected = ['region' => 15, 'district' => 77, 'township' => 491, 'ward' => 17043];

        if ($actual !== $expected) {
            throw new RuntimeException(
                'Dataset count validation failed. Expected '.json_encode($expected).' but received '.json_encode($actual)
            );
        }

        DB::transaction(function () use ($regions, $districts, $townships, $wards): void {
            DB::table('countries')->updateOrInsert(
                ['iso2' => 'MM'],
                [
                    'iso3' => 'MMR',
                    'name' => 'Myanmar',
                    'name_local' => 'မြန်မာ',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $countryId = DB::table('countries')->where('iso2', 'MM')->value('id');

            $this->importLevel('states', $regions, 'country_id', $countryId);
            $this->importLevel('districts', $districts, 'state_id');
            $this->importLevel('townships', $townships, 'district_id');
            $this->importLevel('wards', $wards, 'township_id');
        });
    }

    private function importLevel(string $table, array $records, string $parentColumn, ?int $fixedParentId = null): void
    {
        foreach (array_chunk($records, 500) as $chunk) {
            $rows = [];

            foreach ($chunk as $record) {
                $parentId = $fixedParentId;

                if ($fixedParentId === null) {
                    $parentSourceId = $record['parent_id'] ?? null;

                    if (! $parentSourceId) {
                        throw new RuntimeException("Missing parent_id for {$table} record {$record['id']}.");
                    }

                    $parentTable = match ($table) {
                        'districts' => 'states',
                        'townships' => 'districts',
                        'wards' => 'townships',
                        default => throw new RuntimeException("Unsupported table {$table}."),
                    };

                    $parentId = DB::table($parentTable)->where('source_id', $parentSourceId)->value('id');

                    if (! $parentId) {
                        throw new RuntimeException("Parent {$parentSourceId} not found for {$record['id']}.");
                    }
                }

                $row = [
                    'source_id' => $record['id'],
                    $parentColumn => $parentId,
                    'name' => $record['name_en'] ?? $record['name_local'],
                    'name_local' => $record['name_local'] ?? null,
                    'slug' => $record['slug'] ?? null,
                    'latitude' => $record['lat'] ?? null,
                    'longitude' => $record['lon'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($table === 'wards') {
                    $row['postal_code'] = isset($record['postal_code']) ? (string) $record['postal_code'] : null;
                }

                $rows[] = $row;
            }

            DB::table($table)->upsert(
                $rows,
                ['source_id'],
                array_values(array_diff(array_keys($rows[0]), ['source_id', 'created_at']))
            );
        }
    }
}
