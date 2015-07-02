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

            // Retrieve data
            $youtubeRaw = file_get_contents(YOUTUBE_API . $url);
            $youtubeDec = json_decode($youtubeRaw);



            $reply['nextPage'] = $youtubeDec->nextPageToken;

            foreach ($youtubeDec->items as $video)
            {
                array_push($reply['data'],[
                    'title'     => $video->snippet->title,
                    'id'        => $video->id->videoId,
                    'url'       => 'https://www.youtube.com/watch?v=' . $video->id->videoId,
                    'thumbnail' => 'https://i.ytimg.com/vi/' . $video->id->videoId . '/mqdefault.jpg'
                ]);
            }
        }
        catch (\Exception $e)
        {
            $reply['status']['message'] = 'Youtube: ' . $e->getMessage();
            $reply['status']['file'] = $e->getFile();
            $reply['status']['line'] = $e->getLine();
            $reply['status']['code'] = 400;
            echo json_encode($reply);
            exit;
        }

        // Everything went right
        $reply['status']['message'] = 'Videos retrieved successfully';
        $reply['status']['code'] = 200;
    }

    function videoDetails ($youtubeId)
    {
        global $reply;
        global $db;

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
                    'title'        => $video->snippet->title,
                    'url'          => 'https://www.youtube.com/watch?v=' . $video->id,
                    'embedUrl'     => 'https://www.youtube.com/embed/' . $video->id,
                    'thumbnail'    => 'https://i.ytimg.com/vi/' . $video->id . '/mqdefault.jpg',
                    'description'  => $video->snippet->description,
                    'views'        => $video->statistics->viewCount,
                    'published'    => $video->snippet->publishedAt,
                    'channelTitle' => $video->snippet->channelTitle,
                    'channelId'    => $video->snippet->channelId
                ]);
                $reply['status']['message'] = 'Video details retrieved successfully';
                $reply['status']['code'] = 200;
            }
        }
        catch (\Exception $e)
        {
            $reply['status']['message'] = 'Youtube: ' . $e->getMessage();
            $reply['status']['file'] = $e->getFile();
            $reply['status']['line'] = $e->getLine();
            $reply['status']['code'] = 400;
            echo json_encode($reply);
            exit;
        }
    }
}
