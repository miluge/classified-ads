<?php
namespace Ads;
use \Ads\Ad as Ad;
use \Ads\Twig as Twig;
use \Ads\MJML as MJML;

abstract class Mail
{
    /**
     * @param Ad $ad Ad object to fill mail
     */
    public static function sendValidate($ad){
        $message = new \Swift_Message();
        $message->setSubject('Please review your ad '.$ad->title.'!');
        $message->setFrom(['perbet.dev@gmail.com' => 'Classified Ads']);
        $message->setTo([$ad->user_email]);
        // set body template
        $mjml = Twig::getRender('mail/validate.mjml.twig', ["ad"=>$ad]);
        $html = MJML::getRender($mjml);
        $message->setBody($html, 'text/html');
        // set connection parameters
        $transport = new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
        $transport->setUsername(apache_getenv("GMAIL_USER"));
        $transport->setPassword(apache_getenv("GMAIL_PASSWORD"));
        $mailer = new \Swift_Mailer($transport);
        $mailer->send($message);
    }

    /**
     * @param Ad $ad Ad object to fill mail
     */
    public static function sendDelete($ad){
        $message = new \Swift_Message();
        $message->setSubject('Your ad '.$ad->title.' has been validated !');
        $message->setFrom(['perbet.dev@gmail.com' => 'Classified Ads']);
        $message->setTo([$ad->user_email]);
        // set body template
        $mjml = Twig::getRender('mail/delete.mjml.twig', ["ad"=>$ad]);
        $html = MJML::getRender($mjml);
        $message->setBody($html, 'text/html');
        // set connection parameters
        $transport = new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
        $transport->setUsername(apache_getenv("GMAIL_USER"));
        $transport->setPassword(apache_getenv("GMAIL_PASSWORD"));
        $mailer = new \Swift_Mailer($transport);
        $mailer->send($message);
    }
}