<?php
namespace Ads;

class File
{
    public $name;
    public $tmpName;
    public $extension;
    public $error;

    /**
     * @param array $array ["attribute"=>value] to hydrate attributes
     * @return File newly created instance
     */
    public function __construct($array) {
        foreach($array as $property => $value){
            $this->$property = $value;
        }
        return $this;
    }

    /**
     * @return boolean|string true if file is ok | error message if not
     */
    public function check(){
        if (!in_array($this->extension, ["png", "jpg", "jpeg"])){
            return "allowed file extension png, jpg, jpeg !";
        }
        $response = "";
        switch($this->error){
            case 0:
                $response = true;
                break;
            case 1: case 2:
                $response = "file is to large !";
                break;
            default:
                $response = "problem in file download !";
                break;
        }
        return $response;
    }
}