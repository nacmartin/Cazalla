<?php
namespace Cazalla;

use Symfony\Component\ClassLoader\UniversalClassLoader;

class Application extends \Pimple
{
    public function __construct()
    {
        $app = $this;

        $this['autoloader'] = $this->share(function () {
            $loader = new UniversalClassLoader();
            $loader->register();

            return $loader;
        });
    }

    public function register_twig(){
        $app = $this;

        //Register twig
        $app['twig'] = $app->share(function () use ($app) {
            $twig = new \Twig_Environment($app['twig.loader'], isset($app['twig.options']) ? $app['twig.options'] : array());
            $twig->addGlobal('app', $app);
            return $twig;
        });

        $app['twig.loader'] = $app->share(function () use ($app) {
            if (isset($app['twig.templates']) && isset($app['twig.layouts'])) {
                return new \Twig_Loader_Filesystem(array($app['twig.templates'], $app['twig.layouts'] ));
            }
        });


        if (isset($app['twig.class_path'])) {
            $app['autoloader']->registerPrefix('Twig_', $app['twig.class_path']);
        }

    }

    public function compile()
    {
        $app = $this;
        echo $app['twig']->render('hola.twig');
    }
}
