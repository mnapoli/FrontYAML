<?php declare(strict_types=1);

namespace Mni\FrontYAML\Test\Bridge\CommonMark;

use Mni\FrontYAML\Bridge\CommonMark\CommonMarkParser;
use PHPUnit\Framework\TestCase;

class CommonMarkParserTest extends TestCase
{
    public function testParseWithDefaultParser()
    {
        $parser = new CommonMarkParser();

        $html = $parser->parse('# This is a title');

        $this->assertSame("<h1>This is a title</h1>\n", $html);
    }
}
