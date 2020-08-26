<?php
namespace Ads;

abstract class Database
{
    const HOST = "localhost";
    const USER = "root";
    const PASSWORD = "";
    const NAME = "classified-ads";

    /**
     * @return PDO|array PDO instance on success | ["error" => message] on fail
     */
    public static function connect(){
        try{
            return new \PDO('mysql:host=' . self::HOST . ';dbname=' . self::NAME . ';charset=utf8', self::USER, self::PASSWORD,[\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC]);
        } catch (\PDOException $e){
            return ["error" => $e->getMessage()];
        }
    }
}