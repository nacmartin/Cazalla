<?php
namespace Cazalla;

use Symfony\Component\ClassLoader\UniversalClassLoader;

class Page implements \ArrayAccess
{
    private $content;
    private $renderedContent;
    private $parameters;
    private $className;

    /**
     * __construct 
     * 
     * @param string $content
     * @param array $parameters
     * @return void
     */
    public function __construct($content, $parameters, $className)
    {
        $this->content = $content;
        $this->parameters = $parameters;
        $this->className = $className;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getRenderedContent()
    {
        return $this->renderedContent;
    }

    public function setRenderedContent($renderedContent)
    {
        $this->renderedContent = $renderedContent;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->parameters[] = $value;
        } else {
            $this->parameters[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        return isset($this->parameters[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->parameters[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->parameters[$offset]) ? $this->parameters[$offset] : null;
    }
}
