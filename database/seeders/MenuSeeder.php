<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id',
                'name',
                'sequence',
                'description',
                'status',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at'
            ],
        ];
        dd($data);

        \App\Models\TitleMenu::insert($data);
    }
}
