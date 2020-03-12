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

namespace phpDocumentor\Reflection;

use InvalidArgumentException;
use LogicException;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\StandardTagFactory;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\TagFactory;
use Webmozart\Assert\Assert;
use function array_shift;
use function count;
use function explode;
use function is_object;
use function method_exists;
use function preg_match;
use function preg_replace;
use function str_replace;
use function strpos;
use function substr;
use function trim;

final class DocBlockFactory implements DocBlockFactoryInterface
{
    /** @var DocBlock\DescriptionFactory */
    private $descript