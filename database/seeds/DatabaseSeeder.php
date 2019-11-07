<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('poll_categories')->insert([
            'uuid' => (string) Str::uuid(),
            'name' => 'deleted'
        ]);
    }
}
