<?php
namespace Ads;

class Ad
{
    /** @var integer|null $id null when ad not yet inserted in database*/
    public $id = null;
    /** @var integer|null $user_email null when user not yet inserted in database*/
    public $user_email = null;
    /** @var integer|null $user_lastName null when user not yet inserted in database*/
    public $user_lastName = null;
    /** @var integer|null $user_firstName null when user not yet inserted in database*/
    public $user_firstName = null;
    /** @var integer|null $user_phone null when user not yet inserted in database*/
    public $user_phone = null;
    public $category_id;
    /** @var integer|null $category_name null when ad not yet inserted in database*/
    public $category_name = null;
    public $title;
    public $description;
    /** @var DateTime|null $creationDate null when ad not yet inserted in database*/
    public $creationDate = null;
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