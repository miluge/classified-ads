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
    public static function getRender(string $template, array $params){
        $loader = new \Twig\Loader\FilesystemLoader(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."template");
        $twig = new \Twig\Environment($loader, [
            // 'cache' => dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."cache",
            "cache" => false,
        ]);
        return $twig->render($template,$params);
    }
}