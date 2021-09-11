<?php declare(strict_types=1);

namespace Mni\FrontYAML\Bridge\CommonMark;

use League\CommonMark\CommonMarkConverter;
use Mni\FrontYAML\Markdown\MarkdownParser;

/**
 * Bridge to the League CommonMark parser
 */
class CommonMarkParser implements MarkdownParser
{
    private CommonMarkConverter $parser;

    public function __construct(CommonMarkConverter $commonMarkConverter = null)
    {
        $this->parser = $commonMarkConverter ?: new CommonMarkConverter;
    }

    public function parse(string $markdown): string
    {
        return $this->parser->convertToHtml($markdown)->getContent();
    }
}
