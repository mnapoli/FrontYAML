<?php declare(strict_types=1);

namespace Mni\FrontYAML;

class Document
{
    /** @var mixed */
    private $yaml;

    private string $content;

    /**
     * @param mixed $yaml YAML content.
     * @param string $content Content of the document.
     */
    public function __construct($yaml, string $content)
    {
        $this->yaml = $yaml;
        $this->content = $content;
    }

    /**
     * @return mixed YAML content.
     */
    public function getYAML()
    {
        return $this->yaml;
    }

    /**
     * @return string Content of the document.
     */
    public function getContent(): string
    {
        return $this->content;
    }
}
