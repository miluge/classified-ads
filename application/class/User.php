<?php
namespace Ads;

class User
{
    /** @var integer|null $id null when not inserted in database*/
    public $id;
    public $email;
    public $lastName;
    public $firstName;
    public $phone;

    /**
     * @param array $array ["attribute"=>value] to hydrate attributes
     * @return User newly created instance
     */
    public function __construct($array) {
        foreach($array as $attribute => $value){
            $this->$attribute = $value;
        }
        return $this;
    }
}