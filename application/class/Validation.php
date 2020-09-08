<?php
namespace Ads;

use \Ads\Crypt as Crypt;
use \Respect\Validation\Validator as v;
use \Ads\Mail as Mail;
use \Ads\Manager\CategoryManager as CategoryManager;
use \Ads\Manager\AdManager as AdManager;

abstract class Validation
{
    /**
     * @param string $text value to validate
     * text is valid if it contains non blank
     * @return boolean text is valid or not
     */
    public static function text(string $text){
        return v::stringVal()->notEmpty()->validate($text);
    }

    /**
     * @param string $name value to validate
     * name is valid if it contains only alphabetic, - , whitespace characters and at least one letter
     * @return boolean name is valid or not
     */
    public static function name(string $name){
        return v::alpha('-',' ')->containsAny(['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'])->validate($name);
    }

    /**
     * @param string $email value to validate
     * @return boolean email is valid or not
     */
    public static function email(string $email){
        return v::email()->validate($email);
    }

    /**
     * @param string $phone value to validate
     * @return boolean phone is valid or not
     */
    public static function phone(string $phone){
        return v::phone()->validate($phone);
    }

    /**
     * @param integer|string $id Ad id to validate
     * validate if $id is an int value and exists as id in ad database
     * @return Ad|boolean Ad if $id match in database | false if not
     */
    public static function ad($id){
        if (v::intVal()->validate($id)){
            $id = intval($id);
            return AdManager::get($id);
        }
    }

    /**
     * @param integer|string $id category_id to validate
     * validate if $id is an int value and exists as id in category database
     * @return boolean category_id is valid or not
     */
    public static function category($id){
        if (v::intVal()->validate($id)){
            $id = intval($id);
            return boolval(CategoryManager::get($id));
        }
    }

    /**
	 * @param string $mail user mail
	 * @param string $cryptedMail crypted mail to check
	 * @return boolean user own Ad or not
	 */
    public static function checkMail(string $mail, string $cryptedMail){
        try{
            $crypt = new Crypt();
            if ($crypt->decrypt($cryptedMail, Mail::SECRET_KEY, Mail::SIGN_KEY) === $mail){
                return true;
            }
        } catch (\Exception $e){
            return false;
        }
    }

    /**
     * check if $post contains valid email, lastName, firstName, phone entries
	 * @return boolean|string true if ok | incorrect entry name if it exists
	 */
    public static function userData(){
        if ( !isset($_POST["email"]) || !self::email($_POST["email"]) ){
            return "email";
        }
        if (!isset($_POST["lastName"]) || !self::name($_POST["lastName"])) {
            return "lastName";
        }
        if (!isset($_POST["firstName"]) || !self::name($_POST["firstName"])) {
            return "firstName";
        }
        if (!isset($_POST["phone"]) || !self::phone($_POST["phone"])) {
            return "phone";
        }
        return true;
    }

    /**
     * check if $post contains valid category_id, title, description entries
	 * @return boolean|string true if ok | incorrect entry name if it exists
	 */
    public static function adData(){
        if (!isset($_POST["category_id"]) || !self::category($_POST["category_id"])){
            return "category";
        }
        if (!isset($_POST["title"]) || !self::text($_POST["title"])) {
            return "title";
        }
        if (!isset($_POST["description"]) || !self::text($_POST["description"])) {
            return "description";
        }
        return true;
    }
}