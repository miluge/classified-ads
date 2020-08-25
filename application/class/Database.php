<?php
namespace Ads;

class Database
{
    const HOST = "localhost";
    const USER = "root";
    const PASSWORD = "";
    const NAME = "classified-ads";

    /**
     * @return PDO instance of PDO connection
     */
    public function connect(){
        return new \PDO('mysql:host=' . self::HOST . ';dbname=' . self::NAME . ';charset=utf8', self::USER, self::PASSWORD);
    }
}