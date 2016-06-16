# CubeUpload Content Library
*A basic dedupled filing structure for images*

## About
We store a *lot* of images on cubeupload and it gets messy on the filesystem. With this library we only store a single copy of each uploaded image and use a cache to resolve the user's filename to the corresponding hash.

## Usage
Nice and easy...

```php
// Initialise the class, using /content as our library base directory.
$library = new CubeUpload\ContentLibrary\Library( '/content' );

// Feed the save() method a file path. The file will be copied to the library and the hash returned.
$hash = $library->save( $filePath );
// Save the resulting hash to a cache or database for later resolution

// Load the content of a hashed file
$content = $library->load( $hash );

// Check if a hash exists in the library
$exists = $library->exists( $hash );
```
