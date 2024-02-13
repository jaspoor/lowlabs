<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {        
        DB::table('records')->insert([
            'user' => User::find(['email' => 'jasper.spoor@gmail.com']),
        ]);
    }
}