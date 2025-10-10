<?php declare(strict_types=1);

namespace Mni\FrontYAML\Test;

use Mni\FrontYAML\Parser;
use PHPUnit\Framework\TestCase;

use function str_replace;

final class FunctionalTest extends TestCase
{
    public function testSimpleDocument(): void
    {
        $parser = new Parser();

        $str = <<<EOF
---
foo: bar
---
This **strong**
EOF;
        $document = $parser->parse($str);

        $this->assertEquals(array('foo' => 'bar'), $document->getYAML());
        $this->assertSame("<p>This <strong>strong</strong></p>\n", $document->getContent());
    }

    public function testEscaping(): void
    {
        $parser = new Parser();

        $str = <<<EOF
---
foo: |
     This is a multiline
     text containing ---
     --- hello
     ---
     foo bar
---
Foo
EOF;
        $document = $parser->parse($str);

        $expected = <<<EOF
This is a multiline
text containing ---
--- hello
---
foo bar
EOF;
        $yaml = $document->getYAML();
        $this->assertEquals($this->normalizeEOL($expected), $this->normalizeEOL($yaml['foo']));
        $this->assertSame("<p>Foo</p>\n", $document->getContent());
    }

    public function testMultilineMarkdown(): void
    {
        $parser   = new Parser();
        $str      = <<<EOF
Foo

Bar
EOF;
        $document = $parser->parse($str);
        $expected = <<<EOF
<p>Foo</p>
<p>Bar</p>

EOF;
        $this->assertEquals($this->normalizeEOL($expected), $this->normalizeEOL($document->getContent()));
    }

    public function testCrossOsMultiline(): void
    {
        $parser  = new Parser();
        $content = <<<EOF
---
lorem: ipsum
multiline: |
           I am
           a multine text
---
Lorem
Ipsum
EOF;
        $unix = str_replace("\r", '', $content);
        $dos = str_replace("\n", "\r\n", $unix);

        $unixDocument = $parser->parse($unix);
        $dosDocument = $parser->parse($dos);

        $dosYaml = $dosDocument->getYAML();
        $unixYaml = $unixDocument->getYAML();

        $expectedHtml = <<<EOF
I am
a multine text
EOF;

        $this->assertSame(
            $this->normalizeEOL($dosDocument->getContent()),
            $this->normalizeEOL($unixDocument->getContent())
        );
        $this->assertSame($dosYaml, $unixYaml);
        $this->assertSame('ipsum', $dosYaml['lorem']);
        $this->assertSame($this->normalizeEOL($expectedHtml), $this->normalizeEOL($dosYaml['multiline']));
    }

    public function testNonGreedySeparator(): void
    {
        $parser   = new Parser();
        $content  = <<<EOF
---
lorem: ipsum
---
Lorem
---
Ipsum
EOF;
        $document = $parser->parse($content);
        $this->assertSame(['lorem' => 'ipsum'], $document->getYAML());
    }

    private function normalizeEOL(string $str): string|array
    {
        return str_replace("\r", '', $str);
    }
}
