<?php namespace CubeUpload\ContentLibrary;

use League\Flysystem\Filesystem;

class Library
{
    private $baseDir;
    private $defaultPermission = 0755;
    private $filesystem;

    public function __construct( Filesystem $filesystem, $baseDir = '.' )
    {
        $this->setFilesystem( $filesystem );
        $this->setBaseDir( $baseDir );
    }
    
    public function setFilesystem( $filesystem )
    {
        $this->filesystem = $filesystem;
    }

    public function setBaseDir( $baseDir )
    {
        $testFile = rand() . ".dat";

        if( $this->filesystem->write( $baseDir . '/' . $testFile, "test" ) )
        {
            $this->filesystem->delete( $baseDir . '/' . $testFile );
            $this->baseDir = $baseDir;
        }
        else
            throw new \Exception( "BaseDir {$baseDir} exists but no permission to write" ); 
    }

    public function getFilesystem()
    {
        return $this->filesystem;
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
		if( !file_exists( $path ) )
			throw new \Exception( "File {$path} doesn't exist" );
		
        $hash = $this->makeHashFromFile( $path );

        $hashPath = $this->getHashPath( $hash );
        $savePath = $this->getBaseDir() . '/' . $this->getSplit( $hash );

        /*
        if( !$this->filesystem->has( $savePath ) )
        {
            $oldumask = umask(0);
            mkdir( $savePath, $this->defaultPermission, true );
            umask( $oldumask );
        }
        */

        $this->filesystem->write( $hashPath, file_get_contents($path) );
        return $hash;
    }

    public function load( $hash )
    {
        $hashPath = $this->getHashPath( $hash );
        
        if( $this->filesystem->has( $hashPath ) )
            return $this->filesystem->read( $hashPath );
        else
            throw new \Exception( "File hash {$hash} not found in library" );
    }

    public function exists( $hash )
    {
        $hashPath = $this->getHashPath( $hash );
        
        if( $this->filesystem->has( $hashPath ) )
            return true;
        else
            return false;
    }
}