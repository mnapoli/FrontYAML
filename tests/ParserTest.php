<?php declare(strict_types=1);

namespace Mni\FrontYAML\Test;

use Mni\FrontYAML\Parser;
use PHPUnit\Framework\TestCase;
use Mni\FrontYAML\YAML\YAMLParser;
use Mni\FrontYAML\Markdown\MarkdownParser;

class ParserTest extends TestCase
{
    public function testParseEmptyString()
    {
        $parser = new Parser();
        $document = $parser->parse('', false);
        $this->assertNull($document->getYAML());
        $this->assertSame('', $document->getContent());
    }

    public function testParseNoYAML()
    {
        $parser = new Parser();
        $document = $parser->parse('foo', false);
        $this->assertNull($document->getYAML());
        $this->assertSame('foo', $document->getContent());
    }

    public function testParseNoYAML2()
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

    public function testParseFrontYAMLDelimiter()
    {
        $parser = new Parser();
        $document = $parser->parse('---', false);
        $this->assertNull($document->getYAML());
        $this->assertSame('---', $document->getContent());
    }

    public function testParseFrontYAMLDelimiters()
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

    public function testParseFrontYAMLPregMatchDelimiter()
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

    public function testParseYAML()
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

    public function testParseYAMLMarkdown()
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

    public function testParseMarkdownNoYAML1Line()
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

    public function testParseMarkdownNoYAML2Lines()
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

    public function testMarkdownParserNotCalled()
    {
        $yamlParser = $this->createMock(YAMLParser::class);

        $markdownParser = $this->createMock(MarkdownParser::class);
        $markdownParser->expects($this->never())
            ->method('parse');

        $parser = new Parser($yamlParser, $markdownParser);
        $parser->parse('foo', false);
    }

    public function testParseFrontYAMLEdgeCaseDelimiters()
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
        $this->assertSame(array('foo' => 'bar'), $document->getYAML());
        $this->assertSame('bim', trim($document->getContent()));
    }

    public function testParseFrontYAMLArrayDelimiters()
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
        $this->assertSame(array('foo' => 'bar'), $document1->getYAML());
        $this->assertSame('bim', trim($document1->getContent()));
        $this->assertSame($document1->getYAML(), $document2->getYAML());
        $this->assertSame($document1->getContent(), $document2->getContent());
    }
}
