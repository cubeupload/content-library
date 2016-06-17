<?php


// http://php.net/manual/en/function.rmdir.php#110489
function deltree($dir)
{
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? deltree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir); 
}