<?php declare(strict_types=1);

namespace Mni\FrontYAML;

use Mni\FrontYAML\Bridge\CommonMark\CommonMarkParser;
use Mni\FrontYAML\Bridge\Symfony\SymfonyYAMLParser;
use Mni\FrontYAML\Markdown\MarkdownParser;
use Mni\FrontYAML\YAML\YAMLParser;

/**
 * YAML Front matter parser
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

    private array $startSep;

    private array $endSep;

    /**
     * @param string|string[] $startSep
     * @param string|string[] $endSep
     */
    public function __construct(
        YAMLParser $yamlParser = null,
        MarkdownParser $markdownParser = null,
        $startSep = '---',
        $endSep = '---'
    ) {
        $this->yamlParser = $yamlParser ?: new SymfonyYAMLParser;
        $this->markdownParser = $markdownParser ?: new CommonMarkParser;
        $this->startSep = array_filter((array) $startSep, 'is_string') ?: ['---'];
        $this->endSep = array_filter((array) $endSep, 'is_string') ?: ['---'];
    }

    /**
     * Parse a string containing the YAML front matter and the markdown.
     *
     * @param bool $parseMarkdown Should the Markdown be turned into HTML?
     */
    public function parse(string $str, bool $parseMarkdown = true): Document
    {
        $yaml = null;

        $quote = static function ($str) {
            return preg_quote($str, "~");
        };

        $regex = '~^('
            .implode('|', array_map($quote, $this->startSep)) # $matches[1] start separator
            ."){1}[\r\n|\n]*(.*?)[\r\n|\n]+("                       # $matches[2] between separators
            .implode('|', array_map($quote, $this->endSep))   # $matches[3] end separator
            ."){1}[\r\n|\n]*(.*)$~s";                               # $matches[4] document content

        if (preg_match($regex, $str, $matches) === 1) { // There is a Front matter
            $yaml = trim($matches[2]) !== '' ? $this->yamlParser->parse(trim($matches[2])) : null;
            $str = ltrim($matches[4]);
        }

        return new Document($yaml, $parseMarkdown ? $this->markdownParser->parse($str) : $str);
    }
}
