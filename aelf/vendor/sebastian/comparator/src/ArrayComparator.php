<?php

/*
 * This file is part of composer/semver.
 *
 * (c) Composer <https://github.com/composer>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Composer\Semver;

use Composer\Semver\Constraint\ConstraintInterface;
use Composer\Semver\Constraint\EmptyConstraint;
use Composer\Semver\Constraint\MultiConstraint;
use Composer\Semver\Constraint\Constraint;

/**
 * Version parser.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class VersionParser
{
    /**
     * Regex to match pre-release data (sort of).
     *
     * Due to backwards compatibility:
     *   - Instead of enforcing hyphen, an underscore, dot or nothing at all are also accepted.
     *   - Only stabilities as recognized by Composer are allowed to precede a numerical identifier.
     *   - Numerical-only pre-release identifiers are not supported, see tests.
     *
     *                        |--------------|
     * [major].[minor].[patch] -[pre-release] +[build-metadata]
     *
     * @var string
     */
    private static $modifierRegex = '[._-]?(?:(stable|beta|b|RC|alpha|a|patch|pl|p)((?:[.-]?\d+)*+)?)?([.-]?dev)?';

    /** @var array */
    private static $stabilities = array('stable', 'RC', 'beta', 'alpha', 'dev');

    /**
     * Returns the stability of a version.
     *
     * @param string $version
     *
     * @return string
     */
    public static function parseStability($version)
    {
        $version = preg_replace('{#.+$}i', '', $version);

        if (strpos($version, 'dev-') === 0 || '-dev' === substr($version, -4)) {
            return 'dev';
        }

        preg_match('{' . self::$modifierRegex . '(?:\+.*)?$}i', strtolower($version), $match);

        if (!empty($match[3])) {
            return 'dev';
        }

        if (!empty($match[1])) {
            if ('beta' === $match[1] || 'b' === $match[1]) {
                return 'beta';
            }
            if ('alpha' === $match[1] || 'a' === $match[1]) {
                return 'alpha';
            }
            if ('rc' === $match[1]) {
                return 'RC';
            }
        }

        return 'stable';
    }

    /**
     * @param string $stability
     *
     * @return string
     */
    public static function normalizeStability($stability)
    {
        $stability = strtolower($stability);

        return $stability === 'rc' ? 'RC' : $stability;
    }

    /**
     * Normalizes a version string to be able to perform comparisons on it.
     *
     * @param string $version
     * @param string $fullVersion optional complete version string to give more context
     *
     * @throws \UnexpectedValueException
     *
     * @return string
     */
    public function normalize($version, $fullVersion = null)
    {
        $version = trim($version);
        if (null === $fullVersion) {
            $fullVersion = $version;
        }

        // strip off aliasing
        if (preg_match('{^([^,\s]++) ++as ++([^,\s]++)$}', $version, $match)) {
            // verify that the alias is a version without constraint
            $this->normalize($match[2]);

            $version = $match[1];
        }

        // match master-like branches
        if (preg_match('{^(?:dev-)?(?:master|trunk|default)$}i', $version)) {
            return '9999999-dev';
        }

        // if requirement is branch-like, use full name
        if (stripos($version, 'dev-') === 0) {
            return 'dev-' . substr($version, 4);
        }

        // strip off build metadata
        if (preg_match('{^([^,\s+]++)\+[^\s]++$}', $version, $match)) {
            $version = $match[1];
        }

        // match classical versioning
        if (preg_match('{^v?(\d{1,5})(\.\d++)?(\.\d++)?(\.\d++)?' . self::$modifierRegex . '$}i', $version, $matches)) {
            $version = $matches[1]
                . (!empty($matches[2]) ? $matches[2] : '.0')
                . (!empty($matches[3]) ? $matches[3] : '.0')
                . (!empty($matches[4]) ? $matches[4] : '.0');
            $index = 5;
        // match date(time) based versioning
        } elseif (preg_match('{^v?(\d{4}(?:[.:-]?\d{2}){1,6}(?:[.:-]?\d{1,3})?)' . self::$modifierRegex . '