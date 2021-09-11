<?php declare(strict_types=1);

namespace Mni\FrontYAML\Bridge\Symfony;

use Mni\FrontYAML\YAML\YAMLParser;
use Symfony\Component\Yaml\Parser;

/**
 * Bridge to the Symfony YAML parser
 */
class SymfonyYAMLParser implements YAMLParser
{
    private Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser;
    }

    public function parse(string $yaml)
    {
        return $this->parser->parse($yaml);
    }
}
