<?php
namespace App\Console\Commands\migrate;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository as Config;
use PDO;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * CreateDbCommand
 *
 * @package App\Console\Commands\timestamp
 */
class CreateDatabaseCommand extends Command
{
    /** @type string The console command name. */
    protected $name = 'customer:create:database';

    /** @type string The console command description. */
    protected $description = 'Create a new database.';

    /** @type string */
    private $connection = 'customer-db';

    /** * @type Config */
    private $config;

    public function __construct(Config $config)
    {
        parent::__construct();
        $this->config = $config;
    }

    /**
     * Execute the console command.
     *
     * @throws Exception
     * @return mixed
     */
    public function handle()
    {
        // 契約別データベースの接続設定を読み取り
        $connection = $this->option('connection');
        $config = $this->config->get('database.connections.' . $connection);
        if ($config === null) {
            throw new Exception('Database connection `' . $connection . '` not configured.');
        }

        $database = $this->argument('name');
        $dsn = 'mysql:host=' . array_get($config, 'host') . ';port=' . array_get($config, 'port', 3306);
        $username = array_get($config, 'username');
        $password = array_get($config, 'password');

        // PDO(MySQL)でデータベースを作成
        $pdo = new PDO($dsn, $username, $password);
        $result = $pdo->query("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4");

        if ($result) {
            $this->info('Database `' . $database . '` is ready.');
        } else {
            $this->error('Failed creating new database `' . $database . '`.');
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
            ['name', InputArgument::REQUIRED, 'New database name.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['connection', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.', $this->connection],
        ];
    }
}
