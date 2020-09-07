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
    // echo index page passing all validated Ad, all Category and SERVER_URI
    $ads = AdManager::getAllValidated();
    $categories = CategoryManager::getAll();
    echo Twig::getRender("index.html.twig", [ "ads"=>$ads , "categories"=>$categories , "SERVER_URI"=>SERVER_URI ]);
});

// index page route with message
$router->map('GET','/message/[:message]',function($message){
    // echo index page passing all validated Ad, all Category and SERVER_URI
    $ads = AdManager::getAllValidated();
    $categories = CategoryManager::getAll();
    echo Twig::getRender("index.html.twig", [ "ads"=>$ads , "categories"=>$categories , "SERVER_URI"=>SERVER_URI , "message"=>urldecode($message) ]);
});

// add Ad page route with message
$router->map('GET','/add/[:messageType]',function($messageType, $message){
    // echo add page passing all Category and SERVER_URI
    $categories = CategoryManager::getAll();
    echo Twig::getRender("add/add_form.html.twig", [ "categories"=>$categories , "SERVER_URI"=>SERVER_URI, "messageType"=>urldecode($messageType) , "message"=>urldecode($message) ]);
});

// add Ad form handling route
$router->map('POST','/addform',function(){
    // check User data
    if (!isset($_POST["email"]) || !Validate::email($_POST["email"])){
        header("Location: /add/emailError");
    }
    if (!isset($_POST["lastName"]) || !Validate::name($_POST["lastName"])) {
        header("Location: /add/lastNameError");
    }
    if (!isset($_POST["firstName"]) || !Validate::name($_POST["firstName"])) {
        header("Location: /add/firstNameError");
    }
    if (!isset($_POST["phone"]) || !Validate::phone($_POST["phone"])) {
        header("Location: /add/phoneError");
    }
    // insert User
    $user = new User([ "email"=>$_POST["email"] , "lastName"=>$_POST["lastName"] , "firstName"=>$_POST["firstName"] , "phone"=>$_POST["phone"] ]);
    if (!UserManager::insert($user)){
        header("Location: /message/".urlencode("User couldn't be added !"));
    }
    // check Ad data
    if (!isset($_POST["category_id"]) || !Validate::category($_POST["category_id"])){
        if (AdManager::user_emailExists($_POST["email"])===false){
            UserManager::delete($_POST["email"]);
        }
        header("Location: /add/categoryError");
    }
    if (!isset($_POST["title"]) || !Validate::text($_POST["title"])) {
        if (AdManager::user_emailExists($_POST["email"])===false){
            UserManager::delete($_POST["email"]);
        }
        header("Location: /add/titleError");
    }
    if (!isset($_POST["description"]) || !Validate::text($_POST["description"])) {
        if (AdManager::user_emailExists($_POST["email"])===false){
            UserManager::delete($_POST["email"]);
        }
        header("Location: /add/descriptionError");
    }
    // insert Ad
    $ad = new Ad([ "user_email"=>$_POST["email"] , "category_id"=>$_POST["category_id"] , "title"=>$_POST["title"] , "description"=>$_POST["description"]]);
    if (!$ad = AdManager::insert($ad)){
        if (AdManager::user_emailExists($_POST["email"])===false){
            UserManager::delete($_POST["email"]);
        }
        header("Location: /message/".urlencode("Ad couldn't be added !"));
    }
    // handle picture file if posted
    if(isset($_FILES["picture"]) && !empty($_FILES["picture"]["name"])){
        $file = new File($_FILES["picture"]);
        if (!$file->check()){
            AdManager::delete($ad->id);
            header("Location: /add/pictureError");
        }
        // update new Ad picture with id in picture name
        $picture = $ad->id."-".$file->name;
        $ad->picture = $picture;
        if (!$ad = AdManager::update($ad) || !move_uploaded_file($file->tmpName, dirname(__FILE__)."/assets/pictures/".$picture)){
            AdManager::delete($ad->id);
            header("Location: /add/pictureError");
        }
    }
    // send validation mail
    if (Mail::sendValidate($ad, SERVER_URI)===0){
        AdManager::delete($ad->id);
        // redirect to add page with email error message
        header("Location:/add/emailError");
    }
    // redirect to index page with confirmation message
    header("Location:/message/".urlencode("Your ad has been submitted, please check your email to validate it !"));
});

// edit Ad page route
$router->map('GET','/edit/[i:id]/[**:cryptedMail]',function($id, $cryptedMail){
    // check Ad $id
    if (Validation::ad($id)){
        $ad = AdManager::get($id);
        // check cryptedMail
        if (Validation::checkMail($ad->user_mail, $cryptedMail)){
            // echo edit page passing Ad matching $id, all Category, SERVER_URI and cryptedMail
            $categories = CategoryManager::getAll();
            echo Twig::getRender("edit/edit_form.html.twig", [ "ad"=>$ad , "categories"=>$categories , "SERVER_URI"=>SERVER_URI , "cryptedMail"=>$cryptedMail ]);
        } else {
            // redirect to index page if User don't own Ad
            header("Location:/message/".urlencode("You're not allowed to modify this ad !"));
        }
    } else {
        // redirect to index page if Ad $id doesn't exist
        header("Location:/message/".urlencode("Requested ad doesn't exist !"));
    }
});

// edit Ad page route with message
$router->map('GET','/edit/message/[:messageType]/[i:id]/[**:cryptedMail]',function($id, $messageType, $cryptedMail){
    // check Ad $id
    if (Validation::ad($id)){
        $ad = AdManager::get($id);
        // check cryptedMail
        if (Validation::checkMail($ad->user_mail, $cryptedMail)){
            // echo edit page passing Ad matching $id, all Category, SERVER_URI and cryptedMail
            $categories = CategoryManager::getAll();
            echo Twig::getRender("edit/edit_form.html.twig", [ "ad"=>$ad , "categories"=>$categories , "SERVER_URI"=>SERVER_URI , "cryptedMail"=>$cryptedMail , "messageType"=>urldecode($messageType) ]);
        } else {
            // redirect to index page if User don't own Ad
            header("Location:/message/".urlencode("You're not allowed to modify this ad !"));
        }
    } else {
        // redirect to index page if Ad $id doesn't exist
        header("Location:/message/".urlencode("Requested ad doesn't exist !"));
    }
});

// edit Ad form handling route
$router->map('POST','/editform/[i:id]/[**:cryptedMail]',function($id, $cryptedMail){
    // check Ad $id
    if (!Validation::ad($id)){
        // redirect to index page if Ad $id doesn't exist
        header("Location:/message/".urlencode("Requested ad doesn't exist !"));
    }
    $ad = AdManager::get($id);
    // check cryptedMail
    if (!Validation::checkMail($ad->user_mail, $cryptedMail)){
        // redirect to index page if User don't own Ad
        header("Location:/message/".urlencode("You're not allowed to modify this ad !"));
    }
    // check Ad data and update object
    if (!isset($_POST["category_id"]) || !Validate::category($_POST["category_id"])){
        header("Location: /edit/message/categoryError/".$id."/".$cryptedMail);
    }
    $ad->category_id = $_POST["category_id"];
    if (!isset($_POST["title"]) || !Validate::text($_POST["title"])) {
        header("Location: /edit/message/titleError/".$id."/".$cryptedMail);
    }
    $ad->title = $_POST["title"];
    if (!isset($_POST["description"]) || !Validate::text($_POST["description"])) {
        header("Location: /edit/message/descriptionError/".$id."/".$cryptedMail);
    }
    $ad->description = $_POST["description"];
    // handle picture file if posted
    if(isset($_FILES["picture"]) && !empty($_FILES["picture"]["name"])){
        $file = new File($_FILES["picture"]);
        if (!$file->check()){
            header("Location: /edit/message/pictureError/".$id."/".$cryptedMail);
        }
        // update new Ad picture with id in picture name
        $picture = $ad->id."-".$file->name;
        $ad->picture = $picture;
        if (!move_uploaded_file($file->tmpName, dirname(__FILE__)."/assets/pictures/".$picture)){
            header("Location: /edit/message/pictureError/".$id."/".$cryptedMail);
        }
    }
    // check User data
    if (!isset($_POST["email"]) || !Validate::email($_POST["email"])){
        header("Location: /edit/message/emailError/".$id."/".$cryptedMail);
    }
    if (!isset($_POST["lastName"]) || !Validate::name($_POST["lastName"])) {
        header("Location: /edit/message/lastNameError/".$id."/".$cryptedMail);
    }
    if (!isset($_POST["firstName"]) || !Validate::name($_POST["firstName"])) {
        header("Location: /edit/message/firstNameError/".$id."/".$cryptedMail);
    }
    if (!isset($_POST["phone"]) || !Validate::phone($_POST["phone"])) {
        header("Location: /edit/message/phoneError/".$id."/".$cryptedMail);
    }
    // update User
    $user = new User([ "email"=>$ad->user_email , "lastName"=>$_POST["lastName"] , "firstName"=>$_POST["firstName"] , "phone"=>$_POST["phone"] ]);
    if (!UserManager::insert($user)){
        header("Location: /message/".urlencode("User couldn't be added !"));
    }
    // send validation mail
    if (Mail::sendValidate($ad, SERVER_URI)===0){
        // redirect to index page with error message
        header("Location:/message/".urlencode("Email could not be sent to ".$ad->user_email));
    }
    // update Ad
    $ad = AdManager::update($ad);
    // redirect to index page with confirmation message
    header("Location:/message/".urlencode("Your ad has been modified, please check your email to validate it !"));
});

// validate Ad route
$router->map('GET','/validate/[i:id]/[**:cryptedMail]',function($id, $cryptedMail){
    // check Ad $id
    if (Validation::ad($id)){
        $ad = AdManager::get($id);
        // check cryptedMail
        if (Validation::checkMail($ad->user_mail, $cryptedMail)){
            if (! AdManager::isValidated($id)){
                // send delete mail
                if (Mail::sendDelete($ad, SERVER_URI)!==0){
                    if (AdManager::validate($ad->id)){
                        // redirect to details page with confirmation message
                        header("Location:/details/".$ad->id."/".urlencode("Your ad ".$ad->title." has been validated !"));
                    } else {
                        // redirect to index page with error message
                        header("Location:/message/".urlencode("Your ad ".$ad->title." has not been validated !"));
                    }
                } else {
                    // redirect to index page with error message
                    header("Location:/message/".urlencode("Email could not be sent to".$ad->user_email));
                }
            } else {
                // redirect to details page if Ad is already validated
                header("Location:/details/".$ad->id."/".urlencode("Your ad is already validated !"));
            }
        } else {
            // redirect to index page if User don't own Ad
            header("Location:/message/".urlencode("You're not allowed to modify this ad !"));
        }
    } else {
        // redirect to index page if Ad $id doesn't exist
        header("Location:/message/".urlencode("Requested ad doesn't exist !"));
    }
});

// Ad details page route
$router->map('GET','/details/[i:id]/[:message]',function($id, $message){
    // check Ad $id
    if (Validation::ad($id)){
        // allow details view only for validated Ad
        if (AdManager::isValidated($id)){
            // echo details page of Ad passing Ad matching $id and SERVER_URI
            $ad = AdManager::get($id);
            echo Twig::getRender("details/details.html.twig", [ "ad"=>$ad , "SERVER_URI"=>SERVER_URI , "message"=>urldecode($message) ]);
        }else{
            // redirect to index page if Ad is not validated
            header("Location:/message/".urlencode("This ad is not yet validated !"));
        }
    } else {
        // redirect to index page if Ad $id doesn't exist
        header("Location:/message/".urlencode("Requested ad doesn't exist !"));
    }
    // UNVALIDATE AD DANS CERTAINS CAS
});

// delete Ad page route
$router->map('GET','/delete/[i:id]/[**:cryptedMail]',function($id, $cryptedMail){
    // check Ad $id
    if (Validation::ad($id)){
        $ad = AdManager::get($id);
        // check cryptedMail
        if (Validation::checkMail($ad->user_mail, $cryptedMail)){
            // echo delete page passing Ad, SERVER_URI and cryptedMail
            echo Twig::getRender('delete/delete.html.twig', [ "ad"=>$ad, "SERVER_URI"=>SERVER_URI, "cryptedMail"=>$cryptedMail ]);
        } else {
            // redirect to index page if User don't own Ad
            header("Location:/message/".urlencode("You're not allowed to modify this ad !"));
        }
    } else {
        // redirect to index page if Ad $id doesn't exist
        header("Location:/message/".urlencode("Requested ad doesn't exist !"));
    }
});

// confirm delete Ad route
$router->map('GET','/confirmDelete/[i:id]/[**:cryptedMail]',function($id, $cryptedMail){
    // check Ad $id
    if (Validation::ad($id)){
        $ad = AdManager::get($id);
        // check cryptedMail
        if (Validation::checkMail($ad->user_mail, $cryptedMail)){
            $title = $ad->title;
            if (AdManager::delete($id)){
                // redirect to index page with confirmation message
                header("Location:/message/".urlencode("Your ad ".$title." has been deleted !"));
            } else {
                // redirect to details page with error message
                header("Location:/details/".$id."/".urlencode("Your ad has not been deleted !"));
            }
        } else {
            // redirect to index page if User don't own Ad
            header("Location:/message/".urlencode("You're not allowed to modify this ad !"));
        }
    } else {
        // redirect to index page if Ad $id doesn't exist
        header("Location:/message/".urlencode("Requested ad doesn't exist !"));
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
}