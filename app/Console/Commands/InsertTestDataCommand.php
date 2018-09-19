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
class InsertTestDataCommand extends Command
{
    use CustomerDbConnection;

    /** @type string The console command name. */
    protected $name = 'add:test:data';

    /** @type string The console command description. */
    protected $description = 'insert test datas';


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
        $users = User::withTrashed()->get();

        foreach ($users as $user) {
            if ($user->owner_flg != 1) {
                $user->forceDelete();
            } else {
                $owner = $user;
            }
        }
        DB::connection($this->connection)->table('users')->insert([
            ['login_id' => 'user1', 'email' => 'user1@tsubono.co.jp', 'password' => bcrypt('user1'), 'enable_flg' => 1, 'workplace_uid' => $owner["workplace_uid"], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['login_id' => 'user2', 'email' => 'user2@tsubono.co.jp', 'password' => bcrypt('user2'), 'enable_flg' => 1, 'workplace_uid' => $owner["workplace_uid"], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['login_id' => 'user3', 'email' => 'user3@tsubono.co.jp', 'password' => bcrypt('user3'), 'enable_flg' => 0, 'workplace_uid' => $owner["workplace_uid"], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        $employees = Employee::withTrashed()->get();
        foreach ($employees as $employee) {
            $employee->forceDelete();
        }
        DB::connection($this->connection)->table('employees')->insert([
            ['uid' => '11111111111111111', 'lname' => 'テスト', 'fname' => '従業員①', 'lname_kana' => 'テスト', 'fname_kana' => 'ジュウギョウイン', 'gender' => 'male', 'icon' => '002.png', 'icon_type' => 'icon', 'traffic_cost' => '1000', 'resigned_date' => NULL, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['uid' => '99999999999999999', 'lname' => 'テスト', 'fname' => '従業員②', 'lname_kana' => 'テスト', 'fname_kana' => 'ジュウギョウイン', 'gender' => 'male', 'icon' => NULL, 'icon_type' => NULL, 'traffic_cost' => '0','resigned_date' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['uid' => '22222222222222222', 'lname' => 'テスト', 'fname' => '従業員③', 'lname_kana' => 'テスト', 'fname_kana' => 'ジュウギョウイン', 'gender' => 'female', 'icon' => NULL, 'icon_type' => NULL, 'traffic_cost' => '0','resigned_date' => NULL, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['uid' => '33333333333333333', 'lname' => 'テスト', 'fname' => '従業員④', 'lname_kana' => 'テスト', 'fname_kana' => 'ジュウギョウイン', 'gender' => 'female', 'icon' => NULL, 'icon_type' => NULL, 'traffic_cost' => '0','resigned_date' => NULL, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        $salaries = Salary::withTrashed()->get();
        foreach ($salaries as $salary) {
            $salary->forceDelete();
        }
        DB::connection($this->connection)->table('salaries')->insert([
            ['employee_uid' => '11111111111111111', 'apply_date' => '2017-03-01', 'start_time' => '00:00', 'hourly_pay' => '1300', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_uid' => '11111111111111111', 'apply_date' => '2017-02-01', 'start_time' => '00:00', 'hourly_pay' => '1100', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_uid' => '11111111111111111', 'apply_date' => '2017-02-15', 'start_time' => '00:00', 'hourly_pay' => '1200', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_uid' => '11111111111111111', 'apply_date' => '2017-01-01', 'start_time' => '00:00', 'hourly_pay' => '1000', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_uid' => '99999999999999999', 'apply_date' => '2017-03-01', 'start_time' => '00:00', 'hourly_pay' => '1100', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_uid' => '22222222222222222', 'apply_date' => '2017-03-01', 'start_time' => '00:00', 'hourly_pay' => '1200', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_uid' => '22222222222222222', 'apply_date' => '2017-03-01', 'start_time' => '17:00', 'hourly_pay' => '1400', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);


        $affiliations = Affiliation::get();
        foreach ($affiliations as $affiliation) {
            $affiliation->delete();
        }
        DB::connection($this->connection)->table('affiliations')->insert([
            ['workplace_uid' => $owner["workplace_uid"], 'employee_uid' => '11111111111111111', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['workplace_uid' => $owner["workplace_uid"], 'employee_uid' => '99999999999999999', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['workplace_uid' => $owner["workplace_uid"], 'employee_uid' => '22222222222222222', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['workplace_uid' => $owner["workplace_uid"], 'employee_uid' => '33333333333333333', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        $recorders = Recorder::get();
        foreach ($recorders as $recorder) {
            $recorder->delete();
        }
        DB::connection($this->connection)->table('recorders')->insert([
            ['uid' => '99999999999999999', 'workplace_uid' => $owner["workplace_uid"], 'type' => 'web', 'name' => 'テストレコーダー①', 'pass_code' => '0000', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        $timecards = Timecard::withTrashed()->get();
        foreach ($timecards as $timecard) {
            $timecard->forceDelete();
        }
        DB::connection($this->connection)->table('timecards')->insert([
            //1月は16日出勤
            ['id' => '1', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-01', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '2', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-02', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '3', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-03', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '4', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-04', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '5', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-09', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '6', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-10', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '7', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-11', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '8','employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-15', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '9', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-16', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '10', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-17', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '11', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-19', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '12', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-22', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '13', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-26', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '14', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-28', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '15', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-30', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '16', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-01-31', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            //2月は20日出勤
            ['id' => '17', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-01', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '18', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-02', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '19' ,'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-03', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '20' ,'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-06', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '21', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-07', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '22', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-08', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '23', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-09', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '24', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-10', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '25', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-13', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '26', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-14', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '27', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-15', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '28', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-16', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '29', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-17', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '30', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-20', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '31', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-21', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '32', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-22', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '33', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-23', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '34', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-24', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '35', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-27', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '36', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-02-28', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            ['id' => '37', 'employee_uid' => '33333333333333333', 'workplace_uid' => $owner["workplace_uid"], 'date' => '2017-03-01', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

        ]);

        $timecard_details = TimecardDetail::withTrashed()->get();
        foreach ($timecard_details as $timecard_detail) {
            $timecard_detail->forceDelete();
        }
        DB::connection($this->connection)->table('timecard_details')->insert([
            ['timecard_id' => '1', 'start_time' => '2017-01-01 10:00', 'end_time' => '2017-01-01 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '1', 'start_time' => '2017-01-01 13:00', 'end_time' => '2017-01-01 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '2', 'start_time' => '2017-01-02 10:00', 'end_time' => '2017-01-02 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '2', 'start_time' => '2017-01-02 13:00', 'end_time' => '2017-01-02 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '3', 'start_time' => '2017-01-03 10:00', 'end_time' => '2017-01-03 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '3', 'start_time' => '2017-01-03 13:00', 'end_time' => '2017-01-03 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '4', 'start_time' => '2017-01-04 10:00', 'end_time' => '2017-01-04 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '4', 'start_time' => '2017-01-04 13:00', 'end_time' => '2017-01-04 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '5', 'start_time' => '2017-01-09 10:00', 'end_time' => '2017-01-09 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '5', 'start_time' => '2017-01-09 13:00', 'end_time' => '2017-01-09 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '6', 'start_time' => '2017-01-10 10:00', 'end_time' => '2017-01-10 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '6', 'start_time' => '2017-01-10 13:00', 'end_time' => '2017-01-10 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '7', 'start_time' => '2017-01-11 10:00', 'end_time' => '2017-01-11 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '7', 'start_time' => '2017-01-11 13:00', 'end_time' => '2017-01-11 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '8', 'start_time' => '2017-01-15 10:00', 'end_time' => '2017-01-15 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '8', 'start_time' => '2017-01-15 13:00', 'end_time' => '2017-01-15 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '9', 'start_time' => '2017-01-16 10:00', 'end_time' => '2017-01-16 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '9', 'start_time' => '2017-01-16 13:00', 'end_time' => '2017-01-16 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '10', 'start_time' => '2017-01-17 10:00', 'end_time' => '2017-01-17 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '10', 'start_time' => '2017-01-17 13:00', 'end_time' => '2017-01-17 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '11', 'start_time' => '2017-01-19 10:00', 'end_time' => '2017-01-19 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '11', 'start_time' => '2017-01-19 13:00', 'end_time' => '2017-01-19 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '12', 'start_time' => '2017-01-22 10:00', 'end_time' => '2017-01-22 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '12', 'start_time' => '2017-01-22 13:00', 'end_time' => '2017-01-22 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '13', 'start_time' => '2017-01-26 10:00', 'end_time' => '2017-01-26 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '13', 'start_time' => '2017-01-26 13:00', 'end_time' => '2017-01-26 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '14', 'start_time' => '2017-01-28 10:00', 'end_time' => '2017-01-28 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '14', 'start_time' => '2017-01-28 13:00', 'end_time' => '2017-01-28 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '15', 'start_time' => '2017-01-30 10:00', 'end_time' => '2017-01-30 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '15', 'start_time' => '2017-01-30 13:00', 'end_time' => '2017-01-30 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '16', 'start_time' => '2017-01-31 10:00', 'end_time' => '2017-01-31 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '16', 'start_time' => '2017-01-31 13:00', 'end_time' => '2017-01-31 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            ['timecard_id' => '17', 'start_time' => '2017-02-01 10:00', 'end_time' => '2017-02-01 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '17', 'start_time' => '2017-02-01 13:00', 'end_time' => '2017-02-01 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '18', 'start_time' => '2017-02-02 10:00', 'end_time' => '2017-02-02 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '18', 'start_time' => '2017-02-02 13:00', 'end_time' => '2017-02-02 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '19', 'start_time' => '2017-02-03 10:00', 'end_time' => '2017-02-03 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '19', 'start_time' => '2017-02-03 13:00', 'end_time' => '2017-02-03 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '20', 'start_time' => '2017-02-06 10:00', 'end_time' => '2017-02-06 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '20', 'start_time' => '2017-02-06 13:00', 'end_time' => '2017-02-06 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '21', 'start_time' => '2017-02-07 10:00', 'end_time' => '2017-02-07 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '21', 'start_time' => '2017-02-07 13:00', 'end_time' => '2017-02-07 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '22', 'start_time' => '2017-02-08 10:00', 'end_time' => '2017-02-08 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '22', 'start_time' => '2017-02-08 13:00', 'end_time' => '2017-02-08 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '23', 'start_time' => '2017-02-09 10:00', 'end_time' => '2017-02-09 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '23', 'start_time' => '2017-02-09 13:00', 'end_time' => '2017-02-09 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '24', 'start_time' => '2017-02-10 10:00', 'end_time' => '2017-02-10 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '24', 'start_time' => '2017-02-10 13:00', 'end_time' => '2017-02-10 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '25', 'start_time' => '2017-02-13 10:00', 'end_time' => '2017-02-13 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '25', 'start_time' => '2017-02-13 13:00', 'end_time' => '2017-02-13 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '26', 'start_time' => '2017-02-14 10:00', 'end_time' => '2017-02-14 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '26', 'start_time' => '2017-02-14 13:00', 'end_time' => '2017-02-14 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '27', 'start_time' => '2017-02-15 10:00', 'end_time' => '2017-02-15 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '27', 'start_time' => '2017-02-15 13:00', 'end_time' => '2017-02-15 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '28', 'start_time' => '2017-02-16 10:00', 'end_time' => '2017-02-16 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '28', 'start_time' => '2017-02-16 13:00', 'end_time' => '2017-02-16 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '29', 'start_time' => '2017-02-17 10:00', 'end_time' => '2017-02-17 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '29', 'start_time' => '2017-02-17 13:00', 'end_time' => '2017-02-17 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '30', 'start_time' => '2017-02-20 10:00', 'end_time' => '2017-02-20 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '30', 'start_time' => '2017-02-20 13:00', 'end_time' => '2017-02-20 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '31', 'start_time' => '2017-02-21 10:00', 'end_time' => '2017-02-21 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '31', 'start_time' => '2017-02-21 13:00', 'end_time' => '2017-02-21 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '32', 'start_time' => '2017-02-22 10:00', 'end_time' => '2017-02-22 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '32', 'start_time' => '2017-02-22 13:00', 'end_time' => '2017-02-22 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '33', 'start_time' => '2017-02-23 10:00', 'end_time' => '2017-02-23 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '33', 'start_time' => '2017-02-23 13:00', 'end_time' => '2017-02-23 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '34', 'start_time' => '2017-02-24 10:00', 'end_time' => '2017-02-24 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '34', 'start_time' => '2017-02-24 13:00', 'end_time' => '2017-02-24 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '35', 'start_time' => '2017-02-27 10:00', 'end_time' => '2017-02-27 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '35', 'start_time' => '2017-02-27 13:00', 'end_time' => '2017-02-27 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '36', 'start_time' => '2017-02-28 10:00', 'end_time' => '2017-02-28 18:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['timecard_id' => '36', 'start_time' => '2017-02-28 13:00', 'end_time' => '2017-02-28 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            ['timecard_id' => '37', 'start_time' => '2017-03-01 13:00', 'end_time' => '2017-03-01 14:00','type' => '1', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

        ]);

        $change_timecards = ChangeTimecard::withTrashed()->get();
        foreach ($change_timecards as $change_timecard) {
            $change_timecard->forceDelete();
        }
        DB::connection($this->connection)->table('change_timecards')->insert([
            ['id' => '1', 'timecard_id' => '1', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'status' => NULL, 'date' => '2017-01-01', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '2', 'timecard_id' => '2', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'status' => '2',  'date' => '2017-01-02', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '3', 'timecard_id' => '3', 'employee_uid' => '11111111111111111', 'workplace_uid' => $owner["workplace_uid"], 'status' => NULL,  'date' => '2017-01-03', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

        ]);

        $change_timecard_details = ChangeTimecardDetail::withTrashed()->get();
        foreach ($change_timecard_details as $change_timecard_detail) {
            $change_timecard_detail->forceDelete();
        }
        DB::connection($this->connection)->table('change_timecard_details')->insert([
            ['change_timecard_id' => '1', 'timecard_id' => '1', 'start_time' => '2017-01-01 12:00', 'end_time' => '2017-01-01 20:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['change_timecard_id' => '1', 'timecard_id' => '1', 'start_time' => '2017-01-01 13:00', 'end_time' => '2017-01-01 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            ['change_timecard_id' => '2', 'timecard_id' => '2', 'start_time' => '2017-01-02 12:00', 'end_time' => '2017-01-02 20:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['change_timecard_id' => '2', 'timecard_id' => '2', 'start_time' => '2017-01-02 13:00', 'end_time' => '2017-01-02 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            ['change_timecard_id' => '3', 'timecard_id' => '3', 'start_time' => '2017-01-03 12:00', 'end_time' => '2017-01-03 20:00','type' => '1', 'operating_time_round1' => 480, 'operating_time_round10' => 480, 'operating_time_round15' => 480, 'operating_time_round30' => 480, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['change_timecard_id' => '3', 'timecard_id' => '3', 'start_time' => '2017-01-03 13:00', 'end_time' => '2017-01-03 14:00','type' => '2', 'operating_time_round1' => 60, 'operating_time_round10' => 60, 'operating_time_round15' => 60, 'operating_time_round30' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

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
