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
            $url = 'search?part=snippet&q='.$query.'&type=video&key=';

            // Retrieve data
            $youtubeRaw = file_get_contents(YOUTUBE_API . $url . YOUTUBE_KEY);
            $youtubeDec = json_decode($youtubeRaw);

            foreach ($youtubeDec->items as $video)
            {
                array_push($reply['data'],[
                    'video' => [
                        'title' => $video->snippet->title,
                        'channel' => $video->snippet->channelTitle,
                        'channelId' => $video->snippet->channelId,
                        'url' => 'https://www.youtube.com/watch?v=' . $video->id->videoId,
                        'thumbnail' => 'https://i.ytimg.com/vi/' . $video->id->videoId . '/mqdefault.jpg',
                        'description' => $video->snippet->description
                    ]
                ]);
            }
        }
        catch (Exception $e)
        {
            array_push( $reply['errors'],[
                'message' => 'Youtube Search: '. $e->getMessage() .' '. $e->getFile() .' on line '. $e->getLine(),
                'code'    => $e->getCode()
            ]);
        }
    }
}
