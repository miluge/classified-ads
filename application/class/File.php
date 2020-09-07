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
     * @return boolean true if file is ok, false if not
     */
    public function check(){
        $response = true;
        // check file name
        if (empty($this->name)){
            $response = false;
        }
        // check file extension
        $this->extension = strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
        if (!in_array($this->extension, ["png", "jpg", "jpeg"])){
            $response = false;
        }
        // check error status
        if ($this->error!==0){
            $response = false;
        }
        return $response;
    }
}