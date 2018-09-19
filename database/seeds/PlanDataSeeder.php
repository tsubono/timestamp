<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PlanDataSeeder extends Seeder
{
    private $connection = 'timestamp-db';

    public function run()
    {
        DB::connection($this->connection)->table('plans')->truncate();
        DB::connection($this->connection)->table('plans')->insert($this->records());

    }

    public function records()
    {
        $data[] = [
            'name' => 'スタートプラン',
            'monthly_price' => 980,
            'employee_limit' => 5,
            'rank' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        $data[] = [
            'name' => 'スタンダードプラン',
            'monthly_price' => 1500,
            'employee_limit' => 50,
            'rank' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        $data[] = [
            'name' => 'プレミアムプラン',
            'monthly_price' => 3980,
            'employee_limit' => 150,
            'rank' => 3,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];


        return $data;
    }
}
