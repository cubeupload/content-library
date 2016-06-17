<?php

    use CubeUpload\ContentLibrary\Library;
    use PHPUnit\Framework\TestCase;
    
    use League\Flysystem\Filesystem;
    use League\Flysystem\Memory\MemoryAdapter;
    use League\Flysystem\Adapter\Local;
    
    class AdaptersTest extends TestCase
    {
        private static $testfile = './tests/fixtures/testimage.jpg';
        private static $testhash;
        private static $testcontent;
        private static $libraryDir = './tests/fixtures/content';
        private static $library;

        public static function setUpBeforeClass()
        {
            self::$testhash = md5_file( self::$testfile );
            self::$testcontent = file_get_contents( self::$testfile );
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
            $fs = new FileSystem( new MemoryAdapter () );
            $lib = new Library( $fs );
            $lib->save( self::$testfile );
            
            $this->assertTrue( $lib->exists( self::$testhash ));
            $this->assertEquals( self::$testcontent, $lib->load( self::$testhash ));
        }
        
        public function testLocalAdapter()
        {
            mkdir( self::$libraryDir );
            $fs = new Filesystem( new Local(self::$libraryDir) );
            
            $lib = new Library( $fs );
            $lib->save( self::$testfile );
            
            $this->assertTrue( $lib->exists( self::$testhash ));
            $this->assertEquals( self::$testcontent, $lib->load( self::$testhash ));
            self::delTree( self::$libraryDir );
        }
    }