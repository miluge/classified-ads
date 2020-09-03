<?php
namespace Ads;

class File
{
    public string $name;
    public string $tmpName;
    public ?string $extension = null;
    public int $error;

    /**
     * @param array $file array of $_FILES["file"]
     */
    public function __construct(array $file) {
        $this->name = basename($file["name"]);
        $this->tmpName = $file["tmp_name"];
        $this->error = $file["error"];
    }

    /**
     * @return boolean|string true if file is ok | error message if not
     */
    public function check(){
        $response = "";
        if (empty($this->name)){// check file name
            $response = "no file name provided !";
        }else{// check file extension
            $this->extension = strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
            if (!in_array($this->extension, ["png", "jpg", "jpeg"])){
                $response = "allowed file extension png, jpg, jpeg !";
            }else{// check error status
                switch($this->error){
                    case 0:
                        $response = true;
                        break;
                    case 1: case 2:
                        $response = "file is to large !";
                        break;
                    default:
                        $response = "problem in file upload !";
                        break;
                }
            }
        }
        return $response;
    }
}