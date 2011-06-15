<?php
require_once __DIR__.'/vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';
require_once __DIR__.'/vendor/yaml/lib/sfYaml.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony' => __DIR__.'/vendor',
    'Cazalla' => __DIR__.'/src',
));
$loader->registerPrefixes(array(
    'Pimple' => __DIR__.'/vendor/pimple/lib',
    'Twig' => __DIR__.'/vendor/Twig/lib',
));
$loader->register();
