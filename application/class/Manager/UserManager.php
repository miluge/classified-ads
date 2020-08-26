<?php
namespace Ads\Manager;
use \Ads\Database as Database;
use \Ads\User as User;

class UserManager extends Database
{
    //METHODE NON ENCORE UTILISEE
    // /**
    //  * @param integer $id id of user to fetch in database
    //  * @return User|array fetched User instance on success | ["error" => message] on fail
    //  */
    // public static function getUserById($id){
    //     try{
    //         $pdo = self::connect();
    //         $select = "SELECT id, email, lastName, firstName, phone FROM user WHERE id = :id";
    //         $request = $pdo -> prepare($select);
    //         $request -> bindValue(':id', $id);
    //         $request -> execute();
    //         if ($user = $request->fetch()) {
    //             return new User($user);
    //         } else {
    //             throw new \InvalidArgumentException("User not found !");
    //         }
    //     } catch (\Exception $e) {
    //         return(["error"=>$e->getMessage()]);
    //     }
    // }

    /**
     * @param string $email email of user to fetch in database
     * @return integer|array matching user id on success, 0 if user doesn't exist | ["error" => message] on fail
     */
    public static function getUserIdByEmail($email){
        try{
            $pdo = self::connect();
            $select = "SELECT id FROM user WHERE email = :email";
            $request = $pdo -> prepare($select);
            $request -> bindValue(':email', $email);
            $request -> execute();
            if ($user = $request->fetch()) {
                return $user["id"];
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            return(["error"=>$e->getMessage()]);
        }
    }

    /**
     * @param User $user User instance to insert in database
     * @return integer matching user id on success, 0 if user doesn't exist | ["error" => message] on fail
     */
    public static function InsertUser($user){
        try{
            $pdo = self::connect();
            $select = "SELECT id FROM user WHERE email = :email";
            $request = $pdo -> prepare($select);
            $request -> bindValue(':email', $email);
            $request -> execute();
            if ($user = $request->fetch()) {
                return $user["id"];
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            return(["error"=>$e->getMessage()]);
        }
    }

    /**
     * @param id $id id of user to be deleted in database
     * @return array ["error" => false] on success | ["error" => message] on fail
     */
    public static function deleteUserById($id){
        try{
            $pdo = self::connect();
            $delete = "DELETE FROM user WHERE id = :id";
            $request = $pdo -> prepare($delete);
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