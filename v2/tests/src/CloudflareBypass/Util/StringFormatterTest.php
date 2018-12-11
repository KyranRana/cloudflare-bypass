<?php
use PHPUnit\Framework\TestCase;

use CloudflareBypass\Util\StringFormatter;

/**
 * String formatter tests.
 * -> testFormatContent - Tests formatContent displays content in a rectangular block.
 */
class StringFormatterTest extends TestCase
{
    /**
     * Tests formatContent displays content in a rectangular block.
     * @return void.
     */
    public function testFormatContent() {
        $this->assertSame( StringFormatter::formatContent( "123412341234", "\t", 4 ), "\t1234\n\t1234\n\t1234\n" );
        $this->assertSame( StringFormatter::formatContent( "123123123123", "", 3 ), "123\n123\n123\n123\n" );
        $this->assertSame( StringFormatter::formatContent( "abcdefgabcdefgabcdefgabcdefg", "\t", 14 ), "\tabcdefgabcdefg\n\tabcdefgabcdefg\n" );
        $this->assertSame( StringFormatter::formatContent( "i9dei9ei3iicapsd3i0dkoekde0mfofo", "", 12 ), "i9dei9ei3iic\napsd3i0dkoek\nde0mfofo\n" );
        $this->assertSame( StringFormatter::formatContent( "", "", 12 ), "\n" );
    }
}

