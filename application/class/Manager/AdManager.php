<?php
namespace Ads\Manager;
use \Ads\Database as Database;
use \Ads\Ad as Ad;
use \Ads\Manager\User as UserManager;

class AdManager extends Database
{
    /**
     * @param integer $id [id of ad to be fetched in database]
     * @return integer|array [user_id of ad on success | ["error" => message] on fail]
     */
    public static function getUser_id($id){
        try{
            $database = self::connect();
            $select = "SELECT user_id FROM ad WHERE id=:id"; 
            $request = $database -> prepare($select);
            $request -> bindValue(':id', $id);
            $request -> execute();
            if ($user_id = $request->fetch()){
                return $user_id;
            }else{
                throw new \InvalidArgumentException("Ad user not found !");
            }
        } catch (\Exception $e) {
            return(["error"=>$e->getMessage()]);
        }
    }

    /**
     * @param integer $user_id [user_id to filter ads fetched in database]
     * @return boolean|array [["user_id"=>$user_id] if exists | false if not exists | ["error" => message] on fail]
     */
    public static function user_idExists($user_id){
        try{
            $database = self::connect();
            $select = "SELECT user_id FROM ad WHERE user_id=:user_id"; 
            $request = $database -> prepare($select);
            $request -> bindValue(':user_id', $user_id);
            $request -> execute();
            return $request->fetch();
        } catch (\Exception $e) {
            return(["error"=>$e->getMessage()]);
        }
    }

    /**
     * @param integer $id [id of ad to fetch in database]
     * @return Ad|array [fetched Ad instance on success | ["error" => message] on fail]
     */
    public static function getAdById($id){
        try{
            $database = self::connect();
            $select = "SELECT id, user_id, category_id, title, description, creationDate, validationDate, picture FROM ad WHERE id = :id";
            $request = $database -> prepare($select);
            $request -> bindValue(':id', $id);
            $request -> execute();
            if ($ad = $request->fetch()) {
                return new Ad($ad);
            } else {
                throw new \InvalidArgumentException("Ad not found !");
            }
        } catch (\Exception $e) {
            return(["error"=>$e->getMessage()]);
        }
    }

    /**
     * @return array [array of fetched Ad instances on success | ["error" => message] on fail]
     */
    public static function getAllAds(){
        try{
            $database = self::connect();
            $select = "SELECT id, user_id, category_id, title, description, creationDate, validationDate, picture FROM ad";
            $request = $database -> prepare($select);
            $request -> execute();
            if ($ads = $request->fetchAll()) {
                return (array_map(function($ad){
                    return new Ad($ad);
                }, $ads));
            } else {
                throw new \LengthException("No ads found !");
            }
        } catch (\Exception $e) {
            return(["error"=>$e->getMessage()]);
        }
    }

    /**
     * @param id $id [id of ad to be deleted in database]
     * if user have NO other ads, delete user from user table
     * @return array [["error" => false] on success | ["error" => message] on fail]
     */
    public static function deleteAdById($id){
        try{
            $database = self::connect();
            $user_id = self::getUser_id($id);
            $delete = "DELETE FROM ad WHERE id = :id";
            $request = $database -> prepare($delete);
            $request -> bindValue(':id', $id);
            if ($request -> execute()){
                if (! self::user_idExists($user_id)){
                    \UserManager::deleteUserById($user_id);
                }
                return ["error" => false];
            } else {
                throw new \InvalidArgumentException("Ad not found !");
            }
        } catch (\Exception $e) {
            return(["error"=>$e->getMessage()]);
        }
    }
}