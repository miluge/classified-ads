<?php
namespace Ads\Manager;
use \Ads\Database as Database;
use \Ads\Category as Category;

class CategoryManager extends Database
{
    /**
     * @param integer $id id of category to fetch in database
     * @return Category|array fetched Category instance on success | ["error" => message] on fail
     */
    public static function getCategory($id){
        try{
            $pdo = self::connect();
            $select = "SELECT id, name FROM category WHERE id = :id";
            $request = $pdo -> prepare($select);
            $request -> bindValue(':id', $id);
            $request -> execute();
            if ($category = $request->fetch()) {
                return new Category($category);
            } else {
                throw new \InvalidArgumentException("Category not found !");
            }
        } catch (\Exception $e) {
            return(["error"=>$e->getMessage()]);
        }
    }

    /**
     * @return Category[] array of fetched Category instances on success | ["error" => message] on fail
     */
    public static function getAllCategories(){
        try{
            $pdo = self::connect();
            $select = "SELECT id, name FROM category";
            $request = $pdo -> prepare($select);
            $request -> execute();
            if ($categories = $request->fetchAll()) {
                return (array_map(function($category){
                    return new Category($category);
                }, $categories));
            } else {
                throw new \LengthException("No ads found !");
            }
        } catch (\Exception $e) {
            return(["error"=>$e->getMessage()]);
        }
    }
}