<?php

namespace Security\Sentinel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Security\Sentinel\System\FilesystemSentinel;

class FilesystemController extends Controller
{
    private $sentinel;

    public function __construct(FilesystemSentinel $sentinel)
    {
        $this->sentinel = $sentinel;
    }

    public function index()
    {
        return view('sentinel::filesystem');
    }

    public function register()
    {
        $this->sentinel->registerFiles();
        $result = $this->sentinel->getRegistered();
        return response()->json($result);
    }

    public function unregistered()
    {
        $this->sentinel->findUnregisteredFiles();
        $result = $this->sentinel->getUnregistered();
        return response()->json($result);
    }

    public function modified()
    {
        $this->sentinel->findModifiedFiles();
        $result = $this->sentinel->getModified();
        return response()->json($result);
    }

    public function deleted()
    {
        $this->sentinel->findDeletedFiles();
        $result = $this->sentinel->getDeleted();
        return response()->json($result);
    }

    public function backup()
    {
        $backupresult = $this->sentinel->backupFiles();
        $result = ['result' => $backupresult];
        return response()->json($result);
    }

    public function updateone(Request $request)
    {
        $id = $request->input('id');
        $updateresult = $this->sentinel->updateChecksum($id);
        $output = ['result' => $updateresult];
        return response()->json($output);
    }

}