<?php
namespace Ads;

class Ad
{
    /** @var integer|null $id null when not inserted in database*/
    public $id = null;
    /** @var integer|null $user_id null when user not inserted in database*/
    public $user_id = null;
    public $category_id;
    public $title;
    public $description;
    public $creationDate;
    /** @var DateTime|null $validationDate null when ad not yet validated*/
    public $validationDate = null;
    /** @var string $picture contain default path if no user picture added*/
    public $picture = "default path using category id";

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