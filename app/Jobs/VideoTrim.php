<?php

namespace App\Jobs;

use App\Helpers\VideoHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use FFMpeg\Coordinate\TimeCode;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class VideoTrim implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $url;
    private $start;
    private $duration;
    private $hashed; 

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->url = $request->input('url');
        $this->start = $request->input('s');
        $this->duration = $request->input('d');
        $this->hashed = VideoHelper::makeHash($request);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        FFMpeg::openUrl($this->url)->export()->toDisk('videos')
        ->addFilter('-ss', TimeCode::fromSeconds($this->start))
        ->addFilter('-t', TimeCode::fromSeconds($this->duration))
        ->save($this->hashed . ".mp4");
    }
}
