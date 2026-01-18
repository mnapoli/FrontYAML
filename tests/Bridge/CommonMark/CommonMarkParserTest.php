<?php declare(strict_types=1);

namespace Mni\FrontYAML\Test\Bridge\CommonMark;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use Mni\FrontYAML\Bridge\CommonMark\CommonMarkParser;
use PHPUnit\Framework\TestCase;

class CommonMarkParserTest extends TestCase
{
    public function testParseWithDefaultParser(): void
    {
        $parser = new CommonMarkParser();

        $html = $parser->parse('# This is a title');

        $this->assertSame("<h1>This is a title</h1>\n", $html);
    }

    public function testParseWithCustomParser(): void
    {
        $environment = new Environment;
        $environment->addExtension(new CommonMarkCoreExtension);
        $converter = new MarkdownConverter($environment);

        $parser = new CommonMarkParser($converter);

        $html = $parser->parse('# This is a title');

        $this->assertSame("<h1>This is a title</h1>\n", $html);
    }
}
