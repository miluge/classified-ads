<?php
namespace Ads;

class Ad
{
    public ?int $id = null;
    public string $user_email;
    public ?string $user_lastName = null;
    public ?string $user_firstName = null;
    public ?string $user_phone = null;
    public int $category_id;
    public ?string $category_name = null;
    public string $title;
    public string $description;
    public ?string $creationDate = null;
    public ?string $validationDate = null;
    public string $picture = "default.png";

    /**
     * @param array $array ["attribute"=>value] to hydrate attributes
     * @return Ad newly created instance
     */
    public function __construct(array $array) {
        foreach($array as $property => $value){
            $this->$property = $value;
        }
        return $this;
    }
}