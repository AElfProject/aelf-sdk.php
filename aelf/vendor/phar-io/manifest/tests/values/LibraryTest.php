<?php

declare(strict_types=1);

namespace BitWasp\Buffertools\Tests;

use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\Buffertools;
use PHPUnit\Framework\TestCase;

class BuffertoolsTest extends TestCase
{
    /**
     * @return array
     */
    private function getUnsortedList(): array
    {
        return [
            '0101',
            '4102',
            'a43e',
            '0000',
            '0120',
            'd01b'
        ];
    }

    /**
     * @return array
     */
    private function getSortedList(): array
    {
        return [
            '0000',
            '0101',
            '0120',
            '4102',
            'a43e',
            'd01b'
        ];
    }

    /**
     * @return array
     */
    private function getUnsortedBufferList(): array
    {
        $results = [];
        foreach ($this->getUnsortedList() as $hex) {
            $results[] = Buffer::hex($hex);
        }
        return $results;
    }

    /**
     * @return array
     */
    private function getSortedBufferList(): array
    {