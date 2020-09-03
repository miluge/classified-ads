<?php
namespace Ads;

class User
{
    public string $email;
    public string $lastName;
    public string $firstName;
    public string $phone;

    /**
     * @param array $array ["attribute"=>value] to hydrate attributes
     * @return User newly created instance
     */
    public function __construct(array $array) {
        foreach($array as $property => $value){
            $this->$property = $value;
        }
        return $this;
    }
}