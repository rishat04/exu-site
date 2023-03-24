<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class VideoDownloadController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $hash_name = $request->input('filename') . '.mp4';
        $name_with_times = base64_decode($hash_name);
        $name = explode('_', $name_with_times)[0] . '.mp4';
        
        $headers = [
            'Content-Type' => 'video/mp4'
        ];

        return Storage::download($hash_name, $name, $headers);
    }
}
