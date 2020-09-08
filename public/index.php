<?php

// define application parameters
define("BASE_PATH","");
define("SERVER_URI",$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].BASE_PATH);

// autoload
require_once dirname(dirname(__FILE__))."/vendor/autoload.php";

// namespace
use \Ads\Ad as Ad;
use \Ads\User as User;
use \Ads\File as File;
use \Ads\Mail as Mail;
use \Ads\Crypt as Crypt;
use \Ads\Twig as Twig;
use \Ads\Validation as Validation;
use \Ads\Manager\AdManager as AdManager;
use \Ads\Manager\CategoryManager as CategoryManager;
use \Ads\Manager\UserManager as UserManager;

// ALTOROUTER
$router = new AltoRouter();
$router->setBasePath(BASE_PATH);

// index page route
$router->map('GET','/',function(){
    // get all validated Ads
    if ( ($ads = AdManager::getAllValidated()) === false){
        header("Location: /message/".urlencode("Ads cannot be found !"));
        exit;
    }
    // get all Categories
    if ( ($categories = CategoryManager::getAll()) === false){
        header("Location: /message/".urlencode("Categories cannot be found !"));
        exit;
    }
    // echo index page
    echo Twig::getRender("index.html.twig", [ "ads"=>$ads , "categories"=>$categories , "SERVER_URI"=>SERVER_URI ]);
});

// index page route with message
$router->map('GET','/message/[**:message]',function($message){
    // get all validated Ads
    $ads = AdManager::getAllValidated();
    // get all Categories
    $categories = CategoryManager::getAll();
    // echo index page with message
    echo Twig::getRender("index.html.twig", [ "ads"=>$ads , "categories"=>$categories , "SERVER_URI"=>SERVER_URI , "message"=>urldecode($message) ]);
});

// details page route
$router->map('GET','/details/[i:id]',function($id){
    // check Ad $id
    if ( ($ad = Validation::ad($id)) === false ){
        // redirect to index page if Ad $id doesn't exist
        header("Location:/message/".urlencode("Requested ad doesn't exist !"));
        exit;
    }
    // check if Ad is validated
    if (!AdManager::isValidated($id)){
        // redirect to index page if Ad is not validated
        header("Location:/message/".urlencode("This ad is not yet validated !"));
        exit;
    }
    // echo details page
    echo Twig::getRender("details/details.html.twig", [ "ad"=>$ad , "SERVER_URI"=>SERVER_URI ]);
});

// details page route with message
$router->map('GET','/details/[i:id]/[**:message]',function($id, $message){
    // check Ad $id
    if ( ($ad = Validation::ad($id)) === false ){
        // redirect to index page if Ad $id doesn't exist
        header("Location:/message/".urlencode("Requested ad doesn't exist !"));
        exit;
    }
    // check if Ad is validated
    if (!AdManager::isValidated($id)){
        // redirect to index page if Ad is not validated
        header("Location:/message/".urlencode("This ad is not yet validated !"));
        exit;
    }
    // echo details page
    echo Twig::getRender("details/details.html.twig", [ "ad"=>$ad , "SERVER_URI"=>SERVER_URI , "message"=>urldecode($message) ]);
});

// add page route
$router->map('GET','/add',function(){
    // get all Categories
    if ( ($categories = CategoryManager::getAll()) === false){
        header("Location: /message/".urlencode("Categories cannot be found !"));
        exit;
    }
    // echo add form page
    echo Twig::getRender("add/add_form.html.twig", [ "categories"=>$categories , "SERVER_URI"=>SERVER_URI ]);
});

// add page route with message
$router->map('GET','/add/[:messageType]',function($messageType){
    // get all Categories
    $categories = CategoryManager::getAll();
    // echo add form page with input error message
    echo Twig::getRender("add/add_form.html.twig", [ "categories"=>$categories , "SERVER_URI"=>SERVER_URI, "messageType"=>urldecode($messageType) ]);
});

