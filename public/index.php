<?php

require_once '../vendor/autoload.php';

//define application parameters
define("BASE_PATH","");
define("SERVER_NAME",$_SERVER["SERVER_NAME"]);
define("REQUEST_SCHEME",$_SERVER["REQUEST_SCHEME"]);
//REQUEST_SCHEME SERVER_NAME BASE_PATH pour reconstruire les chemins des liens

//load twig function
function loadTwig(){
    $loader = new \Twig\Loader\FilesystemLoader('../application/template');
    return new \Twig\Environment($loader, [
        'cache' => '../application/cache',
    ]);
}

// ALTOROUTER
$router = new AltoRouter();
$router->setBasePath(BASE_PATH);

// index.php route
$router->map('GET','/',function(){
    $ads = \Ads\Manager\AdManager::getAllAds();
    $categories = \Ads\Manager\CategoryManager::getAllCategories();
    $users = \Ads\Manager\UserManager::getAllUsers();
    $twig = loadTwig();
    //load index template passing all Ad, Category, User objects
    $template = $twig->load('index.html.twig');
    echo $template->render(["ads"=>$ads,"categories"=>$categories,"users"=>$users,"BASE_PATH"=>BASE_PATH,"SERVER_NAME"=>SERVER_NAME,"REQUEST_SCHEME"=>REQUEST_SCHEME]);
});

// Match url
$match = $router->match();

// Process request
if( is_array($match) && is_callable( $match['target'] ) ) {
	call_user_func_array( $match['target'], $match['params'] ); 
} else {
	// no route was matched
	header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}