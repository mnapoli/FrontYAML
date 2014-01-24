<?php

namespace Test\Mni\FrontYAML\Parser;

use Mni\FrontYAML\Bridge\Parsedown\ParsedownParser;
use Mni\FrontYAML\Bridge\Symfony\SymfonyYAMLParser;
use Mni\FrontYAML\Parser;

/**
 * @coversNothing
 */
class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleDocument()
    {
        $parser = new Parser(new SymfonyYAMLParser(), new ParsedownParser());

        $str = <<<EOF
---
foo: bar
---
This **strong**
EOF;
        $document = $parser->parse($str);

        $this->assertEquals(array('foo' => 'bar'), $document->getYAML());
        $this->assertEquals('<p>This <strong>strong</strong></p>', $document->getContent());
    }

    public function testEscaping()
    {
        $parser = new Parser(new SymfonyYAMLParser(), new ParsedownParser());

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

        $this->assertEquals(array('foo' => $expected), $document->getYAML());
        $this->assertEquals('<p>Foo</p>', $document->getContent());
    }
}
