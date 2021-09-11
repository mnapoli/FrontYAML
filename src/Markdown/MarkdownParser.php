<?php declare(strict_types=1);

namespace Mni\FrontYAML\Markdown;

/**
 * Interface of a Markdown parser
 */
interface MarkdownParser
{
    /**
     * Parses a Markdown string to HTML.
     *
     * @param string $markdown Markdown document.
     *
     * @return string HTML document.
     */
    public function parse(string $markdown): string;
}
