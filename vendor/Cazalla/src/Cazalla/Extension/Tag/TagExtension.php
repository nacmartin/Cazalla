<?php
namespace Cazalla\Extension\Tag;

use Cazalla\ExtensionInterface;
use Cazalla\Application;
use Cazalla\Page;

class TagExtension implements ExtensionInterface
{
    private $tags = array();
    private $app;
    private $decorator = null;

    public function register(Application $app)
    {
        $this->app = $app;
        $that = $this;
        $app['tags'] = $app->share(function () use ($app, $that) {
            return $that;
        });

        $app->addModifier('tags', 'storeTags');
        $app->addPostModifier('tags', 'createTags');

        if (isset($app['tags.decorator'])) {
            $this->decorator = $app['tags.decorator'];
        }
    }

    public function storeTags(Page $page)
    {
        if (!isset($pate['tags'])) {
            return;
        }

        foreach ($page['tags'] as $tag) {
            if (!array_key_exists($tag, $this->tags)) {
                $this->tags[$tag] = array($page);
            }else{
                array_push($this->tags[$tag], $page);
            }
        }
        return $page;
    }

    public function createTags()
    {
        if (!file_exists($this->app['cache'].'/imports')) {
            mkdir($this->app['cache'].'/imports');
        }
        $fh = fopen($this->app['cache'].'/imports/tags.twig', 'w');
        $strout = "";
        if ($this->decorator){
            $strout .= '{% extends "'.$this->decorator.'" %}';
            $strout .= '{% block tags %}';
        }
        $strout .= "<ul>\n";
        foreach ($this->tags as $tag => $pages){
            $strout .="<li><a href='tags/".$tag.".html'>".$tag."</a></li>\n";
        }
        $strout .= "</ul>\n";
        if ($this->decorator){
            $strout .= '{% endblock %}';
        }
        fwrite($fh, $strout);
        fclose($fh);

        //Create page for each tag
        if (!file_exists($this->app['output'].'/tags')) {
            mkdir($this->app['output'].'/tags');
        }
        foreach ($this->tags as $tag => $pages){
            $strout = "";
            $strout .=$tag."<ul>";
            foreach ($pages as $page){
                $title = isset($page['title']) ? $page['title'] : preg_replace('/\.[^\.]*$/', '',  $page['ifilename']);
                $strout .="<li><a href='../".$page["filename"]."'>".$title."</a></li>";
            }
            $strout .="</ul>";
            file_put_contents($this->app['output'].'/tags/'.$tag.'.html', $strout);
        }
    }
}
