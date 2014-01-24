<?php

namespace Test\Mni\FrontYAML\Parser;

use Mni\FrontYAML\Parser;

/**
 * @covers Mni\FrontYAML\Parser
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParseEmptyString()
    {
        $parser = new Parser();
        $document = $parser->parse('', false);
        $this->assertNull($document->getYAML());
        $this->assertEquals('', $document->getContent());
    }

    public function testParseNoYAML()
    {
        $parser = new Parser();
        $document = $parser->parse('foo', false);
        $this->assertNull($document->getYAML());
        $this->assertEquals('foo', $document->getContent());
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
        $this->assertEquals($str, $document->getContent());
    }

    public function testParseFrontYAMLDelimiter()
    {
        $parser = new Parser();
        $document = $parser->parse('---', false);
        $this->assertNull($document->getYAML());
        $this->assertEquals('---', $document->getContent());
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
        $this->assertEquals('', $document->getContent());
    }

    public function testParseYAML()
    {
        $yamlParser = $this->getMockForAbstractClass('Mni\FrontYAML\YAML\YAMLParser');
        $yamlParser->expects($this->once())
            ->method('parse')
            ->with('foo')
            ->will($this->returnValue('bar'));

        $markdownParser = $this->getMockForAbstractClass('Mni\FrontYAML\Markdown\MarkdownParser');
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

        $this->assertEquals('bar', $document->getYAML());
        $this->assertEquals('bim', $document->getContent());
    }

    public function testParseYAMLMarkdown()
    {
        $yamlParser = $this->getMockForAbstractClass('Mni\FrontYAML\YAML\YAMLParser');
        $yamlParser->expects($this->once())
            ->method('parse')
            ->with('foo')
            ->will($this->returnValue('bar'));

        $markdownParser = $this->getMockForAbstractClass('Mni\FrontYAML\Markdown\MarkdownParser');
        $markdownParser->expects($this->once())
            ->method('parse')
            ->with('bim')
            ->will($this->returnValue('bam'));

        $parser = new Parser($yamlParser, $markdownParser);

        $str = <<<EOF
---
foo
---
bim
EOF;
        $document = $parser->parse($str);

        $this->assertEquals('bar', $document->getYAML());
        $this->assertEquals('bam', $document->getContent());
    }

    public function testMarkdownParserNotCalled()
    {
        $yamlParser = $this->getMockForAbstractClass('Mni\FrontYAML\YAML\YAMLParser');

        $markdownParser = $this->getMockForAbstractClass('Mni\FrontYAML\Markdown\MarkdownParser');
        $markdownParser->expects($this->never())
            ->method('parse');

        $parser = new Parser($yamlParser, $markdownParser);
        $parser->parse('foo', false);
    }
}
