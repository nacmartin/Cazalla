<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/autoload.php';

use Cazalla\Application;

$app = new Application();

$app['autoloader']->registerNamespace('Tags', __DIR__.'/extensions/tags');

$app->register(new Cazalla\Extension\Tag\TagExtension(), array('tags.decorator' => 'tagsDecorator.twig'));

$app->make();
