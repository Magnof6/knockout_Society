<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

require __DIR__ . '/../installs/vendor/autoload.php';

class MathTest extends TestCase
{
    public function testPruebaSuma()
    {
        $this->assertEquals(3 + 2, 5);
    }
}