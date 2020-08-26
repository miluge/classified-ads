<?php
namespace Ads;

class User
{
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