<?php declare(strict_types=1);

namespace Mni\FrontYAML\Bridge\CommonMark;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\ConverterInterface;
use Mni\FrontYAML\Markdown\MarkdownParser;

/**
 * Bridge to the League CommonMark parser
 */
class CommonMarkParser implements MarkdownParser
{
    private ConverterInterface $parser;

    public function __construct(ConverterInterface|null $commonMarkConverter = null)
    {
        $this->parser = $commonMarkConverter ?: new CommonMarkConverter;
    }

    public function parse(string $markdown): string
    {
        return $this->parser->convert($markdown)->getContent();
    }
}
