<?php

/*
 * This file is part of composer/semver.
 *
 * (c) Composer <https://github.com/composer>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Composer\Semver\Constraint;

/**
 * Defines a constraint.
 */
class Constraint implements ConstraintInterface
{
    /* operator integer values */
    const OP_EQ = 0;
    const OP_LT = 1;
    const OP_LE = 2;
    const OP_GT = 3;
    const OP_GE = 4;
    const OP_NE = 5;

    /**
     * Operator to integer translation table.
     *
     * @var array
     */
    private static $transOpStr = array(
        '=' => self::OP_EQ,
        '==' => self::OP_EQ,
        '<' => self::OP_LT,
        '<=' => self::OP_LE,
        '>' => self::OP_GT,
        '>=' => self::OP_GE,
        '<>' => self::OP_NE,
        '!=' => self::OP_NE,
    );

    /**
     * Integer to operator translation table.
     *
     * @var array
     */
    private static $transOpInt = array(
        self::OP_EQ => '==',
        self::OP_LT => '<',
        self::OP_LE => '<=',
        self::OP_GT => '>',
        self::OP_GE => '>=',
        self::OP_NE => '!=',
    );

    /** @var string */
    protected $operator;

    /** @var string */
    protected $version;

    /** @var string */
    protected $prettyString;

    /**
     * @param ConstraintInterface $provider
     *
     * @return bool
     */
    public function matches(ConstraintInterface $provider)
    {
        if ($provider instanceof $this) {
            return $this->matchSpecific($provider);
        }

        // turn matching around to find a match
        return $provider->matches($this);
    }

    /**
     * @param string $prettyString
     */
    public function setPrettyString($prettyString)
    {
        $this->prettyString = $prettyString;
    }

    /**
     * @return string
     */
    public function getPrettyString()
    {
        if ($this->prettyString) {
            return $this->prettyString;
        }

        return $this->__toString();
    }

    /**
     * Get all supported comparison operators.
     *
     * @return array
     */
    public static function getSupportedOperators()
    {
        return array_keys(self::$transOpStr);
    }

    /**
     * Sets operator and version to compare with.
     *
     * @param string $operator
     * @param string $version
     *
     * @throws \InvalidArgumentException if invalid operator is given.
     */
    public function __construct($operator, $version)
    {
        if (!isset(self::$transOpStr[$operator])) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid operator "%s" given, expected one of: %s',
                $operator,
                implode(', ', self::getSupportedOperators())
            ));
        }

        $this->operator = self::$transOpStr[$operator];
        $this->version = $version;
    }

    /**
     * @param string $a
     * @param string $b
     * @param string $operator
     * @param bool $compareBranches
     *
     * @throws \InvalidArgumentException if invalid operator is given.
     *
     * @return bool
     */
    public function versionCompare($a, $b, $operator, $compareBranches = false)
    {
        if (!isset(self::$transOpStr[$operator])) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid operator "%s" given, expected one of: %s',
                $operator,
                implode(', ', self::getSupportedOperators())
            ));
        }

        $aIsBranch = 'dev-' === substr($a, 0, 4);
        $bIsBranch = 'dev-' === substr($b, 0, 4);

        if ($aIsBranch && $bIsBranch) {
            return $operator === '==' && $a === $b;
        }

        // when branches are not comparable, we make sure dev branches never match anything
        if (!$compareBranches && ($aIsBranch || $bIsBranch)) {
            return false;
        }

        return version_compare($a, $b, $operator);
    }

    /**
     * @param Constraint $provider
     * @param bool $compareBranches
     *
     * @return bool
     */
    public function matchSpecific(Constraint $provider, $compareBranches = false)
    {
        $noEqualOp = str_replace('=', '', self::$transOpInt[$this->operator