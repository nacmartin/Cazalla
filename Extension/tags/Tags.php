<?php
namespace Cazalla\Extension;

use Cazalla\ExtensionInterface;
use Cazalla\Application;

class TagsExtension implements ExtensionInterface
{
    public function register(Application $app)
    {
        $that = $this;
        $app['tags'] = $app->share(function () use ($app, $that) {
            return $that;
        });
        if (isset($app['buzz.class_path'])) {
            $app['autoloader']->registerNamespace('Buzz', $app['buzz.class_path']);
        }
        $app['autoloader']->registerNamespace('Tags', __DIR__.'/extensions/tags');
    }

    public function sayHi()
    {
        echo "hi";
    }
}
