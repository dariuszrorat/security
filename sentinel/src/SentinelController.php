<?php

namespace Security\Sentinel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SentinelController extends Controller
{

    public function index()
    {
        return view('sentinel::sentinel');
    }

    public function register()
    {
        $sentinel = new Sentinel;
        $sentinel->registerFiles();
        $registered = $sentinel->getRegistered();
        return response()->json($registered);
    }

    public function unregistered()
    {
        $sentinel = new Sentinel;
        $sentinel->findUnregisteredFiles();
        $unregistered = $sentinel->getUnregistered();
        return response()->json($unregistered);
    }

    public function modified()
    {
        $sentinel = new Sentinel;
        $sentinel->findModifiedFiles();
        $modified = $sentinel->getModified();
        return response()->json($modified);
    }

    public function deleted()
    {
        $sentinel = new Sentinel;
        $sentinel->findDeletedFiles();
        $deleted = $sentinel->getDeleted();
        return response()->json($deleted);
    }

    public function backup()
    {
        $sentinel = new Sentinel;
        $result = $sentinel->backupFiles();
        $result = array('result' => $result);
        return response()->json($result);
    }

    public function updateone(Request $request)
    {
        $id = $request->input('id');
        $sentinel = new Sentinel;
        $result = $sentinel->updateChecksum($id);
        $output = ['result' => $result];
        return response()->json($output);
    }

}