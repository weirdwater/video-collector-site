<?php
require './includes/init.php';

try
{
    if (isset($_GET['action']))
    {
        switch ($_GET['action'])
        {
            case 'search':
                if (isset($_GET['q']))
                    $youtube->searchQuery($_GET['q']);
                else
                    $reply['status']['message'] = 'No paramaters specified';
                    $reply['status']['code'] = 400;
                break;
            case 'video':
                if (isset($_GET['id']) && $_GET['id'] !== 'latest')
                    $youtube->videoDetails($_GET['id']);
                if (isset($_GET['id']) && $_GET['id'] === 'latest')
                    $database->getLatestVideos();
                else
                    $reply['status']['message'] = 'No paramaters specified';
                    $reply['status']['code'] = 400;
                break;
            case 'category':
                if (isset($_GET['category']))
                {
                    if (is_numeric($_GET['category']))
                        $database->getCategory($_GET['category']);
                    else if ($_GET['category'] == 'all')
                        $database->getAllCategories();
                    else if ($_GET['category'] == 'list')
                        $database->getCategoryList();
                }
                break;
            case 'delete':
                if ($_GET['type'] == 'category' && isset($_GET['id']) && is_numeric($_GET['id']))
                {
                    $database->deleteCategory($_GET['id']);
                }
                if ($_GET['type'] == 'relation' && isset($_GET['ytId']) && isset($_GET['catId']))
                    $database->removeVideoFromCategory($_GET['catId'], $_GET['ytId']);
                break;
            case 'insert':
                if (isset($_GET['type']))
                {
                    if ($_GET['type'] == 'category' && isset($_GET['title']))
                        $database->newCollection($_GET['title']);
                    else if ($_GET['type'] == 'relation' && isset($_GET['ytId']) || isset($_GET['catId']))
                        $database->addVideoToCategory($_GET['catId'], $_GET['ytId']);
                    else if (($_GET['type']) == 'video' && isset($_GET['youtube']) && isset($_GET['title']))
                        $database->newVideo($_GET['title'], $_GET['youtube']);
                    else
                    {
                        $reply['status']['message'] = 'No paramaters specified';
                        $reply['status']['code'] = 400;
                    }
                }
                else
                    $reply['status']['message'] = 'No paramaters specified';
                    $reply['status']['code'] = 400;
                break;
            default :
                // Return all saved videos
                break;
        }
    }
    else
    {
        $reply['status']['message'] = 'No action specified';
        $reply['status']['code'] = 400;
    }
}
catch (Exception $e)
{
    $reply['status']['message'] = 'PDOException: ' . $e->getMessage();
    $reply['status']['file'] = $e->getFile();
    $reply['status']['line'] = $e->getLine();
    $reply['status']['code'] = 500;
}

echo json_encode($reply);
