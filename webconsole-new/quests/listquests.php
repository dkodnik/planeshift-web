<?php
function listquests()
{
    if(checkaccess('quests', 'read'))
    {
  
        $mode = (isset($_GET['mode']) ? $_GET['mode'] : '');

        if ($mode=='hier') // Hierarchical quest listing mode (not default).
        {
            echo '<a href="index.php?do=listquests">Show quest scripts as simple list</a><br />';
            echo '<p> Please notice that this view only relates those scripts that have each other listed in the "pre-requisites" field, not in the actual quest script.</p>';
            // build an array with parentname | childname | child data
            $query = "SELECT id, name, category, prerequisite FROM quests ORDER BY name";
            $result = mysql_query2($query);
            while ($line = mysql_fetch_array($result, MYSQL_ASSOC)){
                $data =  array($line['id'],  $line['prerequisite'], $line['category']); // store id, name and category in $data
                $parent_name = parsePrereqScript($line['prerequisite']);
                $data2 = array($data, $parent_name); // make a $data2 with the whole set of data as [0] and the name of the parent as [1] (or null if not found)
                $questarray[$line['name']] = $data2; // add the quest to  an array indexed by it's name.
            }
            echo '<ul>';
            // recurse on nodes
            foreach ($questarray as $key => $data2) {
                if ($data2[1]==null)  
                {
                    $data = $data2[0];
                    if(checkaccess('quests', 'edit')) // list list node links depending on access
                    {
                        echo '<li> <a href="index.php?do=editquest&id='.$data[0].'">'.$key.'</a> ( '.$data[2].' )</li>';
                    }
                    else 
                    {
                        echo '<li><a href="index.php?do=readquest&id='.$data[0].'">'.$key.'</a> ( '.$data[2].' )</li>';
                    }
                    display_children($questarray,$key); // List children (if any).
                }
            }
            echo '</ul>';
        } 
        else  // normal quest listing mode. (chosen by default)
        {
            $factionlimit = '';
            if (isset($_GET['factionLimit'])) {
                $factionlimit = mysql_real_escape_string($_GET['factionLimit']);
            }
            $factions = PrepSelect('factions');
            echo '<table border="0" width="80%"><tr><td><a href="index.php?do=listquests&mode=hier">Show quest scripts in hierarchical view</a></td>';
            echo '<td><form method="get" action="index.php">';
            echo '<input type="hidden" name="do" value="listquests">';
            if (isset($_GET['sort'])) {
                echo '<input type="hidden" name="sort" value="'.$_GET['sort'].'">';
            }
            echo 'limit to: '.DrawSelectBox('factions', $factions, 'factionLimit', $factionlimit, true);
            echo '<input type="submit" name="submit" value="limit results"></form></td></tr></table><br/>';
            
            $query = 'SELECT q.id, q.name, q.category, q.player_lockout_time, q.quest_lockout_time, q.prerequisite FROM quests AS q';
            
            // REGEXP queries match case-insensitive. To do this on a "blob" field, we first need to convert the data to a charset. (SQL supports REGEXP on binary data, but it'll become case sensitive, so we don't want that.)
            // in a regexp, you can make a character group (in our case \n (with an additional \ to escape it in the PHP string)) by placing something between [].
            // so we get '[\n]Give[^\n]*faction[^\n]*$factionlimit' In this case our second character group is 'NOT newline' where \n is the newline, and ^ means not. Finally, the * after the group means zero or more of this character.
            // In other words, we are looking for a character sequence that contains a newline, followed by "Give" (so it must be at the start of the line) any amount of characters (a number in a proper script), the word "faction" and 
            // "$factionlimit" (being one of the factions that exist in the database, if this script was properly used.
            // Because we say there can be no newline characters between the matches, this effectively means they have to be on the same line, in the form of "give **** faction <name>" where *** can be anything or nothing at all (but in practive will prove to be a number).

            if ($factionlimit != '') {
                $query .= " LEFT JOIN quest_scripts AS qs ON q.id=qs.quest_id WHERE CONVERT(qs.script USING latin1) REGEXP '[\\n]Give[^\\n]*faction $factionlimit'";
            }
            
            if(!isset($_GET['sort'])){
                $query .= ' ORDER BY name ASC';
            }
            else
            {
                switch($_GET['sort'])
                {
                    case 'id':
                        $query .= ' ORDER BY id ASC';
                        break;
                    case 'category':
                        $query .= ' ORDER BY category ASC';
                        break;
                    case 'name':
                        $query .= ' ORDER BY name ASC';
                        break;
                    case 'plock':
                        $query .= ' ORDER BY player_lockout_time ASC'; 
                        break;
                    case 'qlock':
                        $query .= ' ORDER by quest_lockout_time ASC';
                        break;
                    default:
                        $query .= ' ORDER BY name ASC';
                }
            }
            $result = mysql_query2($query);
            echo '<table border="1">'."\n";
            echo '<tr><th><a href="./index.php?do=listquests&amp;sort=id'.($factionlimit==''?'':'&factionLimit='.$factionlimit).'">ID</a></th>';
            echo '<th><a href="./index.php?do=listquests&amp;sort=category'.($factionlimit==''?'':'&factionLimit='.$factionlimit).'">Category</a></th>';
            echo '<th><a href="./index.php?do=listquests&amp;sort=name'.($factionlimit==''?'':'&factionLimit='.$factionlimit).'">Name</a></th>';
            echo '<th><a href="./index.php?do=listquests&amp;sort=plock'.($factionlimit==''?'':'&factionLimit='.$factionlimit).'">Player Lockout</a></th>';
            echo '<th><a href="./index.php?do=listquests&amp;sort=qlock'.($factionlimit==''?'':'&factionLimit='.$factionlimit).'">Quest Lockout</a></th>';
            echo '<th>Prerequisites</th><th>Actions</th></tr>';
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
            {
                echo '<tr><td>'.$row['id'].'</td><td>'.$row['category'].'</td><td>'.$row['name'].'</td><td>'.$row['player_lockout_time'];
                echo '</td><td>'.$row['quest_lockout_time'].'</td><td>'.htmlspecialchars($row['prerequisite']).'</td>';
                echo '<td><a href="./index.php?do=readquest&amp;id='.$row['id'].'">Read</a>';
                echo '<br /><a href="./index.php?do=validatequest&amp;id='.$row['id'].'">Validate</a>';
                if (checkaccess('quests', 'edit'))
                {
                    echo '<br/><a href="./index.php?do=editquest&amp;id='.$row['id'].'">Edit</a>';
                }
                if (checkaccess('quests', 'delete'))
                {
                    echo '<br/><a href="./index.php?do=deletequest&amp;id='.$row['id'].'">Delete</a>';
                }
                echo '</td></tr>';
            }
            echo '</table>'."\n";
        }
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
function parsePrereqScript($prereq)
{
	$pos = stristr($prereq, '<pre>');
	$istrigger = 0;
	$quest_name = null;
	if ($pos != false) {
		$istrigger = 1;
		$quest_name = 1;
	}

    // parse trigger
    if ($istrigger==1)
    {
        $pos = strpos($prereq, '<completed');
        if ( $pos != 0) // If some quest got completed, there is a parent, so grab the name
        {
            $pos = strpos($prereq, '"');
            $endname = substr($prereq,$pos+1);
            $pos = strpos($endname, '"');
            $endname = substr($endname,0,$pos);
            $quest_name = $endname;  // and set the name in the variable.
        }
    }
    return $quest_name; // returns null if nothing was found and the name of the parent quest if it found one.
}


/*
*  This method is recursive, each iteration will add another set of <ul> tags for proper displaying. $current is the "parent" for
*  which we want to display the childs (which are searched for in the $questarray)
*/
function display_children($questarray, $current) 
{

    $list_started = false; // boolean used to determine if a list was started (if children were found).
    
    // retrieve all children of $current
    foreach ($questarray as $key => $data2) 
    {
        if ($data2[1]==$current) 
        {
            if (!$list_started) // only start a list if an item was found, and a list was not already started.
            {
                echo '<ul>';
                $list_started = true;
            }
            $data = $data2[0]; // notice that data2[0] is an array itself containing all information about the quests.
            if(checkaccess('quests', 'edit')) // Determine access and give proper links.
            {
                echo '<li><a href="./index.php?do=editquest&id='.$data[0].'">'.$key.'</a> ( '.$data[2].' )</li>';
            }
            else 
            {
                echo '<li><a href="./index.php?do=readquest&id='.$data[0].'">'.$key.'</a> ( '.$data[2].' )</li>';
            }
            display_children($questarray,$key);
        }
    }
    if ($list_started) 
    {
        echo '</ul>'; // only end the list if it was started (ie, if children were found).
    }
} 

function readquest()
{
    if(checkaccess('quests', 'read'))
    {
        if(!isset($_GET['id']))
        {
            echo '<p class="error">Error: No quest ID specified - Reverting to list quests</p>';
            listquests();
        }
        else
        {
            $id = mysql_real_escape_string($_GET['id']);
            $query = 'SELECT name, category, player_lockout_time, quest_lockout_time, prerequisite FROM quests WHERE id='.$id;
            $result = mysql_query2($query);
            $query2 = 'SELECT script FROM quest_scripts WHERE quest_id='.$id;
            $result2 = mysql_query2($query2);
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            echo 'Quest ID: '.$id."<br/>\n";
            echo 'Quest Name: '.$row['name']."<br/>\n";
            echo 'Quest Category: '.$row['category']."<br/>\n";
            echo 'Player Lockout Time: '.$row['player_lockout_time']."<br/>\n";
            echo 'Quest Lockout Time: '.$row['quest_lockout_time']."<br/>\n";
            echo 'Prerequisites: '.htmlspecialchars($row['prerequisite'])."<br/>\n";
            $row = mysql_fetch_array($result2, MYSQL_ASSOC);
            $script = str_replace("\n", "<br/>\n", $row['script']);
            echo '<hr/>';
            echo 'Quest Script:<br/>'.$script."<br/>\n";
        }
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    } 
}

function npcquests()
{
    if (checkaccess('quests', 'read'))
    {
        //Select all NPCs
        $query = 'SELECT c.id, c.name, c.lastname, s.name AS sector FROM characters AS c LEFT JOIN sectors AS s ON c.loc_sector_id = s.id WHERE account_id=9 AND racegender_id<22';
        if (isset($_GET['sort']))
        {
            if ($_GET['sort'] == 'npc')
            {
                $query = $query . ' ORDER BY c.name';
            }
            else if ($_GET['sort'] == 'sector')
            {
                $query = $query . ' ORDER BY sector, c.name';
            }
        }
        else
        {
            $query = $query . ' ORDER BY c.name';
        }
        $result = mysql_query2($query);
        $query = 'SELECT q.name, q.id, s.script FROM quests AS q LEFT JOIN quest_scripts AS s ON q.id=s.quest_id';
        $result_script = mysql_query2($query);
        echo '<table border="1"><tr><th><a href="./index.php?do=npcquests&amp;sort=npc">NPC Name</a></th><th><a href="./index.php?do=npcquests&amp;sort=sector">Sector</a></th><th>Quests</th><th>Starting Quests</th></tr>';
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $fullname = $row['name'];
            if ($row['lastname'] != "")
            {
                $fullname = $fullname . ' ' . $row['lastname'];
            }
            mysql_data_seek($result_script, 0);
            $namestring = '/'.$fullname.'\:/ims';
            while($scripts = mysql_fetch_array($result_script, MYSQL_ASSOC))
            {
                $id = $scripts['id'];
                if (preg_match($namestring, $scripts['script']) == 1)
                {
                    $AllQuests["$fullname"]["$id"] = $scripts['name'];
                }
                $AllScripts["$id"] = $scripts['script'];
            }

            if (checkaccess('npc', 'edit')) 
            {
                echo '<tr><td><a href="./index.php?do=npc_details&sub=main&npc_id='.$row['id'].'">'.$fullname.'</a> - ';
            }
            else 
            {
                echo '<tr><td>'.$fullname.' - ';
            }
            echo '</td><td>'.$row['sector'].'</td><td>';
            if(isset($AllQuests["$fullname"]))
            {
                foreach($AllQuests["$fullname"] as $Q_ID => $Q_name)
                {
                    $string = '/'.$fullname.'\:.*assign\040quest/ims';
                    if (preg_match($string, $AllScripts["$Q_ID"]) == 1)
                    {
                        $StartQuests["$Q_ID"] = $Q_name;
                    }
                    else
                    {
                        echo $Q_name . ' - <a href="./index.php?do=readquest&amp;id='.$Q_ID.'">Read</a>';
                        if (checkaccess('quests', 'edit'))
                        {
                            echo ' - <a href="./index.php?do=editquest&amp;id='.$Q_ID.'">Edit</a>';
                        }
                        echo '<br/>';
                    }
                }
            }
            echo '</td><td>';
            if (isset($StartQuests))
            {
                foreach($StartQuests as $q_id => $q_name)
                {
                    echo $q_name . ' - <a href="./index.php?do=readquest&amp;id='.$q_id.'">Read</a>';
                    if (checkaccess('quests', 'edit'))
                    {
                        echo ' - <a href="./index.php?do=editquest&amp;id='.$q_id.'">Edit</a>';
                    }
                    echo '<br/>';
                }
            }
            unset($StartQuests);
            echo '</td></tr>'."\n";
        }
        echo '</table>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function countquests()
{
    if (checkaccess('quests', 'read'))
    {
        $query = "SELECT category, COUNT(category) AS count FROM quests GROUP BY category";
        $result = mysql_query2($query);
        echo '<hr/><p>Quest Counts:';
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            echo '<br/>Category: '.$row['category'].' - '.$row['count'];
        }
        echo '</p>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
?>
