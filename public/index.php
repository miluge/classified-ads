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

// add Ad page route
$router->map('GET','/add',function(){
    // echo add page passing all Category and SERVER_URI
    $categories = CategoryManager::getAll();
    echo Twig::getRender("add/add_form.html.twig", [ "categories"=>$categories , "SERVER_URI"=>SERVER_URI ]);
});

// add Ad error page route
$router->map('GET','/add/error/[a:errorType]/[:errorMessage]',function($errorType, $errorMessage){
    // echo add page passing all Category, SERVER_URI and error type=>message
    $categories = CategoryManager::getAll();
    echo Twig::getRender("add/add_form.html.twig", [ "categories"=>$categories , "SERVER_URI"=>SERVER_URI, "error"=>[$errorType=>urldecode($errorMessage)] ]);
});

// edit Ad page route
$router->map('GET','/edit/[i:id]/mail/[:cryptedMail]',function($id, $cryptedMail){
    // check if User own Ad
    $crypt = new Crypt();
    if ($crypt->checkOwner($id, urldecode($cryptedMail))){
        // echo edit page passing Ad matching $id, all Category, SERVER_URI and cryptedMail
        $ad = AdManager::get($id);
        $categories = CategoryManager::getAll();
        echo Twig::getRender("edit/edit_form.html.twig", [ "ad"=>$ad , "categories"=>$categories , "SERVER_URI"=>SERVER_URI ,"cryptedMail"=>$cryptedMail ]);
    } else {
        // ADD MESSAGE 
        // redirect to index page if User don't own Ad
        header("Location:/");
    }
});

// edit Ad error page route
$router->map('GET','/edit/[i:id]/error/[a:errorType]/[:errorMessage]',function($id, $errorType, $errorMessage){
    // echo edit page passing all Category SERVER_URI and error type=>message
    $ad = AdManager::get($id);
    $categories = CategoryManager::getAll();
    echo Twig::getRender("edit/edit_form.html.twig", [ "ad"=>$ad , "categories"=>$categories , "SERVER_URI"=>SERVER_URI, "error"=>[$errorType=>urldecode($errorMessage)] ]);
});

// Ad details page route
$router->map('GET','/details/[i:id]',function($id){
    // allow details view only for validated Ad
    if (AdManager::isValidated($id)){
        // echo details page of Ad passing Ad matching $id and SERVER_URI
        $ad = AdManager::get($id);
        echo Twig::getRender("details/details.html.twig", [ "ad"=>$ad , "SERVER_URI"=>SERVER_URI ]);
    }else{
        // redirect to index page if Ad is not validated
        header("Location:/");
    }
});

// Ad details error page route
$router->map('GET','/details/[i:id]/error/[a:errorType]/[:errorMessage]',function($id, $errorType, $errorMessage){
    // allow details error view only for validated Ad
    if (AdManager::isValidated($id)){
        // unvalidate Ad
        $ad = AdManager::unValidate($id);
        // echo details page of Ad passing Ad matching $id, SERVER_URI and error type=>message
        echo Twig::getRender("details/details.html.twig", [ "ad"=>$ad , "SERVER_URI"=>SERVER_URI, "error"=>[$errorType=>urldecode($errorMessage)] ]);
    }else{
        //redirect to index page if Ad is not validated
        header("Location:/");
    }
});

// delete Ad page route
$router->map('GET','/delete/[i:id]/mail/[:cryptedMail]',function($id, $cryptedMail){
    // check if User own Ad
    $crypt = new Crypt();
    if ($crypt->checkOwner($id, urldecode($cryptedMail))){
        // echo delete page passing Ad, SERVER_URI and cryptedMail
        $ad = AdManager::get($id);
        echo Twig::getRender('delete/delete.html.twig', [ "ad"=>$ad, "SERVER_URI"=>SERVER_URI, "cryptedMail"=>$cryptedMail ]);
    } else {
        // ADD MESSAGE 
        // redirect to index page if User don't own Ad
        header("Location:/");
    }
});

// add Ad form handling route
$router->map('POST','/addform',function(){
    // insert User
    $user = new User([ "email"=>$_POST["email"] , "lastName"=>$_POST["lastName"] , "firstName"=>$_POST["firstName"] , "phone"=>$_POST["phone"] ]);
    UserManager::insert($user);
    var_dump($user);
    // insert Ad
    $ad = new Ad([ "user_email"=>$_POST["email"] , "category_id"=>$_POST["category_id"] , "title"=>$_POST["title"] , "description"=>$_POST["description"]]);
    $ad = AdManager::insert($ad);
    var_dump($ad);
    // handle picture file if posted
    if(isset($_FILES["picture"]) && !empty($_FILES["picture"]["name"])){
        echo "file";
        $file = new File($_FILES["picture"]);
        $fileCheck = $file->check();
        if ($fileCheck===true){
            // update new Ad picture with id in picture name
            $picture = $ad->id."-".$file->name;
            $ad->picture = $picture;
            $ad = AdManager::update($ad);
            // upload file
            move_uploaded_file($file->tmpName, dirname(__FILE__)."/assets/pictures/".$picture);
        }else{
            // remove Ad
            AdManager::delete($ad->id);
            // redirect to add page with picture error message
            header("Location:/add/error/picture/".urlencode($fileCheck));
        }
    }
    // send validation mail
    if (Mail::sendValidate($ad, SERVER_URI)===0){
        echo 'mail';
        $email = $ad->user_email;
        // remove Ad
        AdManager::delete($ad->id);
        // redirect to add page with email error message
        header("Location:/add/error/email/".urlencode("email could not be sent to".$email));
    }
    // ADD CONFIRMATION MESSAGE
    // redirect to index page
    // header("Location:/");
});

// edit Ad form handling route
$router->map('POST','/editform/[i:id]/mail/[:cryptedMail]',function($id, $cryptedMail){
    // check if User own Ad
    $crypt = new Crypt();
    if ($crypt->checkOwner($id, urldecode($cryptedMail))){
        // initialize Ad
        $ad = new Ad([ "id"=> $id , "category_id"=>$_POST["category_id"] , "title"=>$_POST["title"] , "description"=>$_POST["description"]]);
        // handle picture file if posted
        if(isset($_FILES["picture"]) && !empty($_FILES["picture"]["name"])){
            $file = new File($_FILES["picture"]);
            $fileCheck = $file->check();
            if ($fileCheck===true){
                // update Ad picture with id in picture name
                $picture = $ad->id."-".$file->name;
                $ad->picture = $picture;
                // upload file
                move_uploaded_file($file->tmpName, dirname(__FILE__)."/assets/pictures/".$picture);
            }else{
                // redirect to edit page with picture error message
                header("Location:/edit/".$ad->id."/error/picture/".urlencode($fileCheck));
            }
        }
        // update Ad
        $ad = AdManager::update($ad);
        // update User
        $user = new User([ "email"=>$ad->user_email , "lastName"=>$_POST["lastName"] , "firstName"=>$_POST["firstName"] , "phone"=>$_POST["phone"] ]);
        UserManager::insert($user);
        // send validation mail
        if (Mail::sendValidate($ad, SERVER_URI)===0){
            // redirect to edit page with email error message
            header("Location:/edit/".$ad->id."/error/email/".urlencode("email could not be sent to".$ad->user_email));
        }
        // ADD CONFIRMATION MESSAGE
        // redirect to index page
        header("Location:/");
    } else {
        // ADD MESSAGE 
        // redirect to index page if User don't own Ad
        header("Location:/");
    }
});

// validate Ad route
$router->map('GET','/validate/[i:id]/mail/[:cryptedMail]',function($id, $cryptedMail){
    // check if User own Ad
    $crypt = new Crypt();
    if ($crypt->checkOwner($id, urldecode($cryptedMail))){
        // check if Ad is validated
        if (! AdManager::isValidated($id)){
            $ad = AdManager::validate($id);
            // send delete mail
            if (Mail::sendDelete($ad, SERVER_URI)===0){
                // redirect to details page with email error message
                header("Location:/details/".$ad->id."/error/email/".urlencode("email could not be sent to".$ad->user_email));
            }
        }
        // redirect to Ad details page
        header("Location:/details/".$ad->id);
    } else {
        // ADD MESSAGE 
        // redirect to index page if User don't own Ad
        header("Location:/");
    }
});

// confirm delete Ad route
$router->map('GET','/confirmDelete/[i:id]/mail/[:cryptedMail]',function($id, $cryptedMail){
    // check if User own Ad
    $crypt = new Crypt();
    if ($crypt->checkOwner($id, urldecode($cryptedMail))){
        AdManager::delete($id);
        // ADD DELETE CONFIRMATION MESSAGE
        // redirect to index page
    header("Location:/");
    } else {
        // ADD MESSAGE 
        // redirect to index page if User don't own Ad
        header("Location:/");
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