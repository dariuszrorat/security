<?php

namespace Security\Sentinel\System\Archivers;

class ZipArchiver
{

    public function __construct()
    {
        
    }
    
    public function compress($source, $destination)
    {
        if (!extension_loaded('zip') || empty($source))
        {
            return false;
        }

        if (file_exists($destination))
        {
            unlink($destination);
        }

        $zip = new \ZipArchive();

        if (!$zip->open($destination, \ZipArchive::CREATE))
        {
            return false;
        }

        foreach ($source as $src)
        {
            $file = $src->file;
            $zip->addFile($file);
        }
        return $zip->close();
    }
    
    public function extract($source, $destination)
    {
        $zip = new \ZipArchive;
        if ($zip->open($source) === TRUE)
        {
           $zip->extractTo($destination);
           $zip->close();
           return true;
        }
        return false;
    }

}
