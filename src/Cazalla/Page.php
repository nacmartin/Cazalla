<?php
namespace Cazalla;

use Symfony\Component\ClassLoader\UniversalClassLoader;

class Page
{
    private $content;
    private $parameters;

    /**
     * __construct 
     * 
     * @param string $content
     * @param array $parameters
     * @return void
     */
    public function __construct($content, $parameters)
    {
        $this->content = $content;
        $this->parameters = $parameters;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}
