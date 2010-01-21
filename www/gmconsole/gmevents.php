<?php

require_once('config.php');
require_once('classes/Navigation.php');
require_once('classes/PSGmEvents.php');

session_start();

// is the user logged in?
if (!isset($_SESSION["__SECURITY_LEVEL"])) {
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
        <script language="javascript" type="text/javascript">
            function setEventId(eventId)
            {
                document.getElementById("eventId").value = eventId;
                document.getElementById("eventListForm").submit();
            }
        </script>
    </head>
    <body>
        <table>
            <tr>
                <td style="vertical-align:top;">
<?php
                    echo Navigation::S_GetNavigation();
?>
                </td>
                <form id="eventListForm" action="gmeventdetails.php" method="post">
                <td style="width:800px; vertical-align:top;">
                    <table class="table">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>GM</th>
                            <th>Vote Average</th>
                            <th>Action</th>
                        </tr>
                        
                        <?php 
                            $GM_events = new PSGmEvents();
                            $events = $GM_events->getEvents();
                            foreach ($events as $event) {
                                echo '<tr>';
                                echo '<td>' . $event['id'] . '</td>';
                                echo '<td>' . $event['name'] . '</td>';
                                echo '<td>' . $event['gm_name'] . ' ' . $event['gm_lastname'] . '</td>';
                                echo '<td>' . $event['avg'] . '</td>';
                                echo '<td><a href="javascript:setEventId(\'' . $event['id'] . '\');">View</a></td>';
                                echo '</tr>';
                            }
                            
                        ?>
                    </table>
                        <input type="hidden" id="eventId" name="eventId"/>
                </td>
            </tr>
        </table>
    </body>
                        
    
</html>