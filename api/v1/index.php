<?php
require './includes/init.php';

try
{
    $query = "League+of+Legends";
    $url = 'search?part=snippet&q='.$query.'&type=video&key=';
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
        'message' => 'Exception Time: '. $e->getMessage() .' '. $e->getFile() .' on line '. $e->getLine(),
        'code'    => $e->getCode()
    ]);
}
echo json_encode($reply);
