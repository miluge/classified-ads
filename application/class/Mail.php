<?php
namespace Ads;
use \Ads\Ad as Ad;
use \Ads\Twig as Twig;
use \Ads\MJML as MJML;
use \Ads\Crypt as Crypt;

abstract class Mail
{
    const SECRET_KEY = 'Lkd:uweAdus9OAKjAvhskj1mNB1MKJhs';
    const SIGN_KEY = 'By Ads lkjajMlkTjkeq8uB7m,na,7ynd';

    /**
     * @param Ad $ad Ad object to fill mail
     * @param string $server_uri base path to load Twig render
     * @return integer number of successfull delivery
     */
    public static function sendValidate(Ad $ad, string $server_uri){
        $message = new \Swift_Message();
        $message->setSubject('Please review your ad '.$ad->title.'!');
        $message->setFrom(['perbet.dev@gmail.com' => 'Classified Ads']);
        $message->setTo([$ad->user_email => $ad->user_firstName." ".$ad->user_lastName]);
        // crypt user_email
        $crypt = new Crypt();
        $cryptedMail = $crypt->encrypt($ad->user_email, self::SECRET_KEY, self::SIGN_KEY);
        // set body template
        $mjml = Twig::getRender('mail/validate.mjml.twig', ["ad"=>$ad, "SERVER_URI"=>$server_uri, "cryptedMail"=> $cryptedMail ]);
        $html = MJML::getRender($mjml);
        $message->setBody($html, 'text/html');
        // set connection parameters
        $transport = new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
        $transport->setUsername(apache_getenv("GMAIL_USER"));
        $transport->setPassword(apache_getenv("GMAIL_PASSWORD"));
        $mailer = new \Swift_Mailer($transport);
        return $mailer->send($message);
    }

    /**
     * @param Ad $ad Ad object to fill mail
     * @param string $server_uri base path to load Twig render
     * @return integer number of successfull delivery
     */
    public static function sendDelete(Ad $ad, string $server_uri){
        $message = new \Swift_Message();
        $message->setSubject('Your ad '.$ad->title.' has been validated !');
        $message->setFrom(['perbet.dev@gmail.com' => 'Classified Ads']);
        $message->setTo([$ad->user_email => $ad->user_firstName." ".$ad->user_lastName]);
        // crypt user_email
        $crypt = new Crypt();
        $cryptedMail = $crypt->encrypt($ad->user_email, self::SECRET_KEY, self::SIGN_KEY);
        // set body template
        $mjml = Twig::getRender('mail/delete.mjml.twig', ["ad"=>$ad, "SERVER_URI"=>$server_uri, "cryptedMail"=> $cryptedMail ]);
        $html = MJML::getRender($mjml);
        $message->setBody($html, 'text/html');
        // set connection parameters
        $transport = new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
        $transport->setUsername(apache_getenv("GMAIL_USER"));
        $transport->setPassword(apache_getenv("GMAIL_PASSWORD"));
        $mailer = new \Swift_Mailer($transport);
        return $mailer->send($message);
    }
}