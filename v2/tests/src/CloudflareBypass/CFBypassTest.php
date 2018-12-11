<?php
use PHPUnit\Framework\TestCase;

use CloudflareBypass\CFBypass;

/**
 * CF Bypass utility tests
 * -> testGetJschlVc        - Checks if "getJschlVC" extracts correct jschl vc value from iuam doc.
 * -> testGetJschlPass      - Checks if "getJschlPass" extracts correct pass value from iuam doc.
 * -> testGetJschlAnswer    - Checks if "getJschlAnswer" extracts correct jschl answer value from iuam doc. 
 */
class CFBypassTest extends TestCase
{
    protected static $bypass_files = [];

    /**
     * Setup before test case class
     * @return void.
     */
    public static function setUpBeforeClass() {
        $test_files_dir = implode( DIRECTORY_SEPARATOR, [ __DIR__, '..', '..', 'files' ] );

        if ($dir_handle = opendir( $test_files_dir )) {
            while (false !== ($filename = readdir( $dir_handle ))) {
                // do not use use special directories.
                if ($filename === '.' || $filename === '..')
                    continue;

                self::$bypass_files[] = [
                    'name'          => $filename,
                    'content'       => file_get_contents( $test_files_dir . DIRECTORY_SEPARATOR . $filename )
                ];
            }
        
            closedir( $dir_handle );
        }
    }


    /**
     * Tests jschl vc getter
     * @return void
     */
    public function testGetJschlVc() {
        $answers = [
            'cfbypass1.html' => '91e0ad10cd7cef20bbdd3a0413921509',
            'cfbypass2.html' => '9d49e9c975355165894d03f27226effe',
            'cfbypass3.html' => '931aafe1deb01a2a49e458d9e2762314',
            'cfbypass4.html' => '689a32cc75c5857a4e037bbdf96962d9',
            'cfbypass5.html' => 'a486343c00dce8c1ce7401fdf3fe1606'
        ];

        foreach (self::$bypass_files as $file) {
            $this->assertSame( CFBypass::getJschlVC( $file['content'] ), $answers[$file['name']], 
                sprintf( 'jschl vc should match for %s', $file['name'] ) );
        }
    }


    /**
     * Test jschl pass getter
     * @return void
     */
    public function testGetJschlPass() {
        $answers = [
            'cfbypass1.html' => '1543248141.789-bvBJN6wcyM',
            'cfbypass2.html' => '1543235964.146-OZt9m8eYx8',
            'cfbypass3.html' => '1543248270.485-G0SbnXYGYA',
            'cfbypass4.html' => '1543246055.961-2saHtnzUVO',
            'cfbypass5.html' => '1543248348.791-RrZS6BYv32'
        ];

        foreach (self::$bypass_files as $file) {
            $this->assertSame( CFBypass::getJschlPass( $file['content'] ), $answers[$file['name']], 
                sprintf( 'pass should match for %s', $file['name'] ) );
        }
    }


    /**
     * Test jschl answer getter
     * @return void
     */
    public function testGetJschlAnswer() {
        $answers = [
            'cfbypass1.html' => -5.0613261468,
            'cfbypass2.html' => 29.0099192745,
            'cfbypass3.html' => -1.0155658241,
            'cfbypass4.html' => 3.3229504215,
            'cfbypass5.html' => -8.5415482744
        ];

        foreach (self::$bypass_files as $file) {
            $this->assertSame( CFBypass::getJschlAnswer( $file['content'] ), $answers[$file['name']], 
                sprintf( 'jschl answer should match for %s', $file['name'] ) );
        }
    }
}