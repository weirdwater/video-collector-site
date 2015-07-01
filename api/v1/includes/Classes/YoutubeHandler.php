<?php
/**
 * Created by IntelliJ IDEA.
 * User: Arjo
 * Date: 1-7-2015
 * Time: 12:56
 */

namespace imp;


class YoutubeHandler {

    function __construct ()
    {

    }

    /**
     * Queries YouTube for relevant videos using the given query
     * @param $query
     * @param $reply
     * @return mixed
     */
    function searchQuery ($query)
    {
        global $reply;

        try
        {
            // Prepare query for YouTube
            $query = htmlentities($query);
            $query = str_replace(' ', '+', $query);

            // Prepare YouTube url
            $url = 'search?part=snippet&maxResults=15&q='.$query.'&type=video';

            // Check if a nextpage token was provided
            if (isset($_GET['np']))
                $url .= '&pageToken=' . $_GET['np'];

            // Add Key
            $url .= '&key=' . YOUTUBE_KEY;

            array_push( $reply['errors'],[
                'message' => YOUTUBE_API . $url,
            ]);

            // Retrieve data
            $youtubeRaw = file_get_contents(YOUTUBE_API . $url);
            $youtubeDec = json_decode($youtubeRaw);



            $reply['nextPage'] = $youtubeDec->nextPageToken;

            foreach ($youtubeDec->items as $video)
            {
                array_push($reply['data'],[
                    'title' => $video->snippet->title,
                    'id' => $video->id->videoId,
                    'url' => 'https://www.youtube.com/watch?v=' . $video->id->videoId,
                    'thumbnail' => 'https://i.ytimg.com/vi/' . $video->id->videoId . '/mqdefault.jpg'
                ]);
            }
        }
        catch (\Exception $e)
        {
            array_push( $reply['errors'],[
                'message' => 'Youtube Search: '. $e->getMessage() .' '. $e->getFile() .' on line '. $e->getLine(),
                'code'    => $e->getCode()
            ]);
        }
    }

    function videoDetails ($youtubeId)
    {
        global $reply;

        try
        {
            // Prepare YouTube url
            $url = 'videos?part=snippet,statistics&id='.$youtubeId.'&key=';

            // Retrieve data
            $youtubeRaw = file_get_contents(YOUTUBE_API . $url . YOUTUBE_KEY);
            $youtubeDec = json_decode($youtubeRaw);

            foreach ($youtubeDec->items as $video)
            {
                array_push($reply['data'],[
                    'title' => $video->snippet->title,
                    'url' => 'https://www.youtube.com/watch?v=' . $video->id,
                    'embedUrl' => 'https://www.youtube.com/embed/' . $video->id,
                    'thumbnail' => 'https://i.ytimg.com/vi/' . $video->id . '/mqdefault.jpg',
                    'description' => $video->snippet->description,
                    'views' => $video->statistics->viewCount,
                    'published' => $video->snippet->publishedAt,
                    'channelTitle' => $video->snippet->channelTitle,
                    'channelId' => $video->snippet->channelId
                ]);
            }
        }
        catch (\Exception $e)
        {
            array_push( $reply['errors'],[
                'message' => 'Youtube lookup: '. $e->getMessage() .' '. $e->getFile() .' on line '. $e->getLine(),
                'code'    => $e->getCode()
            ]);
        }
    }
}
