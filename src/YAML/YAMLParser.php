<?php declare(strict_types=1);

namespace Mni\FrontYAML\YAML;

/**
 * Interface of a YAML parser
 */
interface YAMLParser
{
    /**
     * Parses a YAML string.
     *
     * @return mixed
     */
    public function parse(string $yaml);
}
