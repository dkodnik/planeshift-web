<?php

require_once('PSBaseClass.php');

class PSGmEvents extends PSBaseClass {
    

    public function getEvents($orderBy = null, $completed = true)
    {
        if (isset($orderBy)) 
        {
            switch ($orderBY) 
            {
                case 'name':
                    $append = 'ORDER BY gme.name ASC';
                    break;
                case 'gm';
                    $append = 'ORDER BY gme.name ASC';
                    break;
                default:
                    $append = '';
            }
        } 
        else 
        {
            $append = '';
        }
        
        $connection = PSBaseClass::S_GetConnection();
        
        $finished = ($completed) ? " WHERE status = '2'" : " WHERE status != '2'";
        
        $query = 'SELECT gme.id, gme.name, c.name AS gm_name, c.lastname AS gm_lastname FROM gm_events gme ' 
                .'LEFT JOIN characters c ON c.id = gme.gm_id'; 
        $query .= $finished;
        $query .= $append;        
        $result = mysql_query($query);
    
        //Build list as an array
        $events = array();
        while ($row = mysql_fetch_assoc($result)) 
        {
            
            $average = $this->getVoteAverage($row['id']);
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
    
    public function getVoteAverage($event_id)
    {
        $connection = PSBaseClass::S_GetConnection();
        
        //Get vote total and number of votes from db
        $query = sprintf("SELECT SUM(vote), COUNT(player_id) FROM character_events WHERE vote IS NOT NULL AND event_id = '%s'", mysql_real_escape_string($event_id));
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        
        //to avoid division by zero
        if ($row['COUNT(player_id)'] < 1) 
        {
            $average = 0;
        } else  
        {
            //Calculate average
            $average = $row['SUM(vote)']/$row['COUNT(player_id)'];
        }
        
        //Format to 2dp for nicer display
        $average = number_format($average, 2);
        
        return $average;
    }
    
    public function getEventDetails($event_id)
    {
        $connection = PSBaseClass::S_GetConnection();
        
        $query = sprintf("SELECT gme.name, gme.description, c.name AS gm_name, c.lastname AS gm_lastname, gme.id AS id FROM gm_events gme  
                          LEFT JOIN characters c ON c.id = gme.gm_id WHERE gme.id ='%s'",  mysql_real_escape_string($event_id));
        $result = mysql_query($query);
        
        return mysql_fetch_assoc($result);
    }
    
    public function getEventComments($event_id)
    {
        $connection = PSBaseClass::S_GetConnection();
        
        $query = sprintf("SELECT ce.comments, ce.vote, c.name, c.lastname FROM character_events ce 
                        LEFT JOIN characters c ON ce.player_id = c.id WHERE ce.comments IS NOT NULL AND ce.event_id='%s'", mysql_real_escape_string($event_id));
        $result = mysql_query($query);
    
        $comments = array();
        while ($row = mysql_fetch_assoc($result)) 
        {
            $comments[] = array('name'=>$row['name'] . ' ' . $row['lastname'], 'vote'=>$row['vote'], 'comment'=>$row['comments']);
        }
        
        return $comments;
    }
    
    public function getNonVoters($event_id)
    {
        $connection = PSBaseClass::S_GetConnection();
        
        $query = sprintf("SELECT c.name, c.lastname FROM characters c JOIN character_events ce ON ce.player_id = c.id WHERE ce.vote IS NULL AND ce.event_id = '%s'", mysql_real_escape_string($event_id));
        $result = mysql_query($query);
        
        $non_voters = array();
        while($row = mysql_fetch_assoc($result)) 
        {
            $non_voters[] = $row;
        }
        
        return $non_voters;
    }
    
}