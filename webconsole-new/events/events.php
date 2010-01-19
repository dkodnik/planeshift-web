<?php

function listevents()
{
	if (checkaccess('events', 'read')) {
		
		if (isset($_GET['order'])) {
			if (in_array($_GET['order'], array('name', 'gm', 'avg'))) {
				$events = getEvents($_GET['order']);
			} else {
				$events = getEvents();
			}			
		}
		else {
			$events = getEvents();
		}
		
		//List all completed events with the option to view further details.
		echo '<table border="1" >';
		
		echo '<tr>';
		echo '<th><a href="./index.php?do=events&order=name">Name</a></th>';
		echo '<th><a href="./index.php?do=events&order=gm">GM</a></th>';
		echo '<th><a href="./index.php?do=events&order=avg">Average</a></th>';
		echo '<th>Actions</th>';
		echo '</tr>';
		
		foreach ($events as $event) {
			echo '<tr>';
			echo '<td>' . $event['name'] . '</td>';
			echo '<td>' . $event['gm_name'] . ' ' . $event['gm_lastname'] . '</td>';
			echo '<td>' . $event['avg'] . '</td>';
			echo '<td><a href="./index.php?do=viewevent&id=' . $event['id'] . '">View</a></td>';
			echo '</tr>';
		}
		
		echo '</table>';
	}
}

function viewevent()
{
	//Display individual event
	if (checkaccess('events', 'read')) {
		$event_id =  $_GET['id'];
		if (is_numeric($event_id)) {
			//get info			
			$details = getEventDetails($event_id);
			$comments = getEventComments($event_id);
			$average = getVoteAverage($event_id);
			
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
		}
		else {
			//Shouldn't be here
			echo '<p class="error">Shouldn\'t be here</p>';
		}
	}
}

function getEvents($orderBy = null)
{
	if (isset($orderBy)) {
		switch ($orderBY) {
			case 'name':
				$append = 'ORDER BY gme.name ASC';
				break;
			case 'gm';
				$append = 'ORDER BY gme.name ASC';
				break;
			default:
				$append = '';
		}
	} else {
		$append = '';
	}
	
	$query = 'SELECT gme.id, gme.name, c.name AS gm_name, c.lastname AS gm_lastname FROM gm_events gme ' 
			.'LEFT JOIN characters c ON c.id = gme.gm_id'; //Add clause to only get completed events
	$query .= $append;		
	$result = mysql_query2($query);

	//Build list as an array
	$events = array();
	while ($row = mysql_fetch_assoc($result)) {
		
		$average = getVoteAverage($row['id']);
		$events[] = array('name'=>$row['name'], 'avg'=>$average, 
						  'id'=>$row['id'], 'gm_name'=>$row['gm_name'], 'gm_lastname'=>$row['gm_lastname']);
	}
	
	
	//@todo look into a better way of doing this.
	if ($orderBy == 'avg') {
		$pos = 0;
		$sort_arr = array();
		foreach ($events as $event) {
			$sort_arr[$pos] = $event['avg'];
			$pos++;
		}
		//Now sorted by average asc, with keys intact	
		arsort($sort_arr);
		
		//rebuild $events
		$rebuild_events = array();
		foreach ($sort_arr as $key => $value) {
			$rebuild_events[] = $events[$key];
		}
		
		$events = $rebuild_events;
	}
	
	return $events;
}

function getVoteAverage($event_id)
{
	//Get vote total and number of votes from db
	$query = "SELECT SUM(vote), COUNT(player_id) FROM character_events WHERE event_id = $event_id";
	$result = mysql_query2($query);
	$row = mysql_fetch_assoc($result);
	
	//to avoid division by zero
	if ($row['COUNT(player_id)'] < 1) {
		$average = 0;
	} else  {
		//Calculate average
		$average = $row['SUM(vote)']/$row['COUNT(player_id)'];
	}
	
	//Format to 2dp for nicer display
	$average = number_format($average, 2);
	
	return $average;
}

function getEventDetails($event_id)
{
	$query = 'SELECT gme.name, gme.description, c.name AS gm_name, c.lastname AS gm_lastname, gme.id AS id FROM gm_events gme ' 
			.'LEFT JOIN characters c ON c.id = gme.gm_id WHERE gme.id =' . $event_id;
	$result = mysql_query2($query);
	
	return mysql_fetch_assoc($result);
}

function getEventComments($event_id)
{
	$query = 'SELECT ce.comments, ce.vote, c.name, c.lastname FROM character_events ce '
			.'LEFT JOIN characters c ON ce.player_id = c.id WHERE ce.event_id=' . $event_id;
	$result = mysql_query2($query);

	$comments = array();
	while ($row = mysql_fetch_assoc($result)) {
		$comments[] = array('name'=>$row['name'] . ' ' . $row['lastname'], 'vote'=>$row['vote'], 'comment'=>$row['comments']);
	}
	
	return $comments;
}

?>