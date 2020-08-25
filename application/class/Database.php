<?php
namespace Ads;

class Database
{
    public $host;
    public $user;
    public $password;
    public $name;

    //take array of ["attribute"=>value]
    public function __construct(array $array) {
        foreach($array as $attribute => $value){
            $this->$attribute = $value;
        }
    }

    public function connect(){
        try {
            return new PDO('mysql:host=' . $this->host . ';dbname=' . $this->name . ';charset=utf8', $this->user, $this->password);
        } catch (PDOException $exception) {
            exit('Failed to connect to database!');
        }
    }
}