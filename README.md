# FrontYAML

An implementation of YAML Front matter for PHP. It can parse both YAML **and** Markdown.

[![Total Downloads](https://poser.pugx.org/mnapoli/front-yaml/downloads.svg)](https://packagist.org/packages/mnapoli/front-yaml)

## Installation

Require the project with Composer:

```
composer require mnapoli/front-yaml
```

## Usage

```php
$parser = new Mni\FrontYAML\Parser;

$document = $parser->parse($str);

$yaml = $document->getYAML();
$html = $document->getContent();
```

If you don't want the Markdown to be parsed (maybe because it is not Markdown):

```php
$document = $parser->parse($str, false);
```

## Example

The following file:

```markdown
---
foo: bar
---
This is **strong**.
```

Will give:

```php
var_export($document->getYAML());
// array("foo" => "bar")

var_export($document->getContent());
// "<p>This is <strong>strong</strong></p>"
```

## YAML and Markdown parsers

```php
$parser = new Mni\FrontYAML\Parser($yamlParser, $markdownParser);
```

This library uses dependency injection and abstraction to allow you to provide your own YAML or Markdown parser.

```php
interface YAMLParser
{
    public function parse($yaml);
}
```

FrontYAML uses by default [Symfony's YAML parser](http://symfony.com/doc/current/components/yaml/introduction.html).

```php
interface MarkdownParser
{
    public function parse($markdown);
}
```

FrontYAML uses by default the [League CommonMark parser](https://github.com/thephpleague/commonmark).
