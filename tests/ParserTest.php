<?php

namespace Mni\FrontYAML\Test;

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

    public function testParseFrontYAMLPregMatchDelimiter()
    {
        $parser = new Parser(null, null, '~', '~');
        $str = <<<EOF
~
~
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

    public function testParseMarkdownNoYAML1Line()
    {
        $yamlParser = $this->getMockForAbstractClass('Mni\FrontYAML\YAML\YAMLParser');
        $yamlParser->expects($this->never())
            ->method('parse');

        $markdownParser = $this->getMockForAbstractClass('Mni\FrontYAML\Markdown\MarkdownParser');
        $markdownParser->expects($this->once())
            ->method('parse')
            ->with('bim')
            ->will($this->returnValue('bam'));

        $parser = new Parser($yamlParser, $markdownParser);

        $str = <<<EOF
bim
EOF;
        $document = $parser->parse($str);

        $this->assertNull($document->getYAML());
        $this->assertEquals('bam', $document->getContent());
    }

    public function testParseMarkdownNoYAML2Lines()
    {
        $yamlParser = $this->getMockForAbstractClass('Mni\FrontYAML\YAML\YAMLParser');
        $yamlParser->expects($this->never())
            ->method('parse');

        $markdownParser = $this->getMockForAbstractClass('Mni\FrontYAML\Markdown\MarkdownParser');
        $markdownParser->expects($this->once())
            ->method('parse')
            ->will($this->returnValue('foo'));

        $parser = new Parser($yamlParser, $markdownParser);

        $str = <<<EOF
bim
bam
EOF;
        $document = $parser->parse($str);

        $this->assertNull($document->getYAML());
        $this->assertEquals('foo', $document->getContent());
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

    public function testParseFrontYAMLEdgeCaseDelimiters()
    {
        $start = '*_-\)``.|.``(/-_*';
        $end = '--({@}{._.}{@})--';
        $yamlParser = $this->getMockForAbstractClass('Mni\FrontYAML\YAML\YAMLParser');
        $yamlParser->expects($this->once())
            ->method('parse')
            ->with('foo: bar')
            ->will($this->returnValue(array('foo' => 'bar')));

        $markdownParser = $this->getMockForAbstractClass('Mni\FrontYAML\Markdown\MarkdownParser');
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
        $this->assertEquals('bim', trim($document->getContent()));
    }

    public function testParseFrontYAMLArrayDelimiters()
    {
        $start = array('---','<!--');
        $end = array('---','-->');
        $yamlParser = $this->getMockForAbstractClass('Mni\FrontYAML\YAML\YAMLParser');
        $yamlParser->expects($this->exactly(2))
            ->method('parse')
            ->with('foo: bar')
            ->will($this->returnValue(array('foo' => 'bar')));

        $markdownParser = $this->getMockForAbstractClass('Mni\FrontYAML\Markdown\MarkdownParser');
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
        $this->assertEquals('bim', trim($document1->getContent()));
        $this->assertSame($document1->getYAML(), $document2->getYAML());
        $this->assertEquals($document1->getContent(), $document2->getContent());
    }
}
