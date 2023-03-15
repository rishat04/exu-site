<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrimmerController extends Controller
{
    /*public function trim(Request $request) {

        Storage::disk('local')->makeDirectory('test');

        return response()->json([
            'status' => 'successfuly'
        ]);
    }*/

    public function __invoke()
    {

    }

    private function trimVideo(): bool
    {
        return true;
    }
}
