<?php
namespace Ads;

class Database
{
    const HOST = "localhost";
    const USER = "root";
    const PASSWORD = "";
    const NAME = "classified-ads";

    /**
     * @return PDO|array [instance of PDO connection on sucess | array ["error" => message] on fail]
     */
    public function connect(){
        try{
            return new \PDO('mysql:host=' . self::HOST . ';dbname=' . self::NAME . ';charset=utf8', self::USER, self::PASSWORD);
        } catch (\PDOException $e){
            return ["error" => $e->getMessage()];
        }
    }
}