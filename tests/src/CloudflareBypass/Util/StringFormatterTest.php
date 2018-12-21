<?php
use PHPUnit\Framework\TestCase;

use CloudflareBypass\Util\StringFormatter;

/**
 * String formatter tests.
 * -> testFormatContent - Tests if "formatContent" displays content in a rectangular block.
 */
class StringFormatterTest extends TestCase
{
    /**
     * Tests content formatter.
     * @return void.
     */
    public function testFormatContent() {
        $this->assertSame( 
            "\t1234\n\t1234\n\t1234\n", StringFormatter::formatContent( "123412341234", "\t", 4 ),

            'StringFormatterTest::testFormatContent -> content is not formatted (#1)' );
        
        $this->assertSame( 
            "123\n123\n123\n123\n", StringFormatter::formatContent( "123123123123", "", 3 ),

            'StringFormatterTest::testFormatContent -> content is not formatted (#2)' );
        
        $this->assertSame( 
            "\tabcdefgabcdefg\n\tabcdefgabcdefg\n", StringFormatter::formatContent( "abcdefgabcdefgabcdefgabcdefg", "\t", 14 ), 

            'StringFormatterTest::testFormatContent -> content is not formatted (#3)' );

        $this->assertSame( 
            "i9dei9ei3iic\napsd3i0dkoek\nde0mfofo\n", StringFormatter::formatContent( "i9dei9ei3iicapsd3i0dkoekde0mfofo", "", 12 ),

            'StringFormatterTest::testFormatContent -> content is not formatted (#4)' );

        $this->assertSame( 
            "123595028385\n02kc93kkd03m\nd93mdf9b83m\n", StringFormatter::formatContent( "12359502838502kc93kkd03md93mdf9b83m", "", 12 ),

            'StringFormatterTest::testFormatContent -> content is not formatted (#5)' );

        $this->assertSame( 
            "\n", StringFormatter::formatContent( "", "", 12 ),

            'StringFormatterTest::testFormatContent -> content is not formatted (#6)' );
    }
}

