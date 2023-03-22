<?php

namespace App\Http\Controllers;

use App\Helpers\VideoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoFormatsController extends Controller
{
    public function __invoke(Request $request) {
        $videoId = $request->input('v');
        return VideoHelper::getFormats($videoId);
    }
}
