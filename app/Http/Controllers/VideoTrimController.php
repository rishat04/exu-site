<?php

namespace App\Http\Controllers;

use App\Helpers\VideoHelper;
use Illuminate\Http\Request;

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
    public function __invoke(Request $request): bool | array
    {
        return VideoHelper::getFormats('g2cYRsWcB54');
    }

    private function trimVideo(): bool
    {
        return true;
    }
}
