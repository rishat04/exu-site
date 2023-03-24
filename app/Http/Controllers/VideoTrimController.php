<?php

namespace App\Http\Controllers;

use App\Helpers\VideoHelper;
use App\Jobs\VideoTrim;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Http\Request;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

/**
 * Summary of VideoTrimController
 */
class VideoTrimController extends Controller
{
    /**
     * Summary of __invoke
     * @param Request $request
     * @return bool
     */
    public function __invoke(Request $request): bool|array
    {
        $url = $request->input('url');
        $start = $request->input('s');
        $duration = $request->input('d');
        $hash = VideoHelper::makeHash($request);

        // VideoTrim::dispatch($request);
        FFMpeg::openUrl($url)->export()->toDisk('videos')
        ->addFilter('-ss', TimeCode::fromSeconds($start))
        ->addFilter('-t', TimeCode::fromSeconds($duration))
        ->save($hash . ".mp4");

        return ['filename' => $hash];
    }
}
