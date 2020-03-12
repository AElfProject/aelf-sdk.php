<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\DocBlock\Tags;

use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\Types\Context as TypeContext;
use Webmozart\Assert\Assert;
use function preg_match;

/**
 * Reflection class for a {@}source tag in a Docblock.
 */
final class Source extends BaseTag implements Factory\StaticMethod
{
    /** @var string */
    protected $name = 'source';

    /** @var int The starting line, relative to the structural element's location. */
    private $startingLine;

    /** @var int|null The number of lines, relative to the starting line. NULL means "to the end". */
    private $lineCount;

    /**
     * @param int|string      $startingLine should be a to int convertible value
     * @param int|string|null $lineCount    should be a to int convertible value
     */
    public function __construct($startingLine, $lineCount = null, ?Description $description = null)
    {
        Assert::integerish($startingLine);
        Assert::nullOrIntegerish($lineCount);

        $this->startingLine = (int) $startingLine;
        $this->lineCount    = $lineCount !== null ? (int) $lineCount : null;
        $this->description  = $description;
    }

    public static function create(
        string $body,
        ?DescriptionFactory $descriptionFactory = nu