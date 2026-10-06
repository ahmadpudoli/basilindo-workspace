<?php

namespace Database\Seeders;

use Core\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $group = Company::updateOrCreate(
            ['code' => 'BASILINDO'],
            [
                'name' => 'Group Basilindo',
                'entity_type' => 'group',
                'classification' => 'internal',
                'roles' => ['group'],
                'status' => 'active',
                'parent_company_id' => null,
            ],
        );

        $companies = [
            ['name' => 'Quinsis', 'code' => 'QUINSIS'],
            ['name' => 'Legospay', 'code' => 'LEGOSPAY'],
            ['name' => 'LTN', 'code' => 'LTN'],
            ['name' => 'Carano', 'code' => 'CARANO'],
            ['name' => 'Magis', 'code' => 'MAGIS'],
            ['name' => 'Mitratama Mahadirga Makmur', 'code' => 'MMM'],
        ];

        foreach ($companies as $company) {
            Company::updateOrCreate(
                ['code' => $company['code']],
                [
                    'name' => $company['name'],
                    'entity_type' => 'company',
                    'classification' => 'internal',
                    'roles' => ['operating_company'],
                    'parent_company_id' => $group->getKey(),
                    'status' => 'active',
                ],
            );
        }
    }
}
