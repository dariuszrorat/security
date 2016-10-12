<?php

namespace Security\Sentinel;

use Illuminate\Support\Facades\Mail;

class Sentinel
{
    const NOTHING = 00;
    const QUARANTINE = 10;
    const REPAIR = 20;
    const DELETE = 30;

    private $config = [];
    private $registered = [];
    private $modified = [];
    private $unregistered = [];
    private $deleted = [];

    private $emailSubject;

    public function __construct()
    {
        $this->config = config('sentinel');
    }

    public function getRegistered()
    {
        return $this->registered;
    }

    public function getModified()
    {
        return $this->modified;
    }

    public function getUnregistered()
    {
        return $this->unregistered;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function registerFiles()
    {
        $directories = $this->config['directories']['scanned'];
        $inspector = new FilesystemInspector($this->config);

        foreach ($directories as $dir)
        {
            $fileinfo = new \SplFileInfo($dir);
            $inspector->registerFile($fileinfo);
        }
        $this->registered = $inspector->getRegistered();
        if (!empty($this->registered))
        {
            return $inspector->save('registered.ser', $this->registered);
        }
        return false;
    }

    public function findModifiedFiles()
    {
        $inspector = new FilesystemInspector($this->config);
        $registered = $inspector->findRegisteredFiles();

        foreach ($registered as $item)
        {
            $file = $item->file;

            if (realpath($file))
            {
                $index = $item->index;
                $checksum = $item->checksum;
                $fchecksum = md5_file($file);
                if ($checksum !== $fchecksum)
                {
                    $this->modified[] = (object) [
                        'index' => $index,
                        'file' => $file,
                        'original_checksum' => $checksum,
                        'new_checksum' => $fchecksum
                    ];
                }
            }
        }
        if (!empty($this->modified))
        {
            $this->runAutoresponder('List of modified files', $this->modified, true);
            return true;
        }

        return false;

    }

    public function findUnregisteredFiles()
    {
        $inspector = new FilesystemInspector($this->config);
        $registered = $inspector->findRegisteredFiles();
        $directories = $this->config['directories']['scanned'];

        foreach ($directories as $dir)
        {
            $fileinfo = new \SplFileInfo($dir);
            $inspector->findUnregisteredFile($fileinfo, $registered);
        }

        $this->unregistered = $inspector->getUnregistered();
        if (!empty($this->unregistered))
        {
            $result = $inspector->save('unregistered.ser', $this->unregistered);
            $this->runAutoresponder('List of unregistered files', $this->unregistered);
            return $result;
        }
        return false;
    }

    public function findDeletedFiles()
    {
        $inspector = new FilesystemInspector($this->config);
        $registered = $inspector->findRegisteredFiles();
        $directories = $this->config['directories']['scanned'];

        foreach ($registered as $item)
        {
            if (!realpath($item->file))
            {
                $this->deleted[] = $item;
            }
        }
        if (!empty($this->deleted))
        {
            $result = $inspector->save('deleted.ser', $this->deleted);
            $this->runAutoresponder('List of deleted files', $this->deleted);
            return $result;
        }
        return;
    }

    public function updateChecksum($index)
    {
        $inspector = new FilesystemInspector($this->config);
        $registered = $inspector->findRegisteredFiles();

        if (!empty($registered))
        {
            $file = $registered[$index]->file;
            $registered[$index]->checksum = md5_file($file);
            return $inspector->save('registered.ser', $registered);
        }
        return false;
    }

    public function backupFiles()
    {
        $inspector = new FilesystemInspector($this->config);
        $registered = $inspector->findRegisteredFiles();
        if (!empty($registered))
        {
            $backupdir = $this->config['directories']['backup'];
            $type = $this->config['compression']['type'];
            $archiver = new ZipArchiver;
            $dest = sprintf('%s.zip', join(DIRECTORY_SEPARATOR, [$backupdir, date('Y_m_d')]));
            $result = $archiver->compress($registered, $dest);
            return $result;
        }
        return false;
    }

    protected function runAutoresponder($title, $data, $modified = false)
    {
            $enable_autoresponder = $this->config['autoresponder']['enabled'];
            if ($enable_autoresponder === true)
            {
                $driver = $this->config['autoresponder']['driver'];
                $results = "";
                foreach ($data as $item)
                {
                    if ($modified === true)
                    {
                        $results .= "<P><B>File: </B>" . $item->file
                          . "<BR><B>Original checksum: </B>" . $item->original_checksum
                          . "<BR><B>New checksum: </B>" . $item->new_checksum
                          . "</P>";
                    }
                    else
                    {
                        $results .= "<P><B>File: </B>" . $item->file
                          . "<BR><B>Checksum: </B>" . $item->checksum
                          . "</P>";
                    }
                }

                $data = [
                    'title' => $title,
                    'results' => $results
                    ];

                $project_name = $this->config['autoresponder']['project_name'];
                $this->emailSubject = $title . ' in project: ' . $project_name;

                Mail::send('sentinel::autoresponder.email.alert', $data, function ($message)
                {
                   $message->from($this->config['autoresponder']['email']['sender'], 'Laravel Sentinel');
                   $message->to($this->config['autoresponder']['email']['recipient'])
                   ->subject($this->emailSubject);
                });
            }

    }

}

// Sentinel
