<?php
namespace Ads\Manager;
use \Ads\Database as Database;
use \Ads\User as User;

class UserManager extends Database
{
    /**
     * @param string $email email of user to select in database
     * @return User|boolean selected User instance on success | false on fail
     */
    public static function get(string $email){
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
            return(false);
        }
    }

    /**
     * @return User[]|boolean all User instances on success | false on fail
     */
    public static function getAll(){
        try{
            $pdo = self::connect();
            $select = "SELECT email, lastName, firstName, phone FROM user";
            $request = $pdo -> prepare($select);
            $request -> execute();
            if ($users = $request->fetchAll()) {
                return (array_map(function($user){
                    return new User($user);
                }, $users));
            } else {
                throw new \LengthException("No user found !");
            }
        } catch (\Exception $e) {
            return(false);
        }
    }

    /**
     * @param User $user User instance to insert in database
     * check if user already exists:
     * insert if not
     * update if so
     * @return boolean true on success | false on fail
     */
    public static function insert(User $user){
        try{
            $pdo = self::connect();
            $previousUser = self::get($user->email);
            if (is_array($previousUser)) {
                $insert = "INSERT INTO user (email, lastName, firstName, phone) VALUES (:email, :lastName, :firstName, :phone)";
                $request = $pdo -> prepare($insert);
                $request -> bindValue(':email', $user->email);
                $request -> bindValue(':lastName', $user->lastName);
                $request -> bindValue(':firstName', $user->firstName);
                $request -> bindValue(':phone', $user->phone);
                if ($request -> execute()){
                    return true;
                }else{
                    throw new \PDOException("user not add !");
                }
            } else {
                $update = "UPDATE user SET lastName=:lastName, firstName=:firstName, phone=:phone WHERE email=:email";
                $request = $pdo -> prepare($update);
                $request -> bindValue(':email', $user->email);
                $request -> bindValue(':lastName', $user->lastName);
                $request -> bindValue(':firstName', $user->firstName);
                $request -> bindValue(':phone', $user->phone);
                if ($request -> execute()){
                    return true;
                }else{
                    throw new \PDOException("user not add !");
                }
            }
        } catch (\Exception $e) {
            return(false);
        }
    }

    /**
     * @param string $email email of user to delete in database
     * @return boolean true on success | false on fail
     */
    public static function delete(string $email){
        try{
            $pdo = self::connect();
            $delete = "DELETE FROM user WHERE email = :email";
            $request = $pdo -> prepare($delete);
            $request -> bindValue(':email', $email);
            if ($request->execute()){
                return true;
            }else {
                throw new \InvalidArgumentException("User not found !");
            }
        } catch (\Exception $e) {
            return(false);
        }
    }
}