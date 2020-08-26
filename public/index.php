<?php

require_once '../vendor/autoload.php';

//load twig function
function loadTwig(){
    $loader = new \Twig\Loader\FilesystemLoader('../application/template');
    return new \Twig\Environment($loader, [
        'cache' => '../application/cache',
    ]);
}

// ALTOROUTER
$router = new AltoRouter();
$router->setBasePath('/public/');

// index.php route
$router->map('GET','*',function(){
    $ads = \Ads\Manager\AdManager::getAllAds();
    $categories = \Ads\Manager\CategoryManager::getAllCategories();
    $users = \Ads\Manager\UserManager::getAllUsers();
    $twig = loadTwig();
    //load index template passing all Ad, Category, User objects
    $template = $twig->load('index.html.twig');
    echo $template->render(["ads"=>$ads,"categories"=>$categories,"users"=>$users]);
});

// Match url
$match = $router->match();
var_dump($match);

// Process request
if( is_array($match) && is_callable( $match['target'] ) ) {
	call_user_func_array( $match['target'], $match['params'] ); 
} else {
	// no route was matched
	header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}