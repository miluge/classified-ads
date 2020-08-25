<?php
namespace Ads\Manager;
use \Ads\Database as Database;
use \Ads\Ad as Ad;


class AdManager extends Database
{
    /**
     * @param id $id id of ad to fetch in classified-ads database
     * @return Ad|array fetched Ad instance on success | ["exception" => message] on fail 
     */
    public function getAdById($id){
        try{
            $database = $this -> connect();
            $select = "SELECT id, user_id, category_id, title, description, creationDate, validationDate, picture FROM Ad WHERE id = :id";
            $request = $database -> prepare($select);
            $request -> bindValue(':id', $id);
            $request -> execute();
            if ($ad = $request->fetch(\PDO::FETCH_ASSOC)) {
                return new Ad($ad);
            } else {
                throw new Exception("Ad not found !");
            }
        } catch (Exception $e) {
            return(["exception"=>$e->getMessage()]);
        }
    }
}