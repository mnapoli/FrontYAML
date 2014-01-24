# FrontYAML

An implementation of YAML Front matter for PHP.

## Installation

Require the project with Composer:

```json
{
    "require": {
        "mnapoli/front-yaml": "*"
    }
}
```

## Usage

```php
$parser = new Mni\FrontYAML\Parser();

$document = $parser->parse($str);

$yaml = $document->getYAML();
$html = $document->getContent();
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

FrontYAML uses by default [Parsedown Markdown parser](http://parsedown.org/).
