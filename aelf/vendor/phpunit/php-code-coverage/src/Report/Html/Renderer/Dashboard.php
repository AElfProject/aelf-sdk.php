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

use Mockery as m;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\ClassString;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;
use stdClass;
use function get_class;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\TypeResolver
 */
class TypeResolverTest extends TestCase
{
    /**
     * Call Mockery::close after each test.
     */
    public function tearDown() : void
    {
        m::close();
    }

    /**
     * @uses         \phpDocumentor\Reflection\Types\Context
     * @uses         \phpDocumentor\Reflection\Types\Array_
     * @uses         \phpDocumentor\Reflection\Types\Object_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @dataProvider provideKeywords
     */
    public function testResolvingKeywords(string $keyword, string $expectedClass) : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve($keyword, new Context(''));

        $this->assertInstanceOf($expectedClass, $resolvedType);
    }

    /**
     * @uses         \phpDocumentor\Reflection\Types\Context
     * @uses         \phpDocumentor\Reflection\Types\Object_
     * @uses         \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @dataProvider provideClassStrings
     */
    public function testResolvingClassStrings(string $classString, bool $throwsException) : void
    {
        $fixture = new TypeResolver();

        if ($throwsException) {
            $this->expectException('RuntimeException');
        }

        $resolvedType = $fixture->resolve($classString, new Context(''));

        $this->assertInstanceOf(ClassString::class, $resolvedType);
    }

    /**
     * @uses         \phpDocumentor\Reflection\Types\Context
     * @uses         \phpDocumentor\Reflection\Types\Object_
     * @uses         \phpDocumentor\Reflection\Fqsen
     * @uses         \phpDocumentor\Reflection\FqsenResolver
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @dataProvider provideFqcn
     */
    public function testResolvingFQSENs(string $fqsen) : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve($fqsen, new Context(''));

        $this->assertInstanceOf(Object_::class, $resolvedType);
        $this->assertInstanceOf(Fqsen::class, $resolvedType->getFqsen());
        $this->assertSame($fqsen, (string) $resolvedType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingRelativeQSENsBasedOnNamespace() : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('DocBlock', new Context('phpDocumentor\Reflection'));

        $this->assertInstanceOf(Object_::class, $resolvedType);
        $this->assertInstanceOf(Fqsen::class, $resolvedType->getFqsen());
        $this->assertSame('\phpDocumentor\Reflection\DocBlock', (string) $resolvedType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingRelativeQSENsBasedOnNamespaceAlias() : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve(
            'm\MockInterface',
            new Context('phpDocumentor\Reflection', ['m' => m::class])
        );

        $this->assertInstanceOf(Object_::class, $resolvedType);
        $this->assertInstanceOf(Fqsen::class, $resolvedType->getFqsen());
        $this->assertSame('\Mockery\MockInterface', (string) $resolvedType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingTypedArrays() : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('string[]', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('string[]', (string) $resolvedType);
        $this->assertInstanceOf(Compound::class, $resolvedType->getKeyType());
        $this->assertInstanceOf(Types\String_::class, $resolvedType->getValueType());
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Nullable
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingNullableTypes() : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('?string', new Context(''));

        $this->assertInstanceOf(Nullable::class, $resolvedType);
        $this->assertInstanceOf(String_::class, $resolvedType->getActualType());
        $this->assertSame('?string', (string) $resolvedType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingNestedTypedArrays() : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('string[][]', new Context(''));

        $childValueType = $resolvedType->getValueType();

        $this->assertInstanceOf(Array_::class, $resolvedType);

        $this->assertSame('string[][]', (string) $resolvedType);
        $this->assertInstanceOf(Compound::class, $resolvedType->getKeyType());
        $this->assertInstanceOf(Array_::class, $childValueType);

        $this->assertSame('string[]', (string) $childValueType);
        $this->assertInstanceOf(Compound::class, $childValueType->getKeyType());
        $this->assertInstanceOf(Types\String_::class, $childValueType->getValueType());
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\String_
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingCompoundTypes() : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('string|Reflection\DocBlock', new Context('phpDocumentor'));

        $this->assertInstanceOf(Compound::class, $resolvedType);
        $this->assertSame('string|\phpDocumentor\Reflection\DocBlock', (string) $resolvedType);

        $firstType = $resolvedType->get(0);

        $secondType = $resolvedType->get(1);

        $this->assertInstanceOf(Types\String_::class, $firstType);
        $this->assertInstanceOf(Object_::class, $secondType);
        $this->assertInstanceOf(Fqsen::class, $secondType->getFqsen());
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingCompoundTypedArrayTypes() : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('\stdClass[]|Reflection\DocBlock[]', new Context('phpDocumentor'));

        $this->assertInstanceOf(Compound::class, $resolvedType);
        $this->assertSame('\stdClass[]|\phpDocumentor\Reflection\DocBlock[]', (string) $resolvedType);

        $firstType = $resolvedType->get(0);

        $secondType = $resolvedType->get(1);

        $this->assertInstanceOf(Array_::class, $firstType);
        $this->assertInstanceOf(Array_::class, $secondType);
        $this->assertInstanceOf(Object_::class, $firstType->getValueType());
        $this->assertInstanceOf(Object_::class, $secondType->getValueType());
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\String_
     * @uses \phpDocumentor\Reflection\Types\Nullable
     * @uses \phpDocumentor\Refle