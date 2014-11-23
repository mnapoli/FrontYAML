<?php
/**
 * FrontYAML
 *
 * @copyright Matthieu Napoli http://mnapoli.fr
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Mni\FrontYAML;

/**
 * Document.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Document
{
    /**
     * @var mixed
     */
    private $yaml;

    /**
     * @var string
     */
    private $content;

    /**
     * @param mixed  $yaml    YAML content.
     * @param mixed $content Content of the document.
     */
    public function __construct($yaml, $content)
    {
        $this->yaml = $yaml;
        $this->content = $content;
    }

    /**
     * @return mixed YAML content.
     */
    public function getYAML()
    {
        if (is_callable($this->yaml)) {
            $this->yaml = call_user_func($this->yaml);
        }
        return $this->yaml;
    }

    /**
     * @return string Content of the document.
     */
    public function getContent()
    {
        if (is_callable($this->content)) {
            $this->content = call_user_func($this->content);
        }
        return $this->content;
    }
}
