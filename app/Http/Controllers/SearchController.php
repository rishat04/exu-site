<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request) {
        $query = $request->input('query');
        $token = $request->input("nextPage");
        $filterParams = [
            'views' => [
                'from' => (int) $request->input('viewsFrom'),
                'to'   => (int) $request->input('viewsTo')
            ],
            'date' => [
                'from' => (int) $request->input('dateFrom'),
                'to'   => (int) $request->input('dateTo'),
            ],
            'subs' => [
                'from' => (int) $request->input('subsFrom'),
                'to'   => (int) $request->input('subsTo')
            ]
        ];

        $times = [
            '',             #nothing
            'EgQIARAB', #last hour
            'EgQIAhAB', #today
            'EgQIAxAB', #last week
            'EgQIBBAB', #last month
            'EgQIBRAB', #last year
        ];

        $time = $times[0];
        [$response, $token] = $this->search($query, $time, $token);

        $filtered = $this->filter($response, $filterParams);  

        $response = $this->getAdvancedDetails($response);

        $filtered = $this->filter($response, $filterParams);

        return response()->json(['videos' => $filtered, 'nextPage' => $token]);       
        
    }

    private function getAdvancedDetails($videos) {
        $response = [];
        foreach($videos as $video) {
            $response[] = $this->getVideoDetails($video['videoId']);
        }

        return $response;
    }

    private function filter($videos, $filterParams)
    {
        $response = [];

        foreach ($videos as $video)
        {
            $accepted = true;
            foreach ($filterParams as $key => $value)
            {

                $filter = $filterParams[$key];

                if ($filter['from'] and $video[$key] <= $filter['from'] or $filter['to'] and $video[$key] >= $filter['to'])
                {
                    $accepted = false;
                }

                if (!$accepted) break;
            }

            if ($accepted) $response[] = $video;
        }

        return $response;
    }

    private function getVideoDetails($videoId) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://www.youtube.com/youtubei/v1/player?key=AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{  "context": {    "client": {      "hl": "en",      "clientName": "WEB",      "clientVersion": "2.20210721.00.00",      "clientFormFactor": "UNKNOWN_FORM_FACTOR",   "clientScreen": "WATCH",      "mainAppWebInfo": {        "graftUrl": "/watch?v='.$videoId.'",           }    },    "user": {      "lockedSafetyMode": false    },    "request": {      "useSsl": true,      "internalExperimentFlags": [],      "consistencyTokenJars": []    }  },  "videoId": "'.$videoId.'",  "playbackContext": {    "contentPlaybackContext": {        "vis": 0,      "splay": false,      "autoCaptionsDefaultOn": false,      "autonavState": "STATE_NONE",      "html5Preference": "HTML5_PREF_WANTS",      "lactMilliseconds": "-1"    }  },  "racyCheckOk": false,  "contentCheckOk": false}');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $json = json_decode($result);

        $response = [
            'title' => $json->videoDetails->title,
            'videoId' => $json->videoDetails->videoId,
            'duration' => $json->videoDetails->lengthSeconds,
            'keywords' => $json->videoDetails->keywords ?? null,
            'channelId' => $json->videoDetails->channelId,
            'channelName' => $json->microformat->playerMicroformatRenderer->ownerChannelName,
            'thumbnail' => $json->videoDetails->thumbnail->thumbnails[3]->url,
            'views' => $json->videoDetails->viewCount,
            'author' => $json->videoDetails->author,
            'date' => $json->microformat->playerMicroformatRenderer->uploadDate,
        ];

        return $response;
    }

    private function search($query, $time, $token) {
        $response = [];

        $raw_data = '{"context":{"client":{"clientName":"WEB","clientVersion":"2.9999099"}}' . ($token ? ',"continuation":"' . $token . '"' : '') . ',"query":"' . $query . '"' . ($time ? ',"params":"' . $time . '"' : '') . '}';

        $opts = [
            "http" => [
                "method" => "POST",
                "header" => "Content-Type: application/json",
                "content" => $raw_data,
            ]
        ];

        $ui_key = 'AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8';

        $json = $this->get_json('https://www.youtube.com/youtubei/v1/search?key=' . $ui_key, $opts);

        $items = ($token ? $json['onResponseReceivedCommands'][0]['appendContinuationItemsAction']['continuationItems'] : $json['contents']['twoColumnSearchResultsRenderer']['primaryContents']['sectionListRenderer']['contents'])[0]['itemSectionRenderer']['contents'];

        $nextPageToken = $json['contents']['twoColumnSearchResultsRenderer']['primaryContents']['sectionListRenderer']['contents'][1]['continuationItemRenderer']['continuationEndpoint']['continuationCommand']['token'] ?? null ;
        $nextPageToken = $nextPageToken ?? ($json['onResponseReceivedCommands'][0]['appendContinuationItemsAction']['continuationItems'][1]['continuationItemRenderer']['continuationEndpoint']['continuationCommand']['token'] ?? null);

        foreach($items as $item) {
            if (!$item = $item['videoRenderer'] ?? null or !$views = $item['viewCountText']['simpleText'] ?? null)
                continue;

            $response[] = [
                'channelId' => $item['ownerText']['runs'][0]['navigationEndpoint']['browseEndpoint']['browseId'],
                'videoId' => $item['videoId'],
                'title' => $item['title']['runs'][0]['text'],
                'thumbnails' => $item['thumbnail']['thumbnails'][0]['url'],
                'channelTitle' => $item['ownerText']['runs'][0]['text'],
                'duration' => ($item['lengthText'] ?? false) ? $this->getIntFromDuration($item['lengthText']['simpleText']) : 0,
                'views' => $this->getIntFromViewCount($views),
                'viewText' => $item['viewCountText']['simpleText'],
            ];
        }

        return [$response, $nextPageToken];
    }

    private function get_json($url, $opts) {

        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);

        return json_decode($result, true);
    }

    function getIntFromViewCount($viewCount) {
        if ($viewCount === 'No views') {
            $viewCount = 0;
        } else {
            foreach([',', ' views', 'view'] as $toRemove) {
                $viewCount = str_replace($toRemove, '', $viewCount);
            }
        } // don't know if the 1 view case is useful
        $viewCount = intval($viewCount);
        return $viewCount;
    }

    function getIntFromDuration($timeStr) {
        $isNegative = $timeStr[0] === '-';
        if ($isNegative) {
            $timeStr = substr($timeStr, 1);
        }
        $format = 'j:H:i:s';
        $timeParts = explode(':', $timeStr);
        $timePartsCount = count($timeParts);
        $minutes = $timeParts[$timePartsCount - 2];
        $timeParts[$timePartsCount - 2] = strlen($minutes) == 1 ? "0$minutes" : $minutes;
        $timeStr = implode(':', $timeParts);
        for ($timePartsIndex = 0; $timePartsIndex < 4 - $timePartsCount; $timePartsIndex++) {
            $timeStr = "00:$timeStr";
        }
        while (date_parse_from_format($format, $timeStr) === false) {
            $format = substr($format, 2);
        }
        $timeComponents = date_parse_from_format($format, $timeStr);
        $timeInt = $timeComponents['day'] * (3600 * 24) +
                   $timeComponents['hour'] * 3600 +
                   $timeComponents['minute'] * 60 +
                   $timeComponents['second'];
        return ($isNegative ? -1 : 1) * $timeInt;
    }
}
