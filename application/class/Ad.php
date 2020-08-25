<?php
namespace Ads;

class Ad
{
    public $id;
    public $title;
    public $category;
    public $description;
    public $picture;
    public $creationDate;
    public $validationDate;

    //take array of ["attribute"=>value] and hydrate object
    public function __construct(array $array) {
        foreach($array as $attribute => $value){
            $this->$attribute = $value;
        }
    }
}