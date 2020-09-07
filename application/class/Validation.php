<?php
namespace Ads;

use \Ads\Crypt as Crypt;
use \Respect\Validation\Validator as v;
use \Ads\Manager\CategoryManager as CategoryManager;
use \Ads\Manager\AdManager as AdManager;

abstract class Validation extends v
{
    
    const SECRET_KEY = 'Ld:LUSweidn,UsAjOOkjpSkjiPmmEWKJ';
    const SIGN_KEY = 'By Ads lkjayfjkeqr9c87mza,na,ndde';

    /**
     * @param string $text value to validate
     * text is valid if it contains non blank
     * @return boolean text is valid or not
     */
    public static function text(string $text): boolean{
        return self::stringVal()->notEmpty()->validate($text);
    }

    /**
     * @param string $name value to validate
     * name is valid if it contains only alphabetic, - , whitespace characters and at least one letter
     * @return boolean name is valid or not
     */
    public static function name(string $name): boolean{
        return self::alpha('-',' ')->containsAny(['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'])->validate($name);
    }

    /**
     * @param string $email value to validate
     * @return boolean email is valid or not
     */
    public static function email(string $email): boolean{
        return self::email()->validate($email);
    }

    /**
     * @param string $phone value to validate
     * @return boolean phone is valid or not
     */
    public static function phone(string $phone): boolean{
        return self::phone()->validate($phone);
    }

    /**
     * @param integer|string $id Ad id to validate
     * validate if $id is an int value and exists as id in ad database
     * @return boolean Ad id is valid or not
     */
    public static function ad($id): boolean{
        if (v::intVal()->validate($id)){
            $id = intval($id);
            return boolval(AdManager::get($id));
        }
    }

    /**
     * @param integer|string $id category_id to validate
     * validate if $id is an int value and exists as id in category database
     * @return boolean category_id is valid or not
     */
    public static function category($id): boolean{
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
    public static function checkMail(string $mail, string $cryptedMail): boolean{
        try{
            $crypt = new Crypt();
            if ($crypt->decrypt($cryptedMail, self::SECRET_KEY, self::SIGN_KEY) === $mail){
                return true;
            }
        } catch (\Exception $e){
            return false;
        }
    }
}