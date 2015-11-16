<?php
/**
 * FrontYAML
 *
 * @copyright Matthieu Napoli http://mnapoli.fr
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Mni\FrontYAML\Bridge\CommonMark;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Converter;
use Mni\FrontYAML\Markdown\MarkdownParser;

/**
 * Bridge to the League CommonMark parser
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class CommonMarkParser implements MarkdownParser
{
    public function __construct($commonMarkConverter = null)
    {
        if (null === $commonMarkConverter) {
            $commonMarkConverter = new CommonMarkConverter();
        }

        if ((class_exists('League\CommonMark\Converter') && $commonMarkConverter instanceof Converter) || $commonMarkConverter instanceof CommonMarkConverter) {
            $this->parser = $commonMarkConverter;
        } else {
            throw new \InvalidArgumentException(sprintf(
                'CommonMarkParser::__construct can only accept some converter (%s, %s), "%s" given',
                'League\CommonMark\Converter',
                'League\CommonMark\CommonMarkConverter',
                is_object($commonMarkConverter) ? get_class($commonMarkConverter) : gettype($commonMarkConverter)
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function parse($markdown)
    {
        return $this->parser->convertToHtml($markdown);
    }
}
