<?php

require_once('config.php');
require_once('classes/Navigation.php');
require_once('classes/PSGmEvents.php');

session_start();

// is the user logged in?
if (!isset($_SESSION["__SECURITY_LEVEL"])) 
{
    header("Location: index.php?origin=gmevents");
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <title>GM Events</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_RELURI?>"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_ADDON_RELURI?>"/>
    </head>
    <body>
        <table>
            <tr>
                <td style="vertical-align:top;">
<?php
                    echo Navigation::S_GetNavigation();
?>
                </td>
                <td style="width:800px; vertical-align:top;">
                <?php 

                if (is_numeric($_POST['eventId'])) 
                {
                        $events = new PSGmEvents();
                        $details = $events->getEventDetails($_POST['eventId']);
                        $comments = $events->getEventComments($_POST['eventId']);
                        $average = $events->getVoteAverage($_POST['eventId']);
                        $non_voters = $events->getNonVoters($_POST['eventId']);
                        
                        echo '<b>Event ID:</b> ' . $details['id'] . '<br/>';
                        echo '<b>Event:</b> ' . $details['name'] .  '<br/>';
                        echo '<b>Desciption:</b> ' . $details['description'] . '<br/>';
                        echo '<b>Ran by:</b> ' . $details['gm_name'] . ' ' . $details['gm_lastname'] . '<br/>';
                        echo '<b>Vote Average:</b> ' . $average;
                        echo '<br/><br/><b>Comments:</b><br/>';
                        
                        foreach ($comments as $comment) 
                        {
                            echo '<b>By:</b>' . $comment['name'];
                            echo '<div ><b>Vote: ' . $comment['vote'] . '</b></div>';
                            echo '<b>Comment: </b>' . $comment['comment'];
                            echo '<br/><br/>';
                        }
                        
                        echo '<b>Non Voters</b><br/>';
                        foreach ($non_voters as $non_voter) 
                        {
                            echo $non_voter['name'] . ' ' . $non_voter['lastname'] . '<br />';
                        }
                        
                    }
                    else 
                    {
                        echo 'Invalid event Id';
                    }
                ?>
                </td>
            </tr>
        </table>
    </body>
                        
    
</html>