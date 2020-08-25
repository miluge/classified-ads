<?php
namespace Ads\Manager;
use \Ads\Database as Database;
use \Ads\Category as Category;

class CategoryManager extends Database
{
    /**
     * @param id $id id of category to fetch in classified-ads database
     * @return Category|array fetched Category instance on success | ["exception" => message] on fail 
     */
    public function getCategoryById($id){
        try{
            $database = $this -> connect();
            $select = "SELECT id, name FROM category WHERE id = :id";
            $request = $database -> prepare($select);
            $request -> bindValue(':id', $id);
            $request -> execute();
            if ($category = $request->fetch(\PDO::FETCH_ASSOC)) {
                return new Category($category);
            } else {
                throw new Exception("Category not found !");
            }
        } catch (Exception $e) {
            return(["exception"=>$e->getMessage()]);
        }
    }
}