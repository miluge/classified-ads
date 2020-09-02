<?php
namespace Ads;

class Category
{
    public int $id;
    public string $name;
    public string $color;
    public string $image;

    /**
     * @param array $array ["attribute"=>value] to hydrate attributes
     * @return Category newly created instance
     */
    public function __construct(array $array) {
        foreach($array as $property => $value){
            $this->$property = $value;
        }
        return $this;
    }
}