<?php

namespace Security\Sentinel\System\Inspectors;

class FileInspector
{

    private $config;
    private $registered = [];
    private $unregistered = [];

    private $index = -1;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getRegistered()
    {
        return $this->registered;
    }

    public function getUnregistered()
    {
        return $this->unregistered;
    }

    public function registerFile(\SplFileInfo $file)
    {
        if ($file->isFile())
        {
            $fpath = $file->getRealPath();
            if (!in_array($file->getFilename(), $this->config['ignored']['files']))
            {
                $checksum = md5_file($fpath);
                $this->index += 1;
                $this->registered[] = (object) [
                    'index' => $this->index,
                    'file' => $fpath,
                    'checksum' => $checksum
                ];
            }
        } elseif ($file->isDir())
        {
            if (!in_array($file->getPathname(), $this->config['ignored']['directories']))
            {
                $files = new \DirectoryIterator($file->getPathname());

                while ($files->valid())
                {
                    $name = $files->getFilename();

                    if ($name != '.' AND $name != '..')
                    {
                        $fp = new \SplFileInfo($files->getRealPath());
                        $this->registerFile($fp);
                    }

                    $files->next();
                }
            }
        } else
        {
            return;
        }
    }

    public function findUnregisteredFile(\SplFileInfo $file, array $checksums)
    {
        if ($file->isFile())
        {
            $fpath = $file->getRealPath();
            if (!in_array($file->getFilename(), $this->config['ignored']['files']) && !$this->inList($fpath, $checksums))
            {
                $checksum = md5_file($fpath);
                $this->index += 1;
                $this->unregistered[] = (object) [
                    'index' => $this->index,
                    'file' => $fpath,
                    'checksum' => $checksum
                ];
            }
        } elseif ($file->isDir())
        {
            if (!in_array($file->getPathname(), $this->config['ignored']['directories']))
            {
                $files = new \DirectoryIterator($file->getPathname());
                while ($files->valid())
                {
                    $name = $files->getFilename();
                    if ($name != '.' AND $name != '..')
                    {
                        $fp = new \SplFileInfo($files->getRealPath());
                        $this->findUnregisteredFile($fp, $checksums);
                    }
                    $files->next();
                }
            }
        } else
        {
            return;
        }
    }

    public function findRegisteredFiles()
    {
        $checkdir = $this->config['inspection']['checksum_storage']['directory'];
        $checkfile = $checkdir . DIRECTORY_SEPARATOR . 'registered.ser';
        $registered = [];

        if (realpath($checkfile))
        {
            $registered = unserialize(file_get_contents($checkfile));
        }

        return $registered;
    }

    public function save($file, $data)
    {
        $outdir = $this->config['inspection']['checksum_storage']['directory'];
        $outfile = $outdir . DIRECTORY_SEPARATOR . $file;
        file_put_contents($outfile, serialize($data), LOCK_EX);
        return true;
    }

    public function inList($file, $list)
    {
        foreach ($list as $item)
        {
            if ($item->file == $file)
            {
                return true;
            }
        }
        return false;
    }
}