// add form handling route
$router->map('POST','/addform',function(){
    // check User data
    if ( ($data = Validation::userData()) !== true){
        header("Location: /add/".$data);
        exit;
    }
    // insert User
    $user = new User([ "email"=>$_POST["email"] , "lastName"=>$_POST["lastName"] , "firstName"=>$_POST["firstName"] , "phone"=>$_POST["phone"] ]);
    if (!UserManager::insert($user)){
        header("Location: /message/".urlencode("User couldn't be added !"));
        exit;
    }
    // check Ad data
    if ( ($data = Validation::adData()) !== true){
        AdManager::deleteUserIfUseless($_POST["email"]);
        header("Location: /add/".$data);
        exit;
    }
    // insert Ad
    $ad = new Ad([ "user_email"=>$_POST["email"] , "category_id"=>$_POST["category_id"] , "title"=>$_POST["title"] , "description"=>$_POST["description"]]);
    if (!$ad = AdManager::insert($ad)){
        AdManager::deleteUserIfUseless($_POST["email"]);
        header("Location: /message/".urlencode("Ad couldn't be added !"));
        exit;
    }
    // handle picture file if posted
    if(isset($_FILES["picture"]) && !empty($_FILES["picture"]["name"])){
        $file = new File($_FILES["picture"]);
        if (!$file->check()){
            AdManager::delete($ad->id);
            header("Location: /add/picture");
            exit;
        }
        // update Ad picture with id in picture name
        $picture = $ad->id."-".$file->name;
        $ad->picture = $picture;
        if (!($ad = AdManager::update($ad)) || !move_uploaded_file($file->tmpName, dirname(__FILE__)."/assets/pictures/".$picture)){
            AdManager::delete($ad->id);
            header("Location: /add/picture");
            exit;
        }
    }
    // send validation mail
    if (Mail::sendValidate($ad, SERVER_URI)===0){
        AdManager::delete($ad->id);
        // redirect to add page with email error message
        header("Location:/add/email");
        exit;
    }
    // redirect to index page with confirmation message
    header("Location:/message/".urlencode("Your ad has been submitted, please check your email to validate it !"));
    exit;
});

// edit page route
$router->map('GET','/edit/[i:id]/[**:cryptedMail]',function($id, $cryptedMail){
    // check Ad $id
    if ( ($ad = Validation::ad($id)) === false ){
        // redirect to index page if Ad $id doesn't exist
        header("Location:/message/".urlencode("Requested ad doesn't exist !"));
        exit;
    }
    // check cryptedMail
    if (!Validation::checkMail($ad->user_email, $cryptedMail)){
        // redirect to index page if User don't own Ad
        header("Location:/message/".urlencode("You're not allowed to modify this ad !"));
        exit;
    }
     // get all Categories
     if ( ($categories = CategoryManager::getAll()) === false){
        header("Location: /message/".urlencode("Categories cannot be found !"));
        exit;
    }
    // echo edit form page
    echo Twig::getRender("edit/edit_form.html.twig", [ "ad"=>$ad , "categories"=>$categories , "SERVER_URI"=>SERVER_URI , "cryptedMail"=>$cryptedMail ]);
});

// edit page route with message
$router->map('GET','/edit/message/[:messageType]/[i:id]/[**:cryptedMail]',function($messageType, $id, $cryptedMail){
    // check Ad $id
    if ( ($ad = Validation::ad($id)) === false ){
        // redirect to index page if Ad $id doesn't exist
        header("Location:/message/".urlencode("Requested ad doesn't exist !"));
        exit;
    }
    // check cryptedMail
    if (!Validation::checkMail($ad->user_email, $cryptedMail)){
        // redirect to index page if User don't own Ad
        header("Location:/message/".urlencode("You're not allowed to modify this ad !"));
        exit;
    }
    // get all Categories
    if ( ($categories = CategoryManager::getAll()) === false){
        header("Location: /message/".urlencode("Categories cannot be found !"));
        exit;
    }
    echo Twig::getRender("edit/edit_form.html.twig", [ "ad"=>$ad , "categories"=>$categories , "SERVER_URI"=>SERVER_URI , "cryptedMail"=>$cryptedMail , "messageType"=>urldecode($messageType) ]);
});

