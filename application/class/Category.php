<?php
namespace Ads;

class Category
{
    public $id;
    public $name;

    //take array of ["attribute"=>value] and hydrate object
    public function __construct(array $array) {
        foreach($array as $attribute => $value){
            $this->$attribute = $value;
        }
    }
}