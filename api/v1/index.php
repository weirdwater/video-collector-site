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
                    array_push( $reply['errors'],[
                        'message' => 'No query provided'
                    ]);
                break;
            case 'video':
                if (isset($_GET['id']))
                    $youtube->videoDetails($_GET['id']);
                else
                    array_push( $reply['errors'],[
                        'message' => 'No id provided'
                    ]);
                break;
            default :
                // Return all saved videos
                break;
        }
    }
    else
    {
        array_push( $reply['errors'],[
            'message' => 'No paramaters specified'
        ]);
    }
}
catch (Exception $e)
{
    array_push( $reply['errors'],[
        'message' => 'Exception: ' . $e->getMessage(),
        'file'    => $e->getFile(),
        'line'    => $e->getLine(),
        'code'    => $e->getCode()
    ]);
}

echo json_encode($reply);
