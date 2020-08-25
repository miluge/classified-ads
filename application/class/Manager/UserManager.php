<?php
namespace Ads\Manager;
use \Ads\Database as Database;
use \Ads\User as User;

class UserManager extends Database
{
    /**
     * @param integer $id [id of user to fetch in database]
     * @return User|array [fetched User instance on success | ["error" => message] on fail]
     */
    public static function getUserById($id){
        try{
            $database = self::connect();
            $select = "SELECT id, email, lastName, firstName, phone FROM user WHERE id = :id";
            $request = $database -> prepare($select);
            $request -> bindValue(':id', $id);
            $request -> execute();
            if ($user = $request->fetch()) {
                return new User($user);
            } else {
                throw new \InvalidArgumentException("User not found !");
            }
        } catch (\Exception $e) {
            return(["error"=>$e->getMessage()]);
        }
    }

    /**
     * @param id $id [id of user to be deleted in database]
     * @return array [["error" => false] on success | ["error" => message] on fail]
     */
    public static function deleteUserById($id){
        try{
            $database = self::connect();
            $delete = "DELETE FROM user WHERE id = :id";
            $request = $database -> prepare($delete);
            $request -> bindValue(':id', $id);
            if ($request->execute()){
                return ["error" => false];
            }else {
                throw new \InvalidArgumentException("User not found !");
            }
        } catch (\Exception $e) {
            return(["error"=>$e->getMessage()]);
        }
    }
}