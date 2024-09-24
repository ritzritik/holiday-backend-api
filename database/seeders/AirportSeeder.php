<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AirportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $airports = [
            ['name' => 'Gatwick', 'code' => 'LGW', 'region' => 'London'],
            ['name' => 'Heathrow', 'code' => 'LHR', 'region' => 'London'],
            ['name' => 'Stansted', 'code' => 'STN', 'region' => 'London'],
            ['name' => 'Luton', 'code' => 'LTN', 'region' => 'London'],
            ['name' => 'London City', 'code' => 'LCY', 'region' => 'London'],
            ['name' => 'Southend', 'code' => 'SEN', 'region' => 'London'],

            ['name' => 'Birmingham', 'code' => 'BHX', 'region' => 'Midlands'],
            ['name' => 'East Midlands', 'code' => 'EMA', 'region' => 'Midlands'],
            ['name' => 'Coventry', 'code' => 'CVT', 'region' => 'Midlands'],
            ['name' => 'Norwich', 'code' => 'NWI', 'region' => 'Midlands'],

            ['name' => 'Cardiff', 'code' => 'CWL', 'region' => 'Wales'],

            ['name' => 'Southampton', 'code' => 'SOU', 'region' => 'South England'],
            ['name' => 'Bournemouth', 'code' => 'BOH', 'region' => 'South England'],
            ['name' => 'Exeter', 'code' => 'EXT', 'region' => 'South England'],
            ['name' => 'Bristol', 'code' => 'BRS', 'region' => 'South England'],

            ['name' => 'Manchester', 'code' => 'MAN', 'region' => 'North West'],
            ['name' => 'Liverpool', 'code' => 'LPL', 'region' => 'North West'],
            ['name' => 'Blackpool', 'code' => 'BLK', 'region' => 'North West'],

            ['name' => 'Leeds Bradford', 'code' => 'LBA', 'region' => 'North East'],
            ['name' => 'Humberside', 'code' => 'HUY', 'region' => 'North East'],
            ['name' => 'New Castle', 'code' => 'NCL', 'region' => 'North East'],
            ['name' => 'Doncaster', 'code' => 'DSA', 'region' => 'North East'],
            ['name' => 'Durham Tees Valley', 'code' => 'MME', 'region' => 'North East'],

            ['name' => 'Glasgow', 'code' => 'GLA', 'region' => 'Scotland'],
            ['name' => 'Prestwick', 'code' => 'PIK', 'region' => 'Scotland'],
            ['name' => 'Edinburgh', 'code' => 'EDI', 'region' => 'Scotland'],
            ['name' => 'Aberdeen', 'code' => 'ABZ', 'region' => 'Scotland'],

            ['name' => 'Belfast', 'code' => 'BFS', 'region' => 'Ireland'],
            ['name' => 'Dublin', 'code' => 'DUB', 'region' => 'Ireland']
        ];

        DB::table('airports')->insert($airports);
    }
}
