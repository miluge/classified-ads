<?php
namespace Ads;

abstract class Database
{
    /**
     * @return PDO PDO instance of database
     * @throws PDOException if connection fails
     */
    public static function connect(){
        return new \PDO('mysql:host=' . HOST . ';dbname=' . NAME . ';charset=utf8', USER, PASSWORD,[\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC]);
    }
}