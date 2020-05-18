<?php

namespace Mni\FrontYAML\Test\Bridge\CommonMark;

use Mni\FrontYAML\Bridge\CommonMark\CommonMarkParser;
use PHPUnit\Framework\TestCase;

class CommonMarkParserTest extends TestCase
{
    public function testParseWithDefaultParser()
    {
        $parser = new CommonMarkParser();

        $html = $parser->parse('# This is a title');

        $this->assertEquals("<h1>This is a title</h1>\n", $html);
    }

    public function testParseWithGivenParser()
    {
        $markdown = '# This is a title';
        $html = "<h1>This is a title</h1>\n";

        $commonMark = $this->createMock('League\CommonMark\CommonMarkConverter');
        $commonMark->expects($this->once())
            ->method('convertToHtml')
            ->with($markdown)
            ->willReturn($html);

        $parser = new CommonMarkParser($commonMark);

        $this->assertEquals($html, $parser->parse($markdown));
    }
}
