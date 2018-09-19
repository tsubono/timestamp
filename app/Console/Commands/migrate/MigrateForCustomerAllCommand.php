<?php
namespace App\Console\Commands\migrate;

use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;

/**
 * MigrateAllCommand
 *
 * @package App\Console\Commands\timestamp
 */
class MigrateForCustomerAllCommand extends Command
{
    /** @type string The console command name. */
    protected $name = 'customer:migrate:all';

    /** @type string The console command description. */
    protected $description = 'Migrate all contracts databases.';

    /** @type \Illuminate\Database\DatabaseManager */
    private $db;

    private $connection = 'timestamp-db';


    public function __construct(DatabaseManager $db)
    {
        parent::__construct();
        $this->db = $db;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $count = 0;
        $this->db->connection($this->connection)->table('contracts')
            ->chunk(50, function ($contractRows) use ($count) {

                foreach ($contractRows as $row) {
                    $count += 1;

                    $res = $this->migrateDatabase($row);

                    if ($res == -1) {
                        $this->error($this->outputMessage($count, 'FAILED', $row['id'], $row['domain_name']));
                        continue;
                    }

                    $this->info($this->outputMessage($count, 'SUCCESS', $row['id'], $row['domain_name']));
                }
            });
    }

    /**
     * @param array $row contractテーブル1レコード
     * @return int 実行結果
     */
    protected function migrateDatabase(array $row)
    {
        return $this->call('customer:migrate', ['db_name' => $row['domain_name']]);
    }

    /**
     * 出力メッセージ
     * @param int $count 番目
     * @param string $result 結果
     * @param string $id contractテーブルid
     * @param string $dbName データベース名
     * @return string メッセージ
     */
    protected function outputMessage($count, $result, $id, $dbName)
    {
        return '[' . sprintf('%4d', $count) . "] result:{$result} contract_id:{$id} db_name:{$dbName}";
    }
}
