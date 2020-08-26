<?php
namespace Ads\Manager;
use \Ads\Database as Database;
use \Ads\User as User;

class UserManager extends Database
{
    /**
     * @param string $email email of user to select in database
     * @return User|array selected User instance on success | ["error" => message] on fail
     */
    public static function getUser($email){
        try{
            $pdo = self::connect();
            $select = "SELECT email, lastName, firstName, phone FROM user WHERE email = :email";
            $request = $pdo -> prepare($select);
            $request -> bindValue(':email', $email);
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
     * @param User $user User instance to insert in database
     * check if user already exists:
     * insert if not
     * update if so
     * @return array ["error" => false] on success | ["error" => message] on fail
     */
    public static function InsertUser($user){
        try{
            $pdo = self::connect();
            $previousUser = self::getUser($user->email);
            if (is_array($previousUser)) {
                $insert = "INSERT INTO user (email, lastName, firstName, phone) VALUES (:email, :lastName, :firstName, :phone)";
                $request = $pdo -> prepare($insert);
                $request -> bindValue(':email', $user->email);
                $request -> bindValue(':lastName', $user->lastName);
                $request -> bindValue(':firstName', $user->firstName);
                $request -> bindValue(':phone', $user->phone);
                if ($request -> execute()){
                    return ["error"=>false];
                }else{
                    throw new PDOException("user not add !");
                }
            } else {
                $update = "UPDATE user SET lastName=:lastName, firstName=:firstName, phone=:phone WHERE email=:email";
                $request = $pdo -> prepare($update);
                $request -> bindValue(':email', $user->email);
                $request -> bindValue(':lastName', $user->lastName);
                $request -> bindValue(':firstName', $user->firstName);
                $request -> bindValue(':phone', $user->phone);
                if ($request -> execute()){
                    return ["error"=>false];
                }else{
                    throw new PDOException("user not add !");
                }
            }
        } catch (\Exception $e) {
            return(["error"=>$e->getMessage()]);
        }
    }

    /**
     * @param string $email email of user to delete in database
     * @return array ["error" => false] on success | ["error" => message] on fail
     */
    public static function deleteUser($email){
        try{
            $pdo = self::connect();
            $delete = "DELETE FROM user WHERE email = :email";
            $request = $pdo -> prepare($delete);
            $request -> bindValue(':email', $email);
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