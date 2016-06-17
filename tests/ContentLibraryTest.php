<?php

    use CubeUpload\ContentLibrary\Library;
    use PHPUnit\Framework\TestCase;
    
    class ContentLibraryTest extends TestCase
    {     

        private static $testfile = './tests/fixtures/testimage.jpg';
        private static $libraryDir = './tests/fixtures/content';
        private static $library;

        public static function setUpBeforeClass()
        {
            mkdir( self::$libraryDir );
            self::$library = new Library( self::$libraryDir );
        }

        public static function tearDownAfterClass()
        {
            self::$library = null;
            self::delTree( self::$libraryDir );
        }

        // http://php.net/manual/en/function.rmdir.php#110489
        public static function delTree($dir)
        {
            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir); 
        }

        public function testClassExists()
        {
            $this->assertInstanceOf( CubeUpload\ContentLibrary\Library::class, self::$library );
        }

        public function testSaveFile()
        {
            $sampleHash = md5_file( self::$testfile );

            $generatedHash = self::$library->save( self::$testfile );

            $this->assertEquals( $sampleHash, $generatedHash );
        }
		
		/**
		 * @expectedException Exception
		 */
		public function testSaveInvalidFile()
		{
			self::$library->save( 'invalid_file' );
		}

        public function testLoadFile()
        {
            $sampleHash = md5_file( self::$testfile );

            $libraryContent = self::$library->load( $sampleHash );
            $testContent = file_get_contents( self::$testfile );

            $this->assertEquals( $libraryContent, $testContent );

        }

        public function testFileExists()
        {
            $sampleHash = md5_file( self::$testfile );

            $this->assertTrue( self::$library->exists( $sampleHash ) );
        }

        public function testFileNotExists()
        {
            $fakehash = "12345";

            $this->assertFalse( self::$library->exists( $fakehash ) );
        }
    }