<?php

    use CubeUpload\ContentLibrary\Library;
    use PHPUnit\Framework\TestCase;
    
    use League\Flysystem\Filesystem;
    use League\Flysystem\Memory\MemoryAdapter;
    use League\Flysystem\Adapter\Local;
    
    class AdaptersTest extends TestCase
    {
        private static $testfile = './tests/fixtures/testimage.jpg';
        private static $libraryDir = './tests/fixtures/content';
        private static $library;

        public static function setUpBeforeClass()
        {
            mkdir( self::$libraryDir );
            $filesystem = new Filesystem( new Local(self::$libraryDir) );
            self::$library = new Library( $filesystem );
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

        public function testMemoryAdapter()
        {
            $testHash = md5_file( self::$testfile );
            $testContent = file_get_contents( self::$testfile );
            
            $fs = new FileSystem( new MemoryAdapter () );
            $lib = new Library( $fs );
            $lib->save( self::$testfile );
            
            $this->assertTrue( $lib->exists( $testHash ));
            $this->assertEquals( $testContent, $lib->load( $testHash ));
        }
        
        public function testLocalAdapter()
        {
            
        }
    }