<?php
namespace App\Console\Commands;

use App\Console\Commands\migrate\CustomerDbConnection;
use App\Models\Affiliation;
use App\Models\ChangeTimecard;
use App\Models\ChangeTimecardDetail;
use App\Models\Employee;
use App\Models\Recorder;
use App\Models\Salary;
use App\Models\Timecard;
use App\Models\TimecardDetail;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\DatabaseManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use DB;

/**
 * MigrateCommand
 *
 * @package App\Console\Commands\timestamp
 */
class InsertTestDataForPaymentCommand extends Command
{
    use CustomerDbConnection;

    /** @type string The console command name. */
    protected $name = 'add:test:payment';

    /** @type string The console command description. */
    protected $description = 'insert test payment datas';


    /** @type Config */
    private $config;

    /** @type \Illuminate\Database\DatabaseManager */
    private $db;


    public function __construct(Config $config, DatabaseManager $db)
    {
        parent::__construct();
        $this->config = $config;
        $this->db = $db;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $res = $this->configureConnection($this->argument('db_name'));

        if (!$res) {
            return -1;
        }

        //テストデータinsert実行
        DB::connection($this->connection)->table('employees')->insert([
            ['uid' => '11111111111111111', 'lname' => '給与明細', 'fname' => '従業員', 'lname_kana' => 'キュウヨメイサイ', 'fname_kana' => 'ジュウギョウイン', 'gender' => 'male', 'icon' => '002.png', 'icon_type' => 'icon', 'traffic_cost' => '1000', 'resigned_date' => NULL, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        DB::connection($this->connection)->table('salaries')->insert([
            ['employee_uid' => '11111111111111111', 'apply_date' => '2017-05-01', 'start_time' => '00:00', 'hourly_pay' => '1200', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_uid' => '11111111111111111', 'apply_date' => '2017-04-14', 'start_time' => '12:00', 'hourly_pay' => '1200', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_uid' => '11111111111111111', 'apply_date' => '2017-04-01', 'start_time' => '00:00', 'hourly_pay' => '1100', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_uid' => '11111111111111111', 'apply_date' => '2017-03-01', 'start_time' => '00:00', 'hourly_pay' => '1000', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_uid' => '11111111111111111', 'apply_date' => '2017-02-15', 'start_time' => '00:00', 'hourly_pay' => '1200', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_uid' => '11111111111111111', 'apply_date' => '2017-02-01', 'start_time' => '00:00', 'hourly_pay' => '1100', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_uid' => '11111111111111111', 'apply_date' => '2017-01-01', 'start_time' => '00:00', 'hourly_pay' => '1000', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        $owner = User::where('owner_flg', '1')->first();

        DB::connection($this->connection)->table('affiliations')->insert([
            ['workplace_uid' => $owner["workplace_uid"], 'employee_uid' => '11111111111111111', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);


        DB::connection($this->connection)->table('timecards')->insert([
            //1月
            ['id' => '1', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-01', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '2', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-02', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '3', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-03', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '4', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-09', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '5', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-10', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '6', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-16', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '7', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-20', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '8', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-25', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '9', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-26', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            //2月
            ['id' => '10', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-01', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '11', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-02', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '12' ,'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-03', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '13', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-09', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '14', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-10', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '15', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-14', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '16', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-20', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '17', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-23', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '18', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-28', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            //3月
            ['id' => '19', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-03-01', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '20', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-03-02', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '21', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-03-03', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '22', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-03-09', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '23', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-03-10', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '24', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-03-16', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '25', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-03-20', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '26', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-03-25', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '27', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-03-26', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            //4月
            ['id' => '28', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-04-01', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '29', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-04-02', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '30', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-04-03', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '31', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-04-09', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '32', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-04-10', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '33', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-04-14', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '34', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-04-20', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '35', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-04-23', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '36', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-04-28', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            //5月
            ['id' => '37', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-05-01', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '38', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-05-02', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '39', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-05-03', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '40', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-05-04', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '41', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-05-05', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '42', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-05-06', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            //6月
            ['id' => '43', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-06-01', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '44', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-06-02', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '45', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-06-03', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '46', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-06-04', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '47', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-06-10', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '48', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-06-11', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '49', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-06-12', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '50', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-06-13', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

        ]);


        DB::connection($this->connection)->table('timecard_details')->insert([
            //1月
            ['timecard_id' => '1', 'start_time' => '2017-01-01 10:10', 'end_time' => '2017-01-01 18:14','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '1', 'start_time' => '2017-01-01 13:12', 'end_time' => '2017-01-01 14:15','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '2', 'start_time' => '2017-01-02 10:17', 'end_time' => '2017-01-02 18:27','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '2', 'start_time' => '2017-01-02 13:20', 'end_time' => '2017-01-02 14:25','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '3', 'start_time' => '2017-01-03 10:35', 'end_time' => '2017-01-03 17:40','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '3', 'start_time' => '2017-01-03 13:37', 'end_time' => '2017-01-03 14:40','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '4', 'start_time' => '2017-01-09 10:47', 'end_time' => '2017-01-09 18:56','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '4', 'start_time' => '2017-01-09 13:55', 'end_time' => '2017-01-09 14:56','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '5', 'start_time' => '2017-01-10 10:00', 'end_time' => '2017-01-10 18:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '5', 'start_time' => '2017-01-10 13:00', 'end_time' => '2017-01-10 14:00','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '6', 'start_time' => '2017-01-16 10:15', 'end_time' => '2017-01-16 18:15','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '6', 'start_time' => '2017-01-16 13:15', 'end_time' => '2017-01-16 14:15','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '7', 'start_time' => '2017-01-20 10:30', 'end_time' => '2017-01-20 18:30','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '7', 'start_time' => '2017-01-20 13:30', 'end_time' => '2017-01-20 14:30','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '8', 'start_time' => '2017-01-25 10:45', 'end_time' => '2017-01-25 18:45','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '8', 'start_time' => '2017-01-25 13:45', 'end_time' => '2017-01-25 14:45','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '9', 'start_time' => '2017-01-26 10:00', 'end_time' => '2017-01-26 18:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '9', 'start_time' => '2017-01-26 13:00', 'end_time' => '2017-01-26 14:00','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            //2月
            ['timecard_id' => '10', 'start_time' => '2017-02-01 10:10', 'end_time' => '2017-02-01 18:14','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '10', 'start_time' => '2017-02-01 13:12', 'end_time' => '2017-02-01 14:15','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '11', 'start_time' => '2017-02-02 10:17', 'end_time' => '2017-02-02 18:27','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '11', 'start_time' => '2017-02-02 13:20', 'end_time' => '2017-02-02 14:25','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '12', 'start_time' => '2017-02-03 10:35', 'end_time' => '2017-02-03 17:40','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '12', 'start_time' => '2017-02-03 13:37', 'end_time' => '2017-02-03 14:40','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '13', 'start_time' => '2017-02-09 10:47', 'end_time' => '2017-02-09 18:56','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '13', 'start_time' => '2017-02-09 13:55', 'end_time' => '2017-02-09 14:56','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '14', 'start_time' => '2017-02-10 10:00', 'end_time' => '2017-02-10 18:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '14', 'start_time' => '2017-02-10 13:00', 'end_time' => '2017-02-10 14:00','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '15', 'start_time' => '2017-02-14 10:15', 'end_time' => '2017-02-14 18:15','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '15', 'start_time' => '2017-02-14 13:15', 'end_time' => '2017-02-14 14:15','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '16', 'start_time' => '2017-02-20 10:30', 'end_time' => '2017-02-20 18:30','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '16', 'start_time' => '2017-02-20 13:30', 'end_time' => '2017-02-20 14:30','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '17', 'start_time' => '2017-02-23 10:45', 'end_time' => '2017-02-23 18:45','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '17', 'start_time' => '2017-02-23 13:45', 'end_time' => '2017-02-23 14:45','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '18', 'start_time' => '2017-02-28 10:00', 'end_time' => '2017-02-28 18:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '18', 'start_time' => '2017-02-28 13:00', 'end_time' => '2017-02-28 14:00','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            //3月
            ['timecard_id' => '19', 'start_time' => '2017-03-01 10:10', 'end_time' => '2017-03-01 18:14','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '20', 'start_time' => '2017-03-02 10:17', 'end_time' => '2017-03-02 18:27','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '21', 'start_time' => '2017-03-03 10:35', 'end_time' => '2017-03-03 17:40','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '22', 'start_time' => '2017-03-09 10:47', 'end_time' => '2017-03-09 18:56','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '23', 'start_time' => '2017-03-10 10:00', 'end_time' => '2017-03-10 18:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '24', 'start_time' => '2017-03-16 10:15', 'end_time' => '2017-03-16 18:15','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '25', 'start_time' => '2017-03-20 10:30', 'end_time' => '2017-03-20 18:30','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '26', 'start_time' => '2017-03-25 10:45', 'end_time' => '2017-03-25 18:45','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '27', 'start_time' => '2017-03-26 10:00', 'end_time' => '2017-03-26 18:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            //4月
            ['timecard_id' => '28', 'start_time' => '2017-04-01 10:10', 'end_time' => '2017-04-01 18:14','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '29', 'start_time' => '2017-04-02 10:17', 'end_time' => '2017-04-02 18:27','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '30', 'start_time' => '2017-04-03 10:35', 'end_time' => '2017-04-03 17:40','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '31', 'start_time' => '2017-04-09 10:47', 'end_time' => '2017-04-09 18:56','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '32', 'start_time' => '2017-04-10 10:00', 'end_time' => '2017-04-10 18:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '33', 'start_time' => '2017-04-14 10:15', 'end_time' => '2017-04-14 18:15','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '34', 'start_time' => '2017-04-20 10:30', 'end_time' => '2017-04-20 18:30','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '35', 'start_time' => '2017-04-23 10:45', 'end_time' => '2017-04-23 18:45','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '36', 'start_time' => '2017-04-28 10:00', 'end_time' => '2017-04-28 18:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            //5月
            ['timecard_id' => '37', 'start_time' => '2017-05-01 21:00', 'end_time' => '2017-05-02 01:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '38', 'start_time' => '2017-05-02 01:00', 'end_time' => '2017-05-02 05:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '38', 'start_time' => '2017-05-02 01:00', 'end_time' => '2017-05-02 03:00','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '39', 'start_time' => '2017-05-03 22:00', 'end_time' => '2017-05-04 01:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '40', 'start_time' => '2017-05-04 01:00', 'end_time' => '2017-05-04 04:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '41', 'start_time' => '2017-05-05 23:00', 'end_time' => '2017-05-06 01:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '42', 'start_time' => '2017-05-06 01:00', 'end_time' => '2017-05-06 07:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '42', 'start_time' => '2017-05-06 01:00', 'end_time' => '2017-05-06 02:00','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            //6月
            ['timecard_id' => '43', 'start_time' => '2017-06-01 10:00', 'end_time' => '2017-06-01 18:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '43', 'start_time' => '2017-06-01 13:00', 'end_time' => '2017-06-01 14:00','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '44', 'start_time' => '2017-06-02 20:00', 'end_time' => '2017-06-02 23:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '45', 'start_time' => '2017-06-03 09:00', 'end_time' => '2017-06-03 15:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '46', 'start_time' => '2017-06-04 17:00', 'end_time' => '2017-06-05 00:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '46', 'start_time' => '2017-06-04 18:00', 'end_time' => '2017-06-04 19:00','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '46', 'start_time' => '2017-06-04 22:00', 'end_time' => '2017-06-04 23:00','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '47', 'start_time' => '2017-06-10 10:00', 'end_time' => '2017-06-10 22:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '47', 'start_time' => '2017-06-10 13:00', 'end_time' => '2017-06-10 14:00','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '47', 'start_time' => '2017-06-10 18:00', 'end_time' => '2017-06-10 19:00','type' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '48', 'start_time' => '2017-06-11 12:00', 'end_time' => '2017-06-11 14:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '49', 'start_time' => '2017-06-12 16:00', 'end_time' => '2017-06-12 19:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '50', 'start_time' => '2017-06-13 20:00', 'end_time' => '2017-06-13 23:00','type' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

        ]);
        
        //稼働時間を入れる
        $details = TimecardDetail::all();
        foreach ($details as $detail) {
            $operating_times = TimecardDetail::getOperatingTimes($detail->start_time, $detail->end_time, $detail->type);

            $updated = TimecardDetail::where('id', $detail->id)->first();
            $updated->fill($operating_times);
            $updated->save();
        }

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['db_name', InputArgument::REQUIRED, 'Database name'],
        ];
    }
}
