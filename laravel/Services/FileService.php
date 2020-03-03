<?php

namespace App\Http\Services;

use Request;

class FileService
{

    public function uploadFile($request, $inputname, $destinationPath = 'unsorted')
    {
        if ($request->hasFile($inputname)) {
            $destinationPath = "uploads/" . $destinationPath;
            $extension = $request->file($inputname)->getClientOriginalExtension(); // getting image extension
            $fileName = uniqid('file_', true) . '.' . $extension; // renameing image
            $moveresult = $request->file($inputname)->move($destinationPath, $fileName); // uploading file to given path
            $filepath =  $destinationPath . "/" . $fileName;
            return $filepath;
            // dd($destinationPath);
//      return Storage::putFile($destinationPath, $request->file($inputname));
        } else {
            return false;
        }
    }

 
}
