<?php
/**
 * Created by IntelliJ IDEA.
 * User: Arjo
 * Date: 1-7-2015
 * Time: 20:30
 */

namespace imp;


class DatabaseHandler {

    function newCollection($categoryName)
    {
        global $db;
        global $reply;

        htmlentities($categoryName);

        try
        {
            $submission = $db->prepare("
                INSERT INTO categories (`name`)
                VALUE (?);
            ");
            $submission->bindParam(1, $categoryName);
            $submission->execute();
        }
        catch (\PDOException $e)
        {
            if ($e->getCode() == '23000')
                $code = 409;
            else
                $code = $e->getCode();

            $reply['status']['message'] = 'PDOException: ' . $e->getMessage();
            $reply['status']['file'] = $e->getFile();
            $reply['status']['line'] = $e->getLine();
            $reply['status']['code'] = $code;

            echo json_encode($reply);
            exit;
        }
        // Everything went right
        $reply['status']['message'] = 'Collection successfully added.';
        $reply['status']['code'] = 201;
    }

    function newVideo($title, $youtubeId)
    {
        global $db;
        global $reply;

        htmlentities($title);

        try
        {
            $submission = $db->prepare("
                INSERT INTO videos (`youtube_id`, `title`)
                VALUE (?, ?);
            ");
            $submission->bindParam(1, $youtubeId);
            $submission->bindParam(2, $title);
            $submission->execute();
        }
        catch (\PDOException $e)
        {
            if ($e->getCode() == '23000')
                $code = 409;
            else
                $code = $e->getCode();

            $reply['status']['message'] = 'PDOException: ' . $e->getMessage();
            $reply['status']['file'] = $e->getFile();
            $reply['status']['line'] = $e->getLine();
            $reply['status']['code'] = $code;

            echo json_encode($reply);
            exit;
        }
        // Everything went right
        $reply['status']['message'] = 'Video successfully added.';
        $reply['status']['code'] = 201;
    }

    function addVideoToCategory($categoryId, $youtubeId)
    {
        global $db;
        global $reply;
        global $youtube;

        try
        {
            $videoCheck = $db->prepare('
                SELECT COUNT(`youtube_id`) AS number_of_videos, `id`
                FROM videos
                WHERE `youtube_id` = ?
            ');
            $videoCheck->bindParam(1, $youtubeId);
            $videoCheck->execute();
            $videoCheck = $videoCheck->fetch(\PDO::FETCH_ASSOC);
            $videoId = $videoCheck['id'];
            $videoCheck = $videoCheck['number_of_videos'];

            if (!$videoCheck)
            {
                // add video to database
                $youtube->videoDetails($youtubeId);
                $title = $reply['data'][0]['title'];
                $reply['data'] = [];
                $this->newVideo($title, $youtubeId);
                $videoId = $db->lastInsertId();
            }

            $submission = $db->prepare("
                INSERT INTO categories_has_videos (`category_id`, `video_id`)
                VALUE (?, ?);
            ");
            $submission->bindParam(1, $categoryId, \PDO::PARAM_INT);
            $submission->bindParam(2, $videoId, \PDO::PARAM_INT);
            $submission->execute();
        }
        catch (\PDOException $e)
        {
            if ($e->getCode() == '23000')
                $code = 409;
            else
                $code = $e->getCode();

            $reply['status']['message'] = 'PDOException: ' . $e->getMessage();
            $reply['status']['file'] = $e->getFile();
            $reply['status']['line'] = $e->getLine();
            $reply['status']['code'] = $code;

            echo json_encode($reply);
            exit;
        }
        // Everything went right
        $reply['status']['message'] = 'Video successfully added.';
        $reply['status']['code'] = 201;
    }

    function removeVideoFromCategory($category, $video)
    {
        global $db;
        global $reply;

        try
        {
            $request = $db->prepare("
                DELETE FROM categories_has_videos
                WHERE `video_id` = ? AND `category_id` = ?
            ");
            $request->bindParam(1, $video);
            $request->bindParam(2, $category);
            $request->execute();
        }
        catch (\PDOException $e)
        {
            if ($e->getCode() == '23000')
                $code = 409;
            else
                $code = $e->getCode();

            $reply['status']['message'] = 'PDOException: ' . $e->getMessage();
            $reply['status']['file'] = $e->getFile();
            $reply['status']['line'] = $e->getLine();
            $reply['status']['code'] = $code;

            echo json_encode($reply);
            exit;
        }
        // Everything went right
        $reply['status']['message'] = 'Video successfully removed from category.';
        $reply['status']['code'] = 204;
    }

    function deleteCategory($categoryId)
    {
        global $db;
        global $reply;

        try
        {
            $request = $db->prepare('
                DELETE FROM categories
                WHERE `id`=?
            ');
            $request->bindParam(1, $categoryId, \PDO::PARAM_INT);
            $request->execute();
        }
        catch (\PDOException $e)
        {
            if ($e->getCode() == '23000')
                $code = 409;
            else
                $code = $e->getCode();

            $reply['status']['message'] = 'PDOException: ' . $e->getMessage();
            $reply['status']['file'] = $e->getFile();
            $reply['status']['line'] = $e->getLine();
            $reply['status']['code'] = $code;

            echo json_encode($reply);
            exit;
        }
        // Everything went right
        $reply['status']['message'] = 'Category successfully deleted.';
        $reply['status']['code'] = 204;
    }

    function getCategoryList()
    {
        global $db;
        global $reply;

        try
        {
            $request = $db->prepare("
                SELECT *
                FROM categories
                ORDER BY `id` ASC
            ");
            $request->execute();
            $request = $request->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($request as $row)
            {
                array_push($reply['data'],[
                    'name' => $row['name'],
                    'id' => $row['id']
                ]);
            }

        }
        catch (\PDOException $e)
        {
            if ($e->getCode() == '23000')
                $code = 409;
            else
                $code = $e->getCode();

            $reply['status']['message'] = 'PDOException: ' . $e->getMessage();
            $reply['status']['file'] = $e->getFile();
            $reply['status']['line'] = $e->getLine();
            $reply['status']['code'] = $code;

            echo json_encode($reply);
            exit;
        }
        // Everything went right
        $reply['status']['message'] = 'Video successfully removed from category.';
        $reply['status']['code'] = 204;
    }

    function getCategory($categoryId)
    {
        global $db;
        global $reply;

        try
        {
            $request = $db->prepare("
                SELECT *
                FROM videos v, categories_has_videos chv
                WHERE v.`id` = `video_id`
                AND `category_id`=?
                ORDER BY `datetime` DESC
            ");
            $request->bindParam(1, $categoryId, \PDO::PARAM_INT);
            $request->execute();
            $request = $request->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($request as $video)
            {
                array_push($reply['data'],[
                    'videoId'   => $video['id'],
                    'title'     => $video['title'],
                    'youtubeId' => $video['youtube_id'],
                    'url'       => 'https://www.youtube.com/watch?v=' . $video['youtube_id'],
                    'embedUrl'  => 'https://www.youtube.com/embed/' . $video['youtube_id'],
                    'thumbnail' => 'https://i.ytimg.com/vi/' . $video['youtube_id'] . '/mqdefault.jpg'
                ]);
            }

        }
        catch (\PDOException $e)
        {
            if ($e->getCode() == '23000')
                $code = 409;
            else
                $code = $e->getCode();

            $reply['status']['message'] = 'PDOException: ' . $e->getMessage();
            $reply['status']['file'] = $e->getFile();
            $reply['status']['line'] = $e->getLine();
            $reply['status']['code'] = $code;

            echo json_encode($reply);
            exit;
        }
        // Everything went right
        $reply['status']['message'] = 'Video successfully removed from category.';
        $reply['status']['code'] = 204;
    }

    function getAllCategories()
    {
        global $db;
        global $reply;

        try
        {
            $request = $db->prepare("
                SELECT *
                FROM videos v, categories_has_videos chv
                WHERE v.`id` = `video_id`
                ORDER BY `datetime` DESC
            ");
            $request->execute();
            $request = $request->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($request as $video)
            {
                array_push($reply['data'],[
                    'videoId'   => $video['id'],
                    'title'     => $video['title'],
                    'youtubeId' => $video['youtube_id'],
                    'url'       => 'https://www.youtube.com/watch?v=' . $video['youtube_id'],
                    'embedUrl'  => 'https://www.youtube.com/embed/' . $video['youtube_id'],
                    'thumbnail' => 'https://i.ytimg.com/vi/' . $video['youtube_id'] . '/mqdefault.jpg'
                ]);
            }

        }
        catch (\PDOException $e)
        {
            if ($e->getCode() == '23000')
                $code = 409;
            else
                $code = $e->getCode();

            $reply['status']['message'] = 'PDOException: ' . $e->getMessage();
            $reply['status']['file'] = $e->getFile();
            $reply['status']['line'] = $e->getLine();
            $reply['status']['code'] = $code;

            echo json_encode($reply);
            exit;
        }
        // Everything went right
        $reply['status']['message'] = 'Succesfully retrieved all category videos';
        $reply['status']['code'] = 200;
    }

    function getLatestVideos()
    {
        global $db;
        global $reply;

        try
        {
            $request = $db->prepare('
                SELECT *
                FROM videos v, categories_has_videos chv
                WHERE v.`id` = chv.`video_id`
                ORDER BY `datetime` DESC
                LIMIT 10
            ');
            $request->bindParam(1, $categoryId, \PDO::PARAM_INT);
            $request->execute();
            $request = $request->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($request as $video)
            {
                array_push($reply['data'],[
                    'videoId'   => $video['id'],
                    'title'     => $video['title'],
                    'youtubeId' => $video['youtube_id'],
                    'url'       => 'https://www.youtube.com/watch?v=' . $video['youtube_id'],
                    'embedUrl'  => 'https://www.youtube.com/embed/' . $video['youtube_id'],
                    'thumbnail' => 'https://i.ytimg.com/vi/' . $video['youtube_id'] . '/mqdefault.jpg'
                ]);
            }

        }
        catch (\PDOException $e)
        {
            if ($e->getCode() == '23000')
                $code = 409;
            else
                $code = $e->getCode();

            $reply['status']['message'] = 'PDOException: ' . $e->getMessage();
            $reply['status']['file'] = $e->getFile();
            $reply['status']['line'] = $e->getLine();
            $reply['status']['code'] = $code;

            echo json_encode($reply);
            exit;
        }
        // Everything went right
        $reply['status']['message'] = 'Succesfully retrieved latest videos.';
        $reply['status']['code'] = 200;
    }
}
