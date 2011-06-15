<?php
namespace Cazalla\Extension;

use Cazalla\ExtensionInterface;
use Cazalla\Application;
use Cazalla\Page;

class TagsExtension implements ExtensionInterface
{
    private $tags = array();
    private $app;

    public function register(Application $app)
    {
        $this->app = $app;
        $that = $this;
        $app['tags'] = $app->share(function () use ($app, $that) {
            return $that;
        });

        $app->addModifier('tags', 'storeTags');
        $app->addPostModifier('tags', 'createTags');
    }


    public function storeTags(Page $page)
    {
        foreach ($page['tags'] as $tag) {
            if (!array_key_exists($tag, $this->tags)) {
                $this->tags[$tag] = array($page);
            }else{
                array_push($this->tags, $page);
            }
        }
        return $page;
    }

    public function createTags()
    {
        $fh = fopen($this->app['cache'].'/tags.twig', 'w');
        $strout = '{% extends "'.$this->app['tags.layout'].'" %}';
        $strout .= '{% block tags %}';
        $strout .= "<ul>\n";
        foreach ($this->tags as $tag => $pages){
            $strout .="<li>$tag</li>\n";
        }
        $strout .= "</ul>\n";
        $strout .= '{% endblock %}';
        fwrite($fh, $strout);
        fclose($fh);
    }
}
