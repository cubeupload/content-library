<?php namespace CubeUpload\ContentLibrary;

use League\Flysystem\Filesystem;

class Library
{
    private $defaultPermission = 0755;
    private $filesystem;

    public function __construct( Filesystem $filesystem = null)
    {
        if( $filesystem != null)
            $this->setFilesystem( $filesystem );
    }
    
    public function setFilesystem( $filesystem )
    {
        $this->filesystem = $filesystem;
    }

    public function getFilesystem()
    {
        return $this->filesystem;
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

    public function write( $path )
    {
		if( !file_exists( $path ) )
			throw new \Exception( "File {$path} doesn't exist" );
		
        $hash = $this->makeHashFromFile( $path );
        $hashPath = $this->getHashPath( $hash );

        $this->filesystem->write( $hashPath, file_get_contents($path) );
        return $hash;
    }

    public function read( $hash )
    {
        $hashPath = $this->getHashPath( $hash );
        
        if( $this->filesystem->has( $hashPath ) )
            return $this->filesystem->read( $hashPath );
        else
            throw new \Exception( "File hash {$hash} not found in library" );
    }

    public function has( $hash )
    {
        $hashPath = $this->getHashPath( $hash );
        
        if( $this->filesystem->has( $hashPath ) )
            return true;
        else
            return false;
    }

    public function delete( $hash )
    {
        $hashPath = $this->getHashPath( $hash );

        if( $this->filesystem->has( $hashPath ))
            $this->filesystem->delete( $hashPath );
        else
            throw new \Exception( "Hash {$hash} wasn't found" );
    }
}