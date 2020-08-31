<?php

//define application parameters
define("BASE_PATH","");
define("SERVER_URI",$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].BASE_PATH);

// autoload
require_once dirname(dirname(__FILE__))."/vendor/autoload.php";

// namespace
use \Ads\Ad as Ad;
use \Ads\User as User;
use \Ads\File as File;
use \Ads\Manager\AdManager as AdManager;
use \Ads\Manager\CategoryManager as CategoryManager;
use \Ads\Manager\UserManager as UserManager;

//load twig function
function loadTwig(){
    $loader = new \Twig\Loader\FilesystemLoader(dirname(dirname(__FILE__))."/application/template");
    return new \Twig\Environment($loader, [
        'cache' => false,
        // 'cache' => dirname(__FILE__)."/application/cache",
    ]);
}

// ALTOROUTER
$router = new AltoRouter();
$router->setBasePath(BASE_PATH);

// index template route
$router->map('GET','/',function(){
    //load index template passing all Ad, all Category objects
    $ads = AdManager::getAllValidated();
    $categories = CategoryManager::getAll();
    $twig = loadTwig();
    $template = $twig->load('index.html.twig');
    echo $template->render([ "ads"=>$ads , "categories"=>$categories , "SERVER_URI"=>SERVER_URI ]);
});

// add template route
$router->map('GET','/add',function(){
    //load add template passing all Category objects
    $categories = CategoryManager::getAll();
    $twig = loadTwig();
    $template = $twig->load('add/add_form.html.twig');
    echo $template->render([ "categories"=>$categories , "SERVER_URI"=>SERVER_URI ]);
});

// edit template route
$router->map('GET','/edit/[i:id]',function($id){
    //load edit template passing Ad(id), all Category objects
    $ad = AdManager::get($id);
    $categories = CategoryManager::getAll();
    $twig = loadTwig();
    $template = $twig->load('edit/edit_form.html.twig');
    echo $template->render([ "ad"=>$ad , "categories"=>$categories , "SERVER_URI"=>SERVER_URI ]);
});

// details template route
$router->map('GET','/details/[i:id]',function($id){
    if (AdManager::isValidated($id)){
        //load details template passing Ad(id)
        $ad = AdManager::get($id);
        $twig = loadTwig();
        $template = $twig->load('details/details.html.twig');
        echo $template->render([ "ad"=>$ad , "SERVER_URI"=>SERVER_URI ]);
    }else{
        // redirect to index
        header("Location:/");
    }
});

// add ad form handling route
$router->map('GET','/addform',function(){
    //insert User
    $user = new User([ "email"=>$_GET["email"] , "lastName"=>$_GET["lastName"] , "firstName"=>$_GET["firstName"] , "phone"=>$_GET["phone"] ]);
    UserManager::insert($user);
    //insert Ad
    $ad = new Ad([ "user_email"=>$_GET["email"] , "category_id"=>$_GET["category_id"] , "title"=>$_GET["title"] , "description"=>$_GET["description"]]);
    AdManager::insert($ad);
    //check if picture is posted
    if(isset($_FILES["picture"]) && not_empty($_FILES["picture"]["name"])){
        $name = basename($_FILES["picture"]["name"]);
        $tmpName = $_FILES["picture"]["tmp_name"];
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $error = $_FILES["picture"]["error"];
        $file = new File([ "name"=>$name , "tmpName"=>$tmpName , "extension"=>$extension , "error"=>$error ]);
        if ($file->check()===true){
            //get new Ad, update picture name, upload file
            $newId = AdManager::lastInsertId();
            $newAd = AdManager::get($newId);
            $newAd->picture = $file->name;
            AdManager::update($newAd);
            move_uploaded_file($file->tmpName, dirname(__FILE__)."/assets/pictures/".$newId."-".$file->name);
        }
    }
    // redirect to index
    header("Location:/");
});

// edit ad form handling route
$router->map('GET','/editform/[i:id]',function($id){
    //check if picture is posted
    if(isset($_FILES["picture"]) && not_empty($_FILES["picture"]["name"])){
        //HANDLE FILE UPLOAD
    }else{
        $_GET["picture"] = "default.png";
    }
    //update Ad
    $ad = new Ad([ "id"=> $id , "category_id"=>$_GET["category_id"] , "title"=>$_GET["title"] , "description"=>$_GET["description"] , "picture"=>$_GET["picture"] ]);
    AdManager::update($ad);
    // redirect to ad details
    header("Location:/details/".$id);
});

// validate ad route
$router->map('GET','/validate/[i:id]',function($id){
    //check if picture is validated
    if (! AdManager::isValidated($id)){
        AdManager::validate($id);
    }
    // redirect to details template
    header("Location:/details/".$id);
});

// match url
$match = $router->match();

// process request
if( is_array($match) && is_callable( $match['target'] ) ) {
	call_user_func_array( $match['target'], $match['params'] ); 
} else {
	// no route was matched
	header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}