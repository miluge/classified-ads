<?php
namespace Ads;

abstract class MJML
{
    /**
     * @param string $mjml MJML formated string
     * @return string HTML formated string
     */
    function getRender($mjml){
        $renderer = new \Qferrer\Mjml\Renderer\BinaryRenderer(dirname(dirname(dirname(__FILE__))).'/node_modules/.bin/mjml');
        return $renderer->render($mjml);
    }
}