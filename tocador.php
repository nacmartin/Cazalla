<?php
require_once __DIR__.'/app/autoload.php';

use Cazalla\Application;

$app = new Application();

$app['autoloader']->registerNamespace('Tags', __DIR__.'/extensions/tags');

$app->register(new Cazalla\Extension\Tag\TagExtension(), array('tags.decorator' => 'tagsDecorator.twig'));

$app->make();
