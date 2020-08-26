<?php

require_once '../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('../application/template');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);


// Load template

$template = $twig->load('edit/edit_form.html.twig');
echo $template->render();