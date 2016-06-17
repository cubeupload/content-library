<?php namespace CubeUpload\ContentLibrary;

use League\Flysystem\Filesystem;

class Library
{
    private $defaultPermission = 0755;
    private $filesystem;

    public function __construct( Filesystem $filesystem )
    {
        $this->setFilesystem( $filesystem );
    }
    
    public function setFilesystem( $filesystem )
    {
        $this->filesystem = $filesystem;
    }

    private function getHashPath( $string )
    {
        return "/" . $this->getSplit( $string ) . "/" . "{$string}.dat";
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