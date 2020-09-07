<?php
namespace Ads;

abstract class Database
{
    const HOST = "localhost";
    const USER = "root";
    const PASSWORD = "";
    const NAME = "classified-ads";

    /**
     * @return PDO PDO instance of database
     * @throws PDOException if connection fails
     */
    public static function connect(){
        return new \PDO('mysql:host=' . self::HOST . ';dbname=' . self::NAME . ';charset=utf8', self::USER, self::PASSWORD,[\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC]);
    }
}