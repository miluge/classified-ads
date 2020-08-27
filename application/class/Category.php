<?php
namespace Ads;

class Category
{
    public $id;
    public $name;

    /**
     * @param array $array ["attribute"=>value] to hydrate attributes
     * @return Category newly created instance
     */
    public function __construct($array) {
        foreach($array as $property => $value){
            if (property_exists($property)){
                $this->$property = $value;
            }
        }
        return $this;
    }
}