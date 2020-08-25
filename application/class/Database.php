<?php
namespace Ads;

class Database
{
    public $host;
    public $user;
    public $password;
    public $name;

    //take array of ["attribute"=>value] and hydrate object
    public function __construct(array $array) {
        foreach($array as $attribute => $value){
            $this->$attribute = $value;
        }
    }

    //return PDO object
    //return Exception object on fail
    public function connect(){
        try {
            return new PDO('mysql:host=' . $this->host . ';dbname=' . $this->name . ';charset=utf8', $this->user, $this->password);
        } catch (Exception $e) {
            return($e);
        }
    }
}