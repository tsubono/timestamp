<?php
namespace App\Console\Commands\migrate;

use Config;
use DB;
use Exception;

/**
 * ContractDbConnection
 *
 * @package App\Console\Commands\timestamp
 */
trait CustomerDbConnection
{
    /**
     * @type string データベース接続名
     */
    protected $connection = 'customer-db';

    /**
     * 接続設定にデータベース名を設定
     */
    public function configureConnection($database)
    {
        try {
            // 接続設定にデータベース名を設定
            Config::set("database.connections.{$this->connection}.database", $database);

            DB::reconnect($this->connection);
            DB::setDefaultConnection($this->connection);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
