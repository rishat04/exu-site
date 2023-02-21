<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request) {
        $query = $request->input('query');
        $url = 'https://www.youtube.com/results?search_query=' . $query;

        // $times = [
        //     '',             #nothing
        //     '&sp=EgQIARAB', #last hour
        //     '&sp=EgQIAhAB', #today
        //     '&sp=EgQIAxAB', #last week
        //     '&sp=EgQIBBAB', #last month
        //     '&sp=EgQIBRAB', #last year
        // ];

        $times = [
            '',             #nothing
            'EgQIARAB', #last hour
            'EgQIAhAB', #today
            'EgQIAxAB', #last week
            'EgQIBBAB', #last month
            'EgQIBRAB', #last year
        ];

        $data = [];

        $time = $times[0];

        // foreach($times as $time) {
            $continuationToken = '';
            $is_first = true;   
            $count = 0;
            while($continuationToken or $is_first) {
                [$response, $continuationToken] = $this->search($query, $time, $continuationToken);
                // dump($response);
                $data = array_merge($data, $response);
                $is_first = false;
                $count++;
            }
        // }

        

        return response()->json($data);
        
        
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

            // if (!($item['lengthText'] ?? false)) {
            //     dd($json);
            //     dd($item);
            // }
                
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
        $GOOGLE_ABUSE_EXEMPTION = '';
        if ($GOOGLE_ABUSE_EXEMPTION !== '') {
            $cookieToAdd = 'GOOGLE_ABUSE_EXEMPTION=' . $GOOGLE_ABUSE_EXEMPTION;
            if (array_key_exists('http', $opts)) {
                $http = $opts['http'];
                if (array_key_exists('header', $http)) {
                    $headers = $http['header'];
                    foreach ($headers as $headerIndex => $header) {
                        if (str_starts_with($header, 'Cookie: ')) {
                            $opts['http']['header'][$headerIndex] = "$header; $cookieToAdd";
                            break;
                        }
                    }
                }
            } else {
                $opts = [
                    'http' => [
                        'header' => [
                            "Cookie: $cookieToAdd"
                        ]
                    ]
                ];
            }
        }
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