// edit form handling route
$router->map('POST','/editform/[i:id]/[**:cryptedMail]',function($id, $cryptedMail){
    // check Ad $id
    if ( ($ad = Validation::ad($id)) === false){
        // redirect to index page if Ad $id doesn't exist
        header("Location:/message/".urlencode("Requested ad doesn't exist !"));
        exit;
    }
    // check cryptedMail
    if (!Validation::checkMail($ad->user_email, $cryptedMail)){
        // redirect to index page if User don't own Ad
        header("Location:/message/".urlencode("You're not allowed to modify this ad !"));
        exit;
    }
    // check User data
    $_POST["email"] = $ad->user_email;
    if ( ($data = Validation::userData()) !== true){
        header("Location: /edit/message/".$data."/".$id."/".$cryptedMail);
        exit;
    }
    // update User
    $user = new User([ "email"=>$ad->user_email , "lastName"=>$_POST["lastName"] , "firstName"=>$_POST["firstName"] , "phone"=>$_POST["phone"] ]);
    if (!UserManager::insert($user)){
        header("Location: /message/".urlencode("User couldn't be updated !"));
        exit;
    }
    // check Ad data
    if ( ($data = Validation::adData()) !== true){
        header("Location: /edit/message/".$data."/".$id."/".$cryptedMail);
        exit;
    }
    // update $ad object
    $ad->category_id = $_POST["category_id"];
    $ad->title = $_POST["title"];
    $ad->description = $_POST["description"];
    // handle picture file if posted
    if(isset($_FILES["picture"]) && !empty($_FILES["picture"]["name"])){
        $file = new File($_FILES["picture"]);
        if (!$file->check()){
            header("Location: /edit/message/picture/".$id."/".$cryptedMail);
            exit;
        }
        // delete previous picture
        try{
            File::delete($ad->picture);
        } catch (\Exception $e) {
            header("Location: /edit/message/picture/".$id."/".$cryptedMail);
            exit;
        }
        // update new Ad picture with id in picture name
        $picture = $ad->id."-".$file->name;
        $ad->picture = $picture;
        if (!move_uploaded_file($file->tmpName, dirname(__FILE__)."/assets/pictures/".$picture)){
            header("Location: /edit/message/picture/".$id."/".$cryptedMail);
            exit;
        }
    }
    // update Ad
    if (!($ad = AdManager::update($ad))){
        header("Location:/message/".urlencode("Your ad cannot be modified !"));
        exit;
    }
    // send validation mail
    if (Mail::sendValidate($ad, SERVER_URI)===0){
        // redirect to index page with error message
        header("Location:/message/".urlencode("Email could not be sent to ".$ad->user_email));
        exit;
    }
    // redirect to index page with confirmation message
    header("Location:/message/".urlencode("Your ad has been modified, please check your email to validate it !"));
    exit;
});

// validate route
$router->map('GET','/validate/[i:id]/[**:cryptedMail]',function($id, $cryptedMail){
    // check Ad $id
    if ( ($ad = Validation::ad($id)) === false ){
        // redirect to index page if Ad $id doesn't exist
        header("Location:/message/".urlencode("Requested ad doesn't exist !"));
        exit;
    }
    // check cryptedMail
    if (!Validation::checkMail($ad->user_email, $cryptedMail)){
        // redirect to index page if User don't own Ad
        header("Location:/message/".urlencode("You're not allowed to modify this ad !"));
        exit;
    }
    if (AdManager::isValidated($id)){
        // redirect to details page if Ad is already validated
        header("Location:/details/".$ad->id."/".urlencode("Your ad is already validated !"));
        exit;
    }
    // send delete mail
    if (Mail::sendDelete($ad, SERVER_URI)===0){
        // redirect to index page with error message
        header("Location:/message/".urlencode("Email could not be sent to".$ad->user_email));
        exit;
    }
    if (!AdManager::validate($id)){
        // redirect to index page with error message
        header("Location:/message/".urlencode("Your ad ".$ad->title." has not been validated !"));
        exit;
    }
    // redirect to details page with confirmation message
    header("Location:/details/".$id."/".urlencode("Your ad ".$ad->title." has been validated !")); 
    exit;               
});

// delete page route
$router->map('GET','/delete/[i:id]/[**:cryptedMail]',function($id, $cryptedMail){
    // check Ad $id
    if ( ($ad = Validation::ad($id)) === false ){
        // redirect to index page if Ad $id doesn't exist
        header("Location:/message/".urlencode("Requested ad doesn't exist !"));
        exit;
    }
    // check cryptedMail
    if (!Validation::checkMail($ad->user_email, $cryptedMail)){
        // redirect to index page if User don't own Ad
        header("Location:/message/".urlencode("You're not allowed to modify this ad !"));
        exit;
    }
    // echo delete page
    echo Twig::getRender('delete/delete.html.twig', [ "ad"=>$ad, "SERVER_URI"=>SERVER_URI, "cryptedMail"=>$cryptedMail ]);
});

// confirm delete Ad route
$router->map('GET','/confirmDelete/[i:id]/[**:cryptedMail]',function($id, $cryptedMail){
    // check Ad $id
    if ( ($ad = Validation::ad($id)) === false ){
        // redirect to index page if Ad $id doesn't exist
        header("Location:/message/".urlencode("Requested ad doesn't exist !"));
        exit;
    }
    // check cryptedMail
    if (!Validation::checkMail($ad->user_email, $cryptedMail)){
        // redirect to index page if User don't own Ad
        header("Location:/message/".urlencode("You're not allowed to modify this ad !"));
        exit;
    }
    // delete Ad
    if (AdManager::delete($id)){
        // redirect to index page with confirmation message
        header("Location:/message/".urlencode("Your ad has been deleted !"));
        exit;
    } else {
        // redirect to details page with error message
        header("Location:/details/".$id."/".urlencode("Your ad has not been deleted !"));
        exit;
    }
});

// match url
$match = $router->match();

// process request
if( is_array($match) && is_callable( $match['target'] ) ) {
	call_user_func_array( $match['target'], $match['params'] ); 
} else {
	// no route was matched
    header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    exit;
}