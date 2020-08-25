<?php
namespace Ads;

class Ad
{
    public $id;
    public $user_id;
    public $category_id;
    public $title;
    public $description;
    public $creationDate;
    public $validationDate;
    public $picture;

    /**
     * @param array $array ["attribute"=>value] to hydrate attributes
     * @return Ad newly created instance
     */
    public function __construct($array) {
        foreach($array as $attribute => $value){
            $this->$attribute = $value;
        }
        return $this;
    }
}