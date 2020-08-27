<?php
namespace Ads;

class Ad
{
    /** @var integer|null $id null when ad not yet inserted in database*/
    public $id = null;
    /** @var string $user_email*/
    public $user_email;
    /** @var string|null $user_lastName null when ad not yet inserted in database*/
    public $user_lastName = null;
    /** @var string|null $user_firstName null when ad not yet inserted in database*/
    public $user_firstName = null;
    /** @var string|null $user_phone null when ad not yet inserted in database*/
    public $user_phone = null;
    /** @var integer $category_id*/
    public $category_id;
    /** @var string|null $category_name null when ad not yet inserted in database*/
    public $category_name = null;
    /** @var string $title*/
    public $title;
    /** @var string $description*/
    public $description;
    /** @var DateTime|null $creationDate null when ad not yet inserted in database*/
    public $creationDate = null;
    /** @var DateTime|null $validationDate null when ad not yet validated*/
    public $validationDate = null;
    /** @var string $picture contain default picture name if no user picture added*/
    public $picture;

    /**
     * @param array $array ["attribute"=>value] to hydrate attributes
     * @return Ad newly created instance
     */
    public function __construct($array) {
        foreach($array as $property => $value){
            $this->$property = $value;
        }
        return $this;
    }
}