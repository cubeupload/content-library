<?php

    use CubeUpload\ContentLibrary\Library;
    use League\Flysystem\Filesystem;
    use League\Flysystem\Memory\MemoryAdapter;
    use PHPUnit\Framework\TestCase;
    
    class ContentLibraryTest extends TestCase
    {    
        private static $testfile = './tests/fixtures/testimage.jpg';
        private static $libraryDir = './tests/fixtures/content';
        private static $testhash;
        private static $library;

        public static function setUpBeforeClass()
        {
            $filesystem = new Filesystem( new MemoryAdapter() );
            self::$library = new Library( $filesystem );
            self::$testhash = md5_file( self::$testfile );
        }

        public static function tearDownAfterClass()
        {
            self::$library = null;
        }

        public function testClassExists()
        {
            $this->assertInstanceOf( CubeUpload\ContentLibrary\Library::class, self::$library );
        }

        public function testSaveFile()
        {
            $sampleHash = md5_file( self::$testfile );

            $generatedHash = self::$library->write( self::$testfile );

            $this->assertEquals( $sampleHash, $generatedHash );
        }

        /*
         * @depends testSaveFile
         */
        public function testLoadFile()
        {
            $sampleHash = md5_file( self::$testfile );

            $libraryContent = self::$library->read( $sampleHash );
            $testContent = file_get_contents( self::$testfile );

            $this->assertEquals( $libraryContent, $testContent );
        }

        /*
         * @depends testSaveFile
         */
        public function testFileExists()
        {
            $sampleHash = md5_file( self::$testfile );

            $this->assertTrue( self::$library->has( $sampleHash ) );
        }

        /*
         * @depends testSaveFile
         */
        public function testDeleteFile()
        {
            self::$library->delete( self::$testhash );

            $this->assertFalse( self::$library->has( self::$testhash ) );
        }

        public function testFileNotExists()
        {
            $fakehash = "12345";

            $this->assertFalse( self::$library->has( $fakehash ) );
        }

        /**
		 * @expectedException Exception
		 */
		public function testSaveInvalidFile()
		{
			self::$library->write( 'invalid_file' );
		}
    }