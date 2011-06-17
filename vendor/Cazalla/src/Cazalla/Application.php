<?php
namespace Cazalla;

use Symfony\Component\ClassLoader\UniversalClassLoader;

class Application extends \Pimple
{
    protected $modifiers = array();
    protected $postModifiers = array();

    public function __construct()
    {
        $app = $this;

        $this['autoloader'] = $this->share(function () {
            $loader = new UniversalClassLoader();
            $loader->register();

            return $loader;
        });

        $this['basedir'] = __DIR__.'/../../../..';

        $this->register_twig();
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

        //Set base directories
        $basedir   = $app['basedir'];
        $app['twig.class_path'] = isset($app['twig.class_path']) ? $app['twig.class_path']: $basedir.'/vendor/twig/lib/';
        $app['twig.layouts'] = isset($app['twig.layouts']) ? $app['twig.layouts'] : $basedir.'/project/layouts';
        $app['twig.templates'] = isset($app['twig.templates']) ? $app['twig.templates']: $basedir.'/project/content';
        $app['output'] = isset($app['output']) ? $app['output'] : $basedir.'/project/output';
        $app['cache'] = isset($app['cache']) ? $app['output'] : $basedir.'/project/cache';

        $app['twig.loader'] = $app->share(function () use ($app) {
            return new \Twig_Loader_Filesystem(array($app['twig.templates'], $app['twig.layouts'], $app['cache'].'/imports'));
        });

        $app['autoloader']->registerPrefix('Twig_', $app['twig.class_path']);

    }

    public function make()
    {
        $app = $this;
        $pages = array();

        if ($handle = opendir($app['twig.templates'])) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $page = $app->parse($file);
                    $fileOutputName = preg_replace('/\.twig/', '.html', $file);
                    $page['ifilename'] = $file;
                    $page['filename'] = $fileOutputName;
                    $page = $this->executeModifiers($page);
                    array_push($pages, $page);
                }
            }
        }
        if (file_exists($app['cache'].'/pages') && $handle = opendir($app['cache'].'/pages')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $page = $app->parse($file);
                    $fileOutputName = preg_replace('/\.twig/', '.html', $file);
                    $page['ifilename'] = $file;
                    $page['filename'] = $fileOutputName;
                    array_push($pages, $page);
                }
            }
        }
        $this->executePostModifiers();
        foreach ($pages as $page){
            $fh = fopen($app['output'].'/'.$page['filename'], 'w');
            $page = $this->compile($page);
            fwrite($fh, $page->getRenderedContent());
            fclose($fh);
        }
    }

    public function parse($file)
    {
        $content = file_get_contents($this['twig.templates'].'/'.$file, 'r');
        preg_match('/---(.*)---(.*)/s', $content, $matches);
        $parameters = null;
        if($matches){
            $parameters = \sfYaml::load($matches[1]);
            $content = $matches[2];
        }
        $cls = $this['twig']->getTemplateClass($file);
        return new Page($content, $parameters, $cls);
    }

    public function compile($page)
    {
        eval('?>'.$this['twig']->compileSource($page->getContent(), $page['ifilename']));
        //echo      $this['twig']->compileSource($page->getContent(), $page['ifilename']);

        $className = $page->getClassName();
        $templ = new $className($this['twig']);
        $page->setRenderedContent($templ->render(array()));
        return $page;
    }

    public function register(ExtensionInterface $extension, array $values = array())
    {
        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }

        $extension->register($this);
    }


    public function addModifier($extension, $modifier)
    {
        array_push($this->modifiers, array('extension' => $extension, 'modifier' => $modifier));
    }

    public function addPostModifier($extension, $modifier)
    {
        array_push($this->postModifiers, array('extension' => $extension, 'modifier' => $modifier));
    }

    public function executeModifiers(Page $page)
    {
        foreach($this->modifiers as $modifier){
            $page = $this[$modifier['extension']]->$modifier['modifier']($page);
        }
        return $page;
    }

    public function executePostModifiers(){
        foreach($this->postModifiers as $modifier){
            $this[$modifier['extension']]->$modifier['modifier']();
        }
    }
}
