<?php
namespace Ads\Manager;
use \Ads\Database as Database;
use \Ads\User as User;

class UserManager extends Database
{
    /**
     * @param id $id [id of user to fetch in classified-ads database]
     * @return User|array [fetched User instance on success | ["error" => message] on fail]
     */
    public function getUserById($id){
        try{
            $database = $this -> connect();
            $select = "SELECT id, email, lastName, firstName, phone FROM user WHERE id = :id";
            $request = $database -> prepare($select);
            $request -> bindValue(':id', $id);
            $request -> execute();
            if ($user = $request->fetch()) {
                return new User($user);
            } else {
                throw new \InvalidArgumentException("User not found !");
            }
        } catch (\InvalidArgumentException $e) {
            return(["error"=>$e->getMessage()]);
        }
    }
}