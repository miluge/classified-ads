<?php
namespace Ads;

use \Ads\Ad as Ad;
use \Ads\Twig as Twig;
use \Ads\MJML as MJML;
use \Ads\Crypt as Crypt;

abstract class Mail
{
    /**
     * @param Ad $ad Ad object to fill mail
     * @param string $server_uri base path to load Twig render
     * @return integer number of successfull delivery
     */
    public static function sendValidate(Ad $ad, string $server_uri){
        $message = new \Swift_Message();
        $message->setSubject('Please review your ad '.$ad->title.'!');
        $message->setFrom([EMAIL => 'Droopist Team']);
        $message->setTo([$ad->user_email => $ad->user_firstName." ".$ad->user_lastName]);
        // crypt user_email
        $cryptedMail = Crypt::encrypt($ad->user_email);
        // set body template
        $mjml = Twig::getRender('mail/validate.mjml.twig', ["ad"=>$ad, "SERVER_URI"=>$server_uri, "cryptedMail"=> $cryptedMail ]);
        $html = MJML::getRender($mjml);
        $message->setBody($html, 'text/html');
        // set connection parameters
        $transport = new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
        $transport->setUsername(EMAIL);
        $transport->setPassword(PASS);
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
        $message->setFrom([EMAIL => 'Droopist Team']);
        $message->setTo([$ad->user_email => $ad->user_firstName." ".$ad->user_lastName]);
        // crypt user_email
        $cryptedMail = Crypt::encrypt($ad->user_email);
        // set body template
        $mjml = Twig::getRender('mail/delete.mjml.twig', ["ad"=>$ad, "SERVER_URI"=>$server_uri, "cryptedMail"=> $cryptedMail ]);
        $html = MJML::getRender($mjml);
        $message->setBody($html, 'text/html');
        // set connection parameters
        $transport = new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
        $transport->setUsername(EMAIL);
        $transport->setPassword(PASS);
        $mailer = new \Swift_Mailer($transport);
        return $mailer->send($message);
    }
}