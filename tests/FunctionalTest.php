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
        $yaml =  $document->getYAML();
        $this->assertEquals($this->normalizeEOL($expected), $this->normalizeEOL($yaml['foo']));
        $this->assertEquals('<p>Foo</p>', $document->getContent());
    }

    public function testMultilineMarkdown()
    {
        $parser = new Parser(new SymfonyYAMLParser(), new ParsedownParser());
        $str = <<<EOF
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

    public function testCrossOsMultiline() {
      $parser = new Parser(new SymfonyYAMLParser(), new ParsedownParser());
      // the 2 files have exact same content,
      // but one is encoded using DOS-like EOL, the other using UNIX-like EOL
      $unix = file_get_contents( __DIR__.DIRECTORY_SEPARATOR.'_files'.DIRECTORY_SEPARATOR.'unix.md');
      $dos = file_get_contents( __DIR__.DIRECTORY_SEPARATOR.'_files'.DIRECTORY_SEPARATOR.'dos.md');
      $doc_unix = $parser->parse($unix);
      $doc_dos = $parser->parse($dos);

      $dos_yaml = $doc_dos->getYAML();
      $unix_yaml = $doc_unix->getYAML();

      $expected = <<<EOF
I am
a multine text
EOF;

      $this->assertSame($this->normalizeEOL($doc_dos->getContent()), $this->normalizeEOL($doc_unix->getContent()));
      $this->assertSame($dos_yaml, $unix_yaml);
      $this->assertSame('ipsum', $dos_yaml['lorem']);
      $this->assertSame($this->normalizeEOL($expected), $this->normalizeEOL($dos_yaml['multiline']));

    }

    private function normalizeEOL($str) {
      return implode(PHP_EOL, array_map('rtrim', preg_split('~\n~', $str)));
    }
}
