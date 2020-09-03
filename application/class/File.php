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
            $response = "no_file_name_provided_!";
        }else{// check file extension
            $this->extension = strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
            if (!in_array($this->extension, ["png", "jpg", "jpeg"])){
                $response = "allowed_file_extension_png,_jpg,_jpeg_!";
            }else{// check error status
                switch($this->error){
                    case 0:
                        $response = true;
                        break;
                    case 1: case 2:
                        $response = "file_is_to_large_!";
                        break;
                    default:
                        $response = "problem_in_file_upload_!";
                        break;
                }
            }
        }
        return $response;
    }
}