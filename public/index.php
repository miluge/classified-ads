<?php

//define application parameters
define("DOCUMENT_ROOT",$_SERVER["DOCUMENT_ROOT"]);
define("BASE_PATH","");
define("SERVER_NAME",$_SERVER["SERVER_NAME"]);
define("REQUEST_SCHEME",$_SERVER["REQUEST_SCHEME"]);
//REQUEST_SCHEME SERVER_NAME BASE_PATH pour reconstruire les chemins des liens

require_once dirname(DOCUMENT_ROOT)."/vendor/autoload.php";

//load twig function
function loadTwig(){
    $loader = new \Twig\Loader\FilesystemLoader(dirname(DOCUMENT_ROOT)."/application/template");
    return new \Twig\Environment($loader, [
        'cache' => false,
        // 'cache' => dirname(DOCUMENT_ROOT)."/application/cache",
    ]);
}

// ALTOROUTER
$router = new AltoRouter();
$router->setBasePath(BASE_PATH);

// index route
$router->map('GET','/',function(){
    $ads = \Ads\Manager\AdManager::getAllAds();
    $categories = \Ads\Manager\CategoryManager::getAllCategories();
    $users = \Ads\Manager\UserManager::getAllUsers();
    $twig = loadTwig();
    //load index template passing all Ad, all Category, all User objects
    $template = $twig->load('index.html.twig');
    echo $template->render(["ads"=>$ads,"categories"=>$categories,"users"=>$users,"BASE_PATH"=>BASE_PATH,"SERVER_NAME"=>SERVER_NAME,"REQUEST_SCHEME"=>REQUEST_SCHEME]);
});

// add route
$router->map('GET','/add',function(){
    $categories = \Ads\Manager\CategoryManager::getAllCategories();
    $twig = loadTwig();
    //load add template passing all Category objects
    $template = $twig->load('add/add_form.html.twig');
    echo $template->render(["categories"=>$categories, "BASE_PATH"=>BASE_PATH,"SERVER_NAME"=>SERVER_NAME,"REQUEST_SCHEME"=>REQUEST_SCHEME]);
});

// edit route
$router->map('GET','/edit/[i:id]',function($id){
    $ad = \Ads\Manager\AdManager::getAd($id);
    $categories = \Ads\Manager\CategoryManager::getAllCategories();
    $twig = loadTwig();
    //load edit template passing Ad(id), all Category objects
    $template = $twig->load('edit/edit_form.html.twig');
    echo $template->render(["ad"=>$ad,"categories"=>$categories,"BASE_PATH"=>BASE_PATH,"SERVER_NAME"=>SERVER_NAME,"REQUEST_SCHEME"=>REQUEST_SCHEME]);
});

// details route
$router->map('GET','/details/[i:id]',function($id){
    $ad = \Ads\Manager\AdManager::getAd($id);
    $categories = \Ads\Manager\CategoryManager::getAllCategories();
    $users = \Ads\Manager\UserManager::getAllUsers();
    $twig = loadTwig();
    //load details template passing Ad(id), all Category, all User objects
    $template = $twig->load('details.html.twig');
    echo $template->render(["ad"=>$ad,"categories"=>$categories,"users"=>$users,"BASE_PATH"=>BASE_PATH,"SERVER_NAME"=>SERVER_NAME,"REQUEST_SCHEME"=>REQUEST_SCHEME]);
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