<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class VideoHelper {


    public static function getFormats($videoId) {
        $response = self::getDetails($videoId);
        $elements = [];
        $quaityExists = [];

        foreach ($response['streamingData']['formats'] as $video) {
            $type = explode(';', $video['mimeType'])[0];
            $videoFormat = explode('/', $type)[1];
            $elements['video']['withAudio'][] = [
                'url' => $video['url'],
                'format' => $videoFormat,
                'quality' => $video['qualityLabel']
            ];
        }
        foreach($response['streamingData']['adaptiveFormats'] as $video) {
            $type = explode(';', $video['mimeType'])[0];
            $mediaFormat = explode('/', $video['mimeType'])[0];
            $videoFormat = explode('/', $type)[1];

            if ($mediaFormat != 'video') continue;
            if ($videoFormat != 'mp4') continue;

            $quality = $video['qualityLabel'];
            if (in_array($quality, $quaityExists)) continue;

            $quaityExists[] = $video['qualityLabel'];

            $elements['video']['withoutAudio'][] = [
                'url' => $video['url'],
                'format' => $videoFormat,
                'quality' => $video['qualityLabel']
            ];
        }
        
        return $elements;
    }

    public static function getDetails($videoId)
    {
        $json = '{"context": {"client": {"hl": "en","clientName": "WEB","clientVersion": "2.20210721.00.00","clientFormFactor": "UNKNOWN_FORM_FACTOR","clientScreen": "WATCH","mainAppWebInfo": {"graftUrl": "/watch?v=' . $videoId . '"}},"user": {"lockedSafetyMode": false},"request": {"useSsl": true,"internalExperimentFlags": [],"consistencyTokenJars": []}},"videoId": "' . $videoId . '","playbackContext": {"contentPlaybackContext": {"vis": 0,"splay": false,"autoCaptionsDefaultOn": false,"autonavState": "STATE_NONE","html5Preference": "HTML5_PREF_WANTS","lactMilliseconds": "-1"}},"racyCheckOk": false,  "contentCheckOk": false}';
        $response = Http::post('https://www.youtube.com/youtubei/v1/player?key=AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8', json_decode($json, true));        
        return $response->json();
    }

}