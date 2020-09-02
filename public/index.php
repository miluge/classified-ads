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
use \Ads\Manager\AdManager as AdManager;
use \Ads\Manager\CategoryManager as CategoryManager;
use \Ads\Manager\UserManager as UserManager;
use \Ads\Twig as Twig;

// mjml render function
use \Qferrer\Mjml\Renderer\BinaryRenderer as BinaryRenderer;
function mjmlRender($mjml){
    $renderer = new BinaryRenderer(dirname(dirname(__FILE__)).'/node_modules/.bin/mjml');
    return $renderer->render($mjml);
}

// ALTOROUTER
$router = new AltoRouter();
$router->setBasePath(BASE_PATH);

// index page route
$router->map('GET','/',function(){
    // echo index page passing all validated Ad, all Category and SERVER_URI
    $ads = AdManager::getAllValidated();
    $categories = CategoryManager::getAll();
    Twig::echoRender("index.html.twig", [ "ads"=>$ads , "categories"=>$categories , "SERVER_URI"=>SERVER_URI ]);
});

// add Ad page route
$router->map('GET','/add',function(){
    // echo add page passing all Category and SERVER_URI
    $categories = CategoryManager::getAll();
    Twig::echoRender("add/add_form.html.twig", [ "categories"=>$categories , "SERVER_URI"=>SERVER_URI ]);
});

// add Ad error page route
$router->map('GET','/add/error/[a:errorType]/[a:errorMessage]',function($errorType, $errorMessage){
    // echo add page passing all Category, SERVER_URI and error type=>message
    $categories = CategoryManager::getAll();
    Twig::echoRender("add/add_form.html.twig", [ "categories"=>$categories , "SERVER_URI"=>SERVER_URI, "error"=>[$errorType=>$errorMessage] ]);
});

// edit Ad page route
$router->map('GET','/edit/[i:id]',function($id){
    // echo edit page passing Ad matching $id, all Category and SERVER_URI
    $ad = AdManager::get($id);
    $categories = CategoryManager::getAll();
    Twig::echoRender("edit/edit_form.html.twig", [ "ad"=>$ad , "categories"=>$categories , "SERVER_URI"=>SERVER_URI ]);
});

// edit Ad error page route
$router->map('GET','/edit/[i:id]/error/[a:errorType]/[a:errorMessage]',function($id, $errorType, $errorMessage){
    // echo edit page passing all Category SERVER_URI and error type=>message
    $ad = AdManager::get($id);
    $categories = CategoryManager::getAll();
    Twig::echoRender("edit/edit_form.html.twig", [ "ad"=>$ad , "categories"=>$categories , "SERVER_URI"=>SERVER_URI, "error"=>[$errorType=>$errorMessage] ]);
});

// Ad details page route
$router->map('GET','/details/[i:id]',function($id){
    // allow details view only for validated Ad
    if (AdManager::isValidated($id)){
        // echo details page of Ad passing Ad matching $id and SERVER_URI
        $ad = AdManager::get($id);
        Twig::echoRender("details/details.html.twig", [ "ad"=>$ad , "SERVER_URI"=>SERVER_URI ]);
    }else{
        //redirect to index page if Ad is not validated
        header("Location:/");
    }
});

// add Ad form handling route
$router->map('POST','/addform',function(){
    // insert User
    $user = new User([ "email"=>$_POST["email"] , "lastName"=>$_POST["lastName"] , "firstName"=>$_POST["firstName"] , "phone"=>$_POST["phone"] ]);
    UserManager::insert($user);
    // insert Ad
    $ad = new Ad([ "user_email"=>$_POST["email"] , "category_id"=>$_POST["category_id"] , "title"=>$_POST["title"] , "description"=>$_POST["description"]]);
    $ad = AdManager::insert($ad);
    // handle picture file if posted
    if(isset($_FILES["picture"])){
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
            header("Location:/add/error/picture/".$fileCheck);
        }
    }
    // send validation mail
    $newAd = AdManager::get($newId);
    $message = new \Swift_Message();
    $message->setSubject('Please review your ad '.$newAd->title.'!');
    $message->setFrom(['perbet.dev@gmail.com' => 'Classified Ads']);
    $message->setTo([$newAd->user_email]);
    // set body template
    $twig = loadTwig();
    $template = $twig->load('mail/validate.mjml.twig');
    $mjml = $template->render([ "ad"=>$newAd, "SERVER_URI"=>SERVER_URI ]);
    $html = mjmlRender($mjml);
    $message->setBody($html, 'text/html');
    // set connection parameters
    $transport = new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
    $transport->setUsername(apache_getenv("GMAIL_USER"));
    $transport->setPassword(apache_getenv("GMAIL_PASSWORD"));
    $mailer = new \Swift_Mailer($transport);
    $mailer->send($message);
    // ADD CONFIRMATION MESSAGE
    // redirect to index page
    header("Location:/");
});

// edit Ad form handling route
$router->map('POST','/editform/[i:id]',function($id){
    // initialize Ad
    $ad = new Ad([ "id"=> $id , "category_id"=>$_POST["category_id"] , "title"=>$_POST["title"] , "description"=>$_POST["description"]]);
   // handle picture file if posted
   if(isset($_FILES["picture"])){
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
            header("Location:/edit/[i:id]/error/picture/".$fileCheck);
        }
    }
    // update Ad
    $ad = AdManager::update($ad);
    // update User
    $user = new User([ "email"=>$ad->user_email , "lastName"=>$_POST["lastName"] , "firstName"=>$_POST["firstName"] , "phone"=>$_POST["phone"] ]);
    UserManager::insert($user);
    // send validation mail
    $newAd = AdManager::get($id);
    $message = new \Swift_Message();
    $message->setSubject('Please review your ad '.$newAd->title.'!');
    $message->setFrom(['perbet.dev@gmail.com' => 'Classified Ads']);
    $message->setTo([$newAd->user_email]);
    // set body template
    $twig = loadTwig();
    $template = $twig->load('mail/validate.mjml.twig');
    $mjml = $template->render([ "ad"=>$newAd, "SERVER_URI"=>SERVER_URI ]);
    $html = mjmlRender($mjml);
    $message->setBody($html, 'text/html');
    // set connection parameters
    $transport = new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
    $transport->setUsername(apache_getenv("GMAIL_USER"));
    $transport->setPassword(apache_getenv("GMAIL_PASSWORD"));
    $mailer = new \Swift_Mailer($transport);
    $mailer->send($message);
    // ADD CONFIRMATION MESSAGE
    // redirect to index page
    header("Location:/");
});

// validate Ad route
$router->map('GET','/validate/[i:id]',function($id){
    // check if Ad is validated
    if (! AdManager::isValidated($id)){
        AdManager::validate($id);
        // send delete mail
        $ad = AdManager::get($id);
        $message = new \Swift_Message();
        $message->setSubject('Your ad '.$ad->title.' has been validated !');
        $message->setFrom(['perbet.dev@gmail.com' => 'Classified Ads']);
        $message->setTo([$ad->user_email]);
        // set body template
        $twig = loadTwig();
        $template = $twig->load('mail/delete.mjml.twig');
        $mjml = $template->render([ "ad"=>$ad, "SERVER_URI"=>SERVER_URI ]);
        $html = mjmlRender($mjml);
        $message->setBody($html, 'text/html');
        // set connection parameters
        $transport = new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
        $transport->setUsername(apache_getenv("GMAIL_USER"));
        $transport->setPassword(apache_getenv("GMAIL_PASSWORD"));
        $mailer = new \Swift_Mailer($transport);
        $mailer->send($message);
        // redirect to details page
        header("Location:/details/".$ad->id);
    }
    // ADD ALREADY VALIDATED MESSAGE
    // redirect to index page
    header("Location:/");
});

// delete Ad route
$router->map('GET','/delete/[i:id]',function($id){
    AdManager::delete($id);
    // ADD DELETE CONFIRMATION MESSAGE
    // redirect to index page
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