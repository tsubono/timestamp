<?php
namespace App\Console\Commands\migrate;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * RollbackCommand
 *
 * @package App\Console\Commands\timestamp
 */
class RollbackForCustomerCommand extends Command
{
    use CustomerDbConnection;

    /** @type string The console command name. */
    protected $name = 'customer:migrate:rollback';

    /** @type string The console command description. */
    protected $description = 'Rollback the last database migration';

    /** @type string */
    private $path = 'database/migrations/customer';

    public function handle()
    {
        $this->configureConnection($this->argument('db_name'));

        $args = [
            '--path' => $this->path,
            '--database' => $this->connection,
        ];

        if ($this->option('force')) {
            $args[] = '--force';
        }

        if ($this->option('pretend')) {
            $args[] = '--pretend';
        }

        return $this->call('migrate:rollback', $args);
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
        ];
    }
}
