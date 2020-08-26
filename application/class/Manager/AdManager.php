<?php
namespace Ads\Manager;
use \Ads\Database as Database;
use \Ads\Ad as Ad;
use \Ads\Manager\UserManager as UserManager;

class AdManager extends Database
{
    /**
     * @param string $user_email user_email to ckeck in database
     * @return boolean|array true if exists, false if not | ["error" => message] on fail
     */
    public static function user_emailExists($user_email){
        try{
            $pdo = self::connect();
            $select = "SELECT id FROM ad WHERE user_email=:user_email"; 
            $request = $pdo -> prepare($select);
            $request -> bindValue(':user_email', $user_email);
            $request -> execute();
            return boolval($request->fetch());
        } catch (\Exception $e) {
            return(["error"=>$e->getMessage()]);
        }
    }

    /**
     * @param integer $id id of ad to select in database
     * @return Ad|array selected Ad instance on success | ["error" => message] on fail
     */
    public static function getAd($id){
        try{
            $pdo = self::connect();
            $select = "SELECT id, user_email, category_id, title, description, creationDate, validationDate, picture FROM ad WHERE id = :id";
            $request = $pdo -> prepare($select);
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
     * @return Ad[] array of all selected Ad instances on success | ["error" => message] on fail
     */
    public static function getAllAds(){
        try{
            $pdo = self::connect();
            $select = "SELECT id, user_email, category_id, title, description, creationDate, validationDate, picture FROM ad";
            $request = $pdo -> prepare($select);
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
     * @param id $id id of ad to delete in database
     * if user have NO OTHER ADS, delete user from user table
     * @return array ["error" => false] on success | ["error" => message] on fail
     */
    public static function deleteAd($id){
        try{
            $pdo = self::connect();
            $user_email = self::getAd($id)->user_email;
            $delete = "DELETE FROM ad WHERE id = :id";
            $request = $pdo -> prepare($delete);
            $request -> bindValue(':id', $id);
            if ($request -> execute()){
                if (! self::user_emailExists($user_id)){
                    UserManager::deleteUser($user_email);
                }
                return ["error" => false];
            } else {
                throw new \InvalidArgumentException("Ad not found !");
            }
        } catch (\Exception $e) {
            return(["error"=>$e->getMessage()]);
        }
    }

    /**
     * @param Ad $ad Ad instance to insert in database
     * @return array ["error" => false] on success | ["error" => message] on fail
     */
    public static function insertAd($ad){
        try{
            $pdo = self::connect();
            $insert = "INSERT INTO ad (user_email, category_id, title, description, creationDate, picture) VALUES (:user_email, :category_id, :title, :description, NOW(), :picture)";
            $request = $pdo -> prepare($insert);
            $request -> bindValue(':user_email', $ad->user_email);
            $request -> bindValue(':category_id', $ad->category_id);
            $request -> bindValue(':title', $ad->title);
            $request -> bindValue(':description', $ad->description);
            $request -> bindValue(':picture', $ad->picture);
            if ($request -> execute()){
                return ["error" => false];
            } else {
                throw new \PDOException("Ad not inserted !");
            }
        } catch (\Exception $e) {
            return(["error"=>$e->getMessage()]);
        }
    }
}