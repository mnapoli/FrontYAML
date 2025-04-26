<?php declare(strict_types=1);

namespace Mni\FrontYAML\YAML;

/**
 * Interface of a YAML parser
 */
interface YAMLParser
{
    /**
     * Parses a YAML string.
     */
    public function parse(string $yaml): mixed;
}
