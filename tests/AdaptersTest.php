<?php

    use CubeUpload\ContentLibrary\Library;
    use PHPUnit\Framework\TestCase;
    
    use League\Flysystem\Filesystem;
    use League\Flysystem\Memory\MemoryAdapter;
    use League\Flysystem\Adapter\Local;

    class AdaptersTest extends TestCase
    {                                                                                                                                                                                                           private static $testfile = './tests/fixtures/testimage.jpg';
        private static $testhash;
        private static $testcontent;
        private static $libraryDir = './tests/fixtures/content';
        private static $library;

        public static function setUpBeforeClass()
        {
            self::$testhash = md5_file( self::$testfile );
            self::$testcontent = file_get_contents( self::$testfile );          
        }

        public function testMemoryAdapter()
        {          
            $fs = new FileSystem( new MemoryAdapter () );
            $lib = new Library( $fs );
            $lib->write( self::$testfile );
            
            $this->assertTrue( $lib->has( self::$testhash ));
            $this->assertEquals( self::$testcontent, $lib->read( self::$testhash ));
        }
        
        public function testLocalAdapter()
        {
            mkdir( self::$libraryDir );
            $fs = new Filesystem( new Local(self::$libraryDir) );
            
            $lib = new Library( $fs );
            $lib->write( self::$testfile );
            
            $this->assertTrue( $lib->has( self::$testhash ));
            $this->assertEquals( self::$testcontent, $lib->read( self::$testhash ));
            deltree( self::$libraryDir );
        }
    }