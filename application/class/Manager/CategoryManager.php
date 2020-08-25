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

    /**
     * @return array [array of fetched Category instances on success | ["error" => message] on fail]
     */
    public function getAllCategories(){
        try{
            $database = $this -> connect();
            $select = "SELECT id, name FROM category";
            $request = $database -> prepare($select);
            $request -> execute();
            if ($categories = $request->fetchAll()) {
                return (array_map(function($category){
                    return new Category($category);
                }, $categories));
            } else {
                throw new \LengthException("No ads found !");
            }
        } catch (\LengthException $e) {
            return(["error"=>$e->getMessage()]);
        }
    }
}