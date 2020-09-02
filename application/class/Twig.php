<?php
namespace Ads;

abstract class Twig
{
    /**
     * @param string $template template to load in render
     * @param array $params parameters to pass to template
     * load Twig and echo $template render passing $params
     * @return string generated template render
     */
    public static function getRender($template, $params){
        $loader = new \Twig\Loader\FilesystemLoader(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."template");
        $twig = new \Twig\Environment($loader, [
            'cache' => false,
            // 'cache' => dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."cache",
        ]);
        return $twig->render($template,$params);
    }
}