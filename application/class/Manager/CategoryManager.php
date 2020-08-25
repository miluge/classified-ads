<?php
namespace Ads\Manager;
use \Ads\Database as Database;
use \Ads\Category as Category;

class CategoryManager extends Database
{
    /**
     * @param id $id [id of category to fetch in classified-ads database]
     * @return Category|array [fetched Category instance on success | ["error" => message] on fail]
     */
    public function getCategoryById($id){
        try{
            $database = $this -> connect();
            $select = "SELECT id, name FROM category WHERE id = :id";
            $request = $database -> prepare($select);
            $request -> bindValue(':id', $id);
            $request -> execute();
            if ($category = $request->fetch()) {
                return new Category($category);
            } else {
                throw new \InvalidArgumentException("Category not found !");
            }
        } catch (\InvalidArgumentException $e) {
            return(["error"=>$e->getMessage()]);
        }
    }
}