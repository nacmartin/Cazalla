<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/autoload.php';

use Cazalla\Application;

$app = new Application();

$app['twig.layouts']       = __DIR__.'/layouts';
$app['twig.templates']       = __DIR__.'/content';
$app['twig.class_path'] = __DIR__.'/vendor/twig/lib';
$app['output'] = __DIR__.'/output';

$app->register_twig();

$app->compile();
