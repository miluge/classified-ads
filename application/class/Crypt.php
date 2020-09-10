<?php
namespace Ads;

abstract class Crypt
{
    const CIPHER = "aes-128-cbc";

    /**
     * @param string $text text to encrypt 
     * @return string urlencoded crypted text
     */
    public static function encrypt($text){
        $ivlen = openssl_cipher_iv_length(self::CIPHER);
        $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $iv = "";
        for ($i = 0; $i<$ivlen; $i++){
            $iv .= $characters[random_int(0,51)];
        }
        return urlencode($iv.openssl_encrypt($text,self::CIPHER,KEY,0,$iv));
    }

    /**
     * @param string $cipherText urlencoded crypted text
     * @return string decrypted text 
     */
    public static function decrypt($cipherText){
		$ivlen = openssl_cipher_iv_length(self::CIPHER);
        $cipherText = urldecode($cipherText);
        $iv = substr($cipherText,0,$ivlen);
        $cipherRaw = substr($cipherText,$ivlen);
        return openssl_decrypt($cipherRaw,self::CIPHER,KEY,0,$iv);
    }
}