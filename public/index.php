<?php

//define application parameters
define("BASE_PATH","");
define("SERVER_URI",$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].BASE_PATH);

// autoload
require_once dirname(dirname(__FILE__))."/vendor/autoload.php";

// namespace
use \Ads\Ad as Ad;
use \Ads\User as User;
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
    $ads = AdManager::getAllValidatedAds();
    $categories = CategoryManager::getAllCategories();
    $twig = loadTwig();
    $template = $twig->load('index.html.twig');
    echo $template->render(["ads"=>$ads,"categories"=>$categories,"SERVER_URI"=>SERVER_URI]);
});

// add template route
$router->map('GET','/add',function(){
    //load add template passing all Category objects
    $categories = CategoryManager::getAllCategories();
    $twig = loadTwig();
    $template = $twig->load('add/add_form.html.twig');
    echo $template->render(["categories"=>$categories,"SERVER_URI"=>SERVER_URI]);
});

// edit template route
$router->map('GET','/edit/[i:id]',function($id){
    //load edit template passing Ad(id), all Category objects
    $ad = AdManager::getAd($id);
    $categories = CategoryManager::getAllCategories();
    $twig = loadTwig();
    $template = $twig->load('edit/edit_form.html.twig');
    echo $template->render(["ad"=>$ad,"categories"=>$categories,"SERVER_URI"=>SERVER_URI]);
});

// details template route
$router->map('GET','/details/[i:id]',function($id){
    //load details template passing Ad(id), all Category, all User objects
    $ad = AdManager::getAd($id);
    $twig = loadTwig();
    $template = $twig->load('details.html.twig');
    echo $template->render(["ad"=>$ad,"SERVER_URI"=>SERVER_URI]);
});

// add form handling route
$router->map('GET','/addform',function(){
    //check if picture is posted
    if(isset($_FILES["picture"]) && not_empty($_FILES["picture"]["name"])){
        //HANDLE FILE UPLOAD
    }else{
        $_GET["picture"] = "default.png";
    }
    //insert User
    $user = new User(["email"=>$_GET["email"], "lastName"=>$_GET["lastName"], "firstName"=>$_GET["firstName"], "phone"=>$_GET["phone"]]);
    UserManager::insertUser($user);
    //insert Ad
    $ad = new Ad(["user_email"=>$_GET["email"], "category_id"=>$_GET["category_id"], "title"=>$_GET["title"], "description"=>$_GET["description"], "picture"=>$_GET["picture"]]);
    AdManager::insertAd($ad);
    // redirect to index template
    header("Location:/");
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