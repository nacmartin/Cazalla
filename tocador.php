<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/autoload.php';
require_once __DIR__.'/Extension/tags/Tags.php';


use Cazalla\Application;

$app = new Application();

$app['twig.layouts'] = __DIR__.'/layouts';
$app['twig.templates'] = __DIR__.'/content';
$app['twig.class_path'] = __DIR__.'/vendor/twig/lib';
$app['output'] = __DIR__.'/output';
$app['cache'] = __DIR__.'/cache';

$app['autoloader']->registerNamespace('Tags', __DIR__.'/extensions/tags');

$app->register(new Cazalla\Extension\TagsExtension(), array('tags.decorator' => 'tagsDecorator.twig'));

$app->register_twig();

$app->make();
