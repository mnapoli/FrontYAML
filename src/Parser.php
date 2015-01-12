<?php
/**
 * FrontYAML
 *
 * @copyright Matthieu Napoli http://mnapoli.fr
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Mni\FrontYAML;

use Mni\FrontYAML\Bridge\Parsedown\ParsedownParser;
use Mni\FrontYAML\Bridge\Symfony\SymfonyYAMLParser;
use Mni\FrontYAML\Markdown\MarkdownParser;
use Mni\FrontYAML\YAML\YAMLParser;

/**
 * YAML Front matter parser
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Parser
{
    /**
     * @var YAMLParser
     */
    private $yamlParser;

    /**
     * @var MarkdownParser
     */
    private $markdownParser;

    public function __construct(YAMLParser $yamlParser = null, MarkdownParser $markdownParser = null)
    {
        $this->yamlParser = $yamlParser ?: new SymfonyYAMLParser();
        $this->markdownParser = $markdownParser ?: new ParsedownParser();
    }

    /**
     * Parse a string containing the YAML front matter and the markdown.
     *
     * @param string $str
     * @param bool   $parseMarkdown Should the Markdown be turned into HTML?
     * @param string $startSep      Front matter separator start
     * @param string $endSep        Front matter separator end
     *
     * @return Document
     */
    public function parse($str, $parseMarkdown = true, $startSep = '---', $endSep = '---')
    {
        $left = '~^'.preg_quote($startSep).'{1}[\r\n|\n]+';
        $right = preg_quote($endSep).'{1}~s';

        $hasFrontMatter = preg_match($left.'(.*)[\r\n|\n]+'.$right, $str, $matches);

        $yamlContent = $hasFrontMatter ? trim($matches[1]) : false;

        if ($yamlContent === false) {
            $str = preg_replace($left.$right, '', $str, 1);
            if ($parseMarkdown) {
                $str = $this->markdownParser->parse($str);
            }
            return new Document(null, $str);
        }

        // There is a Front matter
        $yaml = $this->yamlParser->parse($yamlContent);
        $content = ltrim(preg_replace($left.'(.*)[\r\n|\n]+'.$right, '', $str, 1));

        if ($parseMarkdown) {
            $content = $this->markdownParser->parse($content);
        }

        return new Document($yaml, $content);
    }
}
