<?php
namespace App\Console\Commands\migrate;

use App\Console\Commands\migrate\CustomerDbConnection;
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
class MigrateForCustomerCommand extends Command
{
    use CustomerDbConnection;

    /** @type string The console command name. */
    protected $name = 'customer:migrate';

    /** @type string The console command description. */
    protected $description = 'Run the database migrations';

    /** @type string */
    private $path = 'database/migrations/customer';

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

        $args = [
            '--path' => $this->path,
            '--database' => $this->connection,
        ];

        if ($this->option('force')) {
            $args['--force'] = '--force';
        }

        if ($this->option('pretend')) {
            $args['--pretend'] = '--pretend';
        }

        if ($this->option('seed')) {
            $args['--seed'] = '--seed';
        }

        return $this->call('migrate', $args);
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

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
        ];
    }
}
