<?php
namespace App\Console\Commands;

use App\Console\Commands\migrate\CustomerDbConnection;
use App\Models\Workplace;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\DatabaseManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * MigrateCommand
 *
 * @package App\Console\Commands\timestamp
 */
class UpdateChargeDateForTestCommand extends Command
{
    use CustomerDbConnection;

    /** @type string The console command name. */
    protected $name = 'update:charge:date';

    /** @type string The console command description. */
    protected $description = 'Run the database migrations';

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

        $uid = $this->argument('workplace_uid');
        $workplace = Workplace::where('uid', $uid)->first();
        if (empty($workplace)) {
            echo 'Workplace is not found.';
            return -1;
        }

        $workplace->next_charge_date = Carbon::now();
        $workplace->save();

        return true;

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
            ['workplace_uid', InputArgument::REQUIRED, 'Workplace uid'],
        ];
    }

}
