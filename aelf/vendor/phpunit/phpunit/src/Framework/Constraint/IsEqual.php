<?php

/*
 * This file is part of the webmozart/assert package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Assert;

use ArrayAccess;
use BadMethodCallException;
use Closure;
use Countable;
use Exception;
use InvalidArgumentException;
use ResourceBundle;
use SimpleXMLElement;
use Throwable;
use Traversable;

/**
 * Efficient assertions to validate the input/output of your methods.
 *
 * @method static void nullOrString($value, $message = '')
 * @method static void nullOrStringNotEmpty($value, $message = '')
 * @method static void nullOrInteger($value, $message = '')
 * @method static void nullOrIntegerish($value, $message = '')
 * @method static void nullOrFloat($value, $message = '')
 * @method static void nullOrNumeric($value, $message = '')
 * @method static void nullOrNatural($value, $message = '')
 * @method static void nullOrBoolean($value, $message = '')
 * @method static void nullOrScalar($value, $message = '')
 * @method static void nullOrObject($value, $message = '')
 * @method static void nullOrResource($value, $type = null, $message = '')
 * @method static void nullOrIsCallable($value, $message = '')
 * @method static void nullOrIsArray($value, $message = '')
 * @method static void nullOrIsTraversable($value, $message = '')
 * @method static void nullOrIsArrayAccessible($value, $message = '')
 * @method static void nullOrIsCountable($value, $message = '')
 * @method static void nullOrIsIterable($value, $message = '')
 * @method static void nullOrIsInstanceOf($value, $class, $message = '')
 * @method static void nullOrNotInstanceOf($value, $class, $message = '')
 * @method static void nullOrIsInstanceOfAny($value, $classes, $message = '')
 * @method static void nullOrIsAOf($value, $classes, $message = '')
 * @method static void nullOrIsAnyOf($value, $classes, $message = '')
 * @method static void nullOrIsNotA($value, $classes, $message = '')
 * @method static void nullOrIsEmpty($value, $message = '')
 * @method static void nullOrNotEmpty($value, $message = '')
 * @method static void nullOrTrue($value, $message = '')
 * @method static void nullOrFalse($value, $message = '')
 * @method static void nullOrNotFalse($value, $message = '')
 * @method static void nullOrIp($value, $message = '')
 * @method static void nullOrIpv4($value, $message = '')
 * @method static void nullOrIpv6($value, $message = '')
 * @method static void nullOrEmail($value, $message = '')
 * @method static void nullOrUniqueValues($values, $message = '')
 * @method static void nullOrEq($value, $expect, $message = '')
 * @method static void nullOrNotEq($value, $expect, $message = '')
 * @method static void nullOrSame($value, $expect, $message = '')
 * @method static void nullOrNotSame($value, $expect, $message = '')
 * @method static void nullOrGreaterThan($value, $limit, $message = '')
 * @method static void nullOrGreaterThanEq($value, $limit, $message = '')
 * @method static void nullOrLessThan($value, $limit, $message = '')
 * @method static void nullOrLessThanEq($value, $limit, $message = '')
 * @method static void nullOrRange($value, $min, $max, $message = '')
 * @method static void nullOrOneOf($value, $values, $message = '')
 * @method static void nullOrContains($value, $subString, $message = '')
 * @method static void nullOrNotContains($value, $subString, $message = '')
 * @method static void nullOrNotWhitespaceOnly($value, $message = '')
 * @method static void nullOrStartsWith($value, $prefix, $message = '')
 * @method static void nullOrStartsWithLetter($value, $message = '')
 * @method static void nullOrEndsWith($value, $suffix, $message = '')
 * @method static void nullOrRegex($value, $pattern, $message = '')
 * @method static void nullOrNotRegex($value, $pattern, $message = '')
 * @method static void nullOrUnicodeLetters($value, $message = '')
 * @method static void nullOrAlpha($value, $message = '')
 * @method static void nullOrDigits($value, $message = '')
 * @method static void nullOrAlnum($value, $message = '')
 * @method static void nullOrLower($value, $message = '')
 * @method static void nullOrUpper($value, $message = '')
 * @method static void nullOrLength($value, $length, $message = '')
 * @method static void nullOrMinLength($value, $min, $message = '')
 * @method static void nullOrMaxLength($value, $max, $message = '')
 * @method static void nullOrLengthBetween($value, $min, $max, $message = '')
 * @method static void nullOrFileExists($value, $message = '')
 * @method static void nullOrFile($value, $message = '')
 * @method static void nullOrDirectory($value, $message = '')
 * @method static void nullOrReadable($value, $message = '')
 * @m