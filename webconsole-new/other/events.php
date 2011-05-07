<?php

function listevents()
{
    if (checkaccess('events', 'read')) {
        

        $complete_events = getEvents($_GET['order']);
        
        //List all completed events with the option to view further details.
        echo '<table border="1" >';
        
        echo '<tr>';
        echo '<th><a href="./index.php?do=events&amp;order=id">ID</a></th>';    
        echo '<th><a href="./index.php?do=events&amp;order=name">Name</a></th>';
        echo '<th><a href="./index.php?do=events&amp;order=gm">GM</a></th>';
        echo '<th><a href="./index.php?do=events&amp;order=avg">Average</a></th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        
        foreach ($complete_events as $event) 
        {
            echo '<tr>';
            echo '<td>' . $event['id'] . '</td>';
            echo '<td>' . $event['name'] . '</td>';
            echo '<td>' . $event['gm_name'] . ' ' . $event['gm_lastname'] . '</td>';
            echo '<td>' . $event['avg'] . '</td>';
            echo '<td><a href="./index.php?do=viewevent&amp;id=' . $event['id'] . '">View</a></td>';
            echo '</tr>';
        }
        
        echo '</table>';
    }
      
}

function viewevent()
{
   //Display individual event
    if (checkaccess('events', 'read')) 
    {
        $event_id =  $_GET['id'];
        if (is_numeric($event_id)) 
        {
            //get info         
            $details = getEventDetails($event_id);
            $comments = getEventComments($event_id);
            $average = getVoteAverage($event_id);
            $non_voters = getNonVoters($event_id);
         
            //display
            echo '<b>Event ID:</b> ' . $details['id'] . '<br/>';
            echo '<b>Event:</b> ' . $details['name'] .  '<br/>';
            echo '<b>Desciption:</b> ' . $details['description'] . '<br/>';
            echo '<b>Ran by:</b> ' . $details['gm_name'] . ' ' . $details['gm_lastname'] . '<br/>';
            echo '<b>Vote Average:</b> ' . $average;
            echo '<br/><br/><b>Comments:</b><br/>';
         
            foreach ($comments as $comment) {
                echo '<b>By:</b>' . $comment['name'];
                echo '<div ><b>Vote: ' . $comment['vote'] . '</b></div>';
                echo '<b>Comment: </b>' . $comment['comment'];
                echo '<br/><br/>';
            }
         
            echo '<b>Non Voters:</b><br />';
         
            foreach ($non_voters as $non_voter) 
            {
                echo $non_voter['name'] . ' ' . $non_voter['lastname'] . '<br />';
            }
         
        }
        else 
        {
            //Shouldn't be here
            echo '<p class="error">Shouldn\'t be here</p>';
        }
    }
}

function getEvents($orderBy = null, $completed = true)
{
    if (isset($orderBy)) 
    {
        switch ($orderBy) 
        {
            case 'name':
                $append = 'ORDER BY gme.name ASC';
                break;
            case 'gm';
                $append = 'ORDER BY gme.name ASC';
                break;
            case 'id':
                $append = 'ORDER BY gme.id DESC';
                break;
            default:
                $append = '';
        }
    } 
    else 
    {
        $append = '';
    }
    
    $finished = ($completed) ? " WHERE gme.status = '2'" : " WHERE gme.status != '2'";
    
    $query = 'SELECT gme.id, gme.name, c.name AS gm_name, c.lastname AS gm_lastname FROM gm_events gme ' 
            .'LEFT JOIN characters c ON c.id = gme.gm_id'; 

    $query .= $finished;
    $query .= $append;        
    $result = mysql_query2($query);

    //Build list as an array
    $events = array();
    while ($row = mysql_fetch_assoc($result)) 
    {
        
        $average = getVoteAverage($row['id']);
        $events[] = array('name'=>$row['name'], 'avg'=>$average, 
                          'id'=>$row['id'], 'gm_name'=>$row['gm_name'], 'gm_lastname'=>$row['gm_lastname']);
    }
    
    
    //@todo look into a better way of doing this.
    if ($orderBy == 'avg') 
    {
        $pos = 0;
        $sort_arr = array();
        foreach ($events as $event) 
        {
            $sort_arr[$pos] = $event['avg'];
            $pos++;
        }
        //Now sorted by average asc, with keys intact    
        arsort($sort_arr);
        
        //rebuild $events
        $rebuild_events = array();
        foreach ($sort_arr as $key => $value) 
        {
            $rebuild_events[] = $events[$key];
        }
        
        $events = $rebuild_events;
    }
    
    return $events;
}

function getVoteAverage($event_id)
{
    //Get vote total and number of votes from db
    $query = sprintf("SELECT SUM(vote), COUNT(player_id) FROM character_events WHERE vote IS NOT NULL AND event_id = '%s'", mysql_real_escape_string($event_id));
    $result = mysql_query2($query);
    $row = mysql_fetch_assoc($result);
    
    //to avoid division by zero
    if ($row['COUNT(player_id)'] < 1)
    {
        $average = 0;
    } else  {
        //Calculate average
        $average = $row['SUM(vote)']/$row['COUNT(player_id)'];
    }
    
    //Format to 2dp for nicer display
    $average = number_format($average, 2);
    
    return $average;
}

function getNonVoters($event_id)
{
    $query = sprintf("SELECT c.name, c.lastname FROM characters c JOIN character_events ce ON ce.player_id = c.id WHERE ce.vote IS NULL AND ce.event_id = '%s'", mysql_real_escape_string($event_id));
    $result = mysql_query2($query);
    
    $non_voters = array();
    while($row = mysql_fetch_assoc($result)) 
    {
        $non_voters[] = $row;
    }
    
    return $non_voters;
}

function getEventDetails($event_id)
{
    $query = sprintf("SELECT gme.name, gme.description, c.name AS gm_name, c.lastname AS gm_lastname, gme.id AS id FROM gm_events gme 
                        LEFT JOIN characters c ON c.id = gme.gm_id WHERE gme.id = '%s'", mysql_real_escape_string($event_id));
    $result = mysql_query2($query);
    
    return mysql_fetch_assoc($result);
}

function getEventComments($event_id)
{
    $query = sprintf("SELECT ce.comments, ce.vote, c.name, c.lastname FROM character_events ce 
            LEFT JOIN characters c ON ce.player_id = c.id WHERE ce.comments IS NOT NULL AND ce.event_id='%s'", mysql_real_escape_string($event_id));
    $result = mysql_query2($query);

    $comments = array();
    while ($row = mysql_fetch_assoc($result)) 
    {
        $comments[] = array('name'=>$row['name'] . ' ' . $row['lastname'], 'vote'=>$row['vote'], 'comment'=>$row['comments']);
    }
    
    return $comments;
}

?>
