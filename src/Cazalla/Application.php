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

    /**
     * registers Twig 
     * 
     * @return void
     */
    public function register_twig()
    {
        $app = $this;

        //Register twig
        $app['twig'] = $app->share(function () use ($app) {
            $twig = new \Twig_Environment(
                $app['twig.loader'],
                isset($app['twig.options']) ? $app['twig.options'] : array()
            );
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

        if ($handle = opendir($app['twig.templates'])) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $out = $app->parse($file);
                    $fh = fopen($app['output'].'/'.preg_replace('/\.twig/', '.html', $file), 'w');
                    fwrite($fh, $out);
                    fclose($fh);
                }
            }
        }
    }

    public function parse($file)
    {
        $content = file_get_contents($this['twig.templates'].'/'.$file, 'r');
        $cls = $this['twig']->getTemplateClass($file);
        eval('?>'.$this['twig']->compileSource($content, $file));
        $templ = new $cls($this['twig']);
        return $templ->render(array());

    }
}
