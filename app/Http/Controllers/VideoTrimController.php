<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Support\Facades\Storage;
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
        $url        = $request->input('url');
        $start      = $request->input('s');
        $duration   = $request->input('d');
        $name       = $request->input('n');

        $hash_name = base64_encode($name . '_' . $start . $duration);

        $this->trimVideo($url, $start, $duration, $hash_name);

        return ['filename' => $hash_name];
    }

    private function trimVideo($url, $start, $duration, $name): bool|string
    {
        FFMpeg::openUrl($url)->export()->toDisk('videos')
        ->addFilter('-ss', TimeCode::fromSeconds($start))
        ->addFilter('-t', TimeCode::fromSeconds($duration))
        ->save("{$name}.mp4");

        return true;
    }
}
