<?php declare(strict_types=1);

namespace Mni\FrontYAML\Test;

use Mni\FrontYAML\Markdown\MarkdownParser;
use Mni\FrontYAML\Parser;
use Mni\FrontYAML\YAML\YAMLParser;
use PHPUnit\Framework\TestCase;

use function trim;

final class ParserTest extends TestCase
{
    public function testParseEmptyString(): void
    {
        $parser = new Parser();
        $document = $parser->parse('', false);
        $this->assertNull($document->getYAML());
        $this->assertSame('', $document->getContent());
    }

    public function testParseNoYAML(): void
    {
        $parser = new Parser();
        $document = $parser->parse('foo', false);
        $this->assertNull($document->getYAML());
        $this->assertSame('foo', $document->getContent());
    }

    public function testParseNoYAML2(): void
    {
        $parser = new Parser();
        $str = <<<EOF
foo
bar
EOF;
        $document = $parser->parse($str, false);
        $this->assertNull($document->getYAML());
        $this->assertSame($str, $document->getContent());
    }

    public function testParseFrontYAMLDelimiter(): void
    {
        $parser = new Parser();
        $document = $parser->parse('---', false);
        $this->assertNull($document->getYAML());
        $this->assertSame('---', $document->getContent());
    }

    public function testParseFrontYAMLDelimiters(): void
    {
        $parser = new Parser();
        $str = <<<EOF
---
---
EOF;
        $document = $parser->parse($str, false);
        $this->assertNull($document->getYAML());
        $this->assertSame('', $document->getContent());
    }

    public function testParseFrontYAMLPregMatchDelimiter(): void
    {
        $parser = new Parser(null, null, '~', '~');
        $str = <<<EOF
~
~
EOF;
        $document = $parser->parse($str, false);
        $this->assertNull($document->getYAML());
        $this->assertSame('', $document->getContent());
    }

    public function testParseYAML(): void
    {
        $yamlParser = $this->createMock(YAMLParser::class);
        $yamlParser->expects($this->once())
            ->method('parse')
            ->with('foo')
            ->willReturn('bar');

        $markdownParser = $this->createMock(MarkdownParser::class);
        $markdownParser->expects($this->never())
            ->method('parse');

        $parser = new Parser($yamlParser, $markdownParser);

        $str = <<<EOF
---
foo
---
bim
EOF;
        $document = $parser->parse($str, false);

        $this->assertSame('bar', $document->getYAML());
        $this->assertSame('bim', $document->getContent());
    }

    public function testParseYAMLMarkdown(): void
    {
        $yamlParser = $this->createMock(YAMLParser::class);
        $yamlParser->expects($this->once())
            ->method('parse')
            ->with('foo')
            ->willReturn('bar');

        $markdownParser = $this->createMock(MarkdownParser::class);
        $markdownParser->expects($this->once())
            ->method('parse')
            ->with('bim')
            ->willReturn('bam');

        $parser = new Parser($yamlParser, $markdownParser);

        $str = <<<EOF
---
foo
---
bim
EOF;
        $document = $parser->parse($str);

        $this->assertSame('bar', $document->getYAML());
        $this->assertSame('bam', $document->getContent());
    }

    public function testParseMarkdownNoYAML1Line(): void
    {
        $yamlParser = $this->createMock(YAMLParser::class);
        $yamlParser->expects($this->never())
            ->method('parse');

        $markdownParser = $this->createMock(MarkdownParser::class);
        $markdownParser->expects($this->once())
            ->method('parse')
            ->with('bim')
            ->willReturn('bam');

        $parser = new Parser($yamlParser, $markdownParser);

        $str = <<<EOF
bim
EOF;
        $document = $parser->parse($str);

        $this->assertNull($document->getYAML());
        $this->assertSame('bam', $document->getContent());
    }

    public function testParseMarkdownNoYAML2Lines(): void
    {
        $yamlParser = $this->createMock(YAMLParser::class);
        $yamlParser->expects($this->never())
            ->method('parse');

        $markdownParser = $this->createMock(MarkdownParser::class);
        $markdownParser->expects($this->once())
            ->method('parse')
            ->willReturn('foo');

        $parser = new Parser($yamlParser, $markdownParser);

        $str = <<<EOF
bim
bam
EOF;
        $document = $parser->parse($str);

        $this->assertNull($document->getYAML());
        $this->assertSame('foo', $document->getContent());
    }

    public function testMarkdownParserNotCalled(): void
    {
        $yamlParser = $this->createMock(YAMLParser::class);

        $markdownParser = $this->createMock(MarkdownParser::class);
        $markdownParser->expects($this->never())
            ->method('parse');

        $parser = new Parser($yamlParser, $markdownParser);
        $parser->parse('foo', false);
    }

    public function testParseFrontYAMLEdgeCaseDelimiters(): void
    {
        $start = '*_-\)``.|.``(/-_*';
        $end = '--({@}{._.}{@})--';
        $yamlParser = $this->createMock(YAMLParser::class);
        $yamlParser->expects($this->once())
            ->method('parse')
            ->with('foo: bar')
            ->willReturn(['foo' => 'bar']);

        $markdownParser = $this->createMock(MarkdownParser::class);
        $markdownParser->expects($this->never())
            ->method('parse');

        $parser = new Parser($yamlParser, $markdownParser, $start, $end);
        $str = <<<EOF
*_-\)``.|.``(/-_*
foo: bar
--({@}{._.}{@})--
bim
EOF;
        $document = $parser->parse($str, false);
        $this->assertSame(['foo' => 'bar'], $document->getYAML());
        $this->assertSame('bim', trim($document->getContent()));
    }

    public function testParseFrontYAMLArrayDelimiters(): void
    {
        $start = ['---','<!--'];
        $end = ['---','-->'];
        $yamlParser = $this->createMock(YAMLParser::class);
        $yamlParser->expects($this->exactly(2))
            ->method('parse')
            ->with('foo: bar')
            ->willReturn(['foo' => 'bar']);

        $markdownParser = $this->createMock(MarkdownParser::class);
        $markdownParser->expects($this->never())
            ->method('parse');

        $parser = new Parser($yamlParser, $markdownParser, $start, $end);
        $str1 = <<<EOF
<!--
foo: bar
-->
bim
EOF;
        $str2 = <<<EOF
---
foo: bar
---
bim
EOF;
        $document1 = $parser->parse($str1, false);
        $document2 = $parser->parse($str2, false);
        $this->assertSame(['foo' => 'bar'], $document1->getYAML());
        $this->assertSame('bim', trim($document1->getContent()));
        $this->assertSame($document1->getYAML(), $document2->getYAML());
        $this->assertSame($document1->getContent(), $document2->getContent());
    }
}
