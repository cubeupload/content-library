<?php namespace CubeUpload\ContentLibrary;

//use League\Flysystem;

class Library
{
    private $baseDir;
    private $defaultPermission = 0755;

    public function __construct( $baseDir = '.' )
    {
        $this->setBaseDir( $baseDir );
    }

    public function setBaseDir( $baseDir )
    {
        $testFile = rand() . ".dat";

        if( file_exists( $baseDir ) )
        {
            if( touch( $baseDir . '/' . $testFile ) )
            {
                unlink( $baseDir . '/' . $testFile );
                $this->baseDir = $baseDir;
            }
            else
                throw new \Exception( "Base directory {$baseDir} exists but no permission to write" ); 
        }
        else
            throw new \Exception( "Base directory {$baseDir} doesn't exist" );
    }

    public function getBaseDir()
    {
        return $this->baseDir;
    }

    private function getHashPath( $string )
    {
        return $this->baseDir . "/" . $this->getSplit( $string ) . "/" . "{$string}.dat";
    }

    private function getSplit( $string )
    {
        return "{$string[0]}/{$string[1]}/{$string[2]}";
    }

    private function makeHashFromFile( $path )
    {
        return md5_file( $path );
    }

    public function save( $path )
    {
        $hash = $this->makeHashFromFile( $path );

        $hashPath = $this->getHashPath( $hash );
        $savePath = $this->getBaseDir() . '/' . $this->getSplit( $hash );

        if( !file_exists( $savePath ) )
        {
            $oldumask = umask(0);
            mkdir( $savePath, $this->defaultPermission, true );
            umask( $oldumask );
        }


        copy( $path, $hashPath );
        return $hash;
    }

    public function load( $hash )
    {
        $hashPath = $this->getHashPath( $hash );
        
        if( file_exists( $hashPath ) )
            return file_get_contents( $hashPath );
        else
            throw new \Exception( "File hash {$hash} not found in library" );
    }

    public function exists( $hash )
    {
        $hashPath = $this->getHashPath( $hash );
        
        if( file_exists( $hashPath ) )
            return true;
        else
            return false;
    }
}