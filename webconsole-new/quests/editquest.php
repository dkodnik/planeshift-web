<?php
function editquest()
{
    if(checkaccess('quests', 'edit'))
    {
        if(!isset($_GET['id']))
        {
            echo '<p class="error">Error: No quest ID specified - Reverting to list quests</p>';
            include("listquests.php");
            listquests();
        }   
        else if(!isset($_GET['commit']))
        {
            $id = escapeSqlString($_GET['id']);
            $query = 'SELECT name, category, player_lockout_time, quest_lockout_time, prerequisite, task FROM quests WHERE id='.$id;
            $result = mysql_query2($query);
            $query2 = 'SELECT script FROM quest_scripts WHERE quest_id='.$id;
            $result2 = mysql_query2($query2);
            $row = fetchSqlAssoc($result);
            $row2 = fetchSqlAssoc($result2);
            $script = $row2['script'];
            $return_url = '';
            if (isset($_GET['sort']) && isset($_GET['direction']))
            {
                $return_url = '&amp;sort='.htmlentities($_GET['sort']).'&amp;direction='.htmlentities($_GET['direction']);
            }
            if (isset($_GET['matchType']) && isset($_GET['searchText']))
            {
                $return_url .= '&amp;matchType='.htmlentities($_GET['matchType']).'&amp;searchText='.htmlentities($_GET['searchText']);
            }
            echo '<form action="./index.php?do=editquest&amp;id='.$id.'&amp;commit'.$return_url.'" method="post"><div><table border="0">';
            echo '<tr><td>Quest ID:</td><td> '.$id."</td></tr>\n";
            echo '<tr><td>Quest Name:</td><td> <input type="text" name="name" value="'.$row['name'].'" />'."</td></tr>\n";
            echo '<tr><td>Quest Category:</td><td> <input type="text" name="category" value="'.$row['category'].'" />'."</td></tr>\n";
            echo '<tr><td>Quest Description:</td><td> <textarea name="task" rows="2" cols="45">'.$row['task']."</textarea></td></tr>\n";
            echo '<tr><td>Player Lockout Time:</td><td> <input type="text" name="player_lockout_time" value="'.$row['player_lockout_time'].'" />'."</td></tr>\n";
            echo '<tr><td>Quest Lockout Time:</td><td> <input type="text" name="quest_lockout_time" value="'.$row['quest_lockout_time'].'" />'."</td></tr>\n";
            echo '<tr><td>Prerequisites:</td><td> <textarea name="prerequisite" rows="2" cols="50">'.htmlentities($row['prerequisite'])."</textarea></td></tr>\n";
            echo '</table></div><hr/>';
            echo '<p>Quest Script:<br/><textarea name="script" rows="25" cols="80">'.htmlentities($script)."</textarea><br />\n";
            echo '<input type="submit" name="submit" value="Update Quest" /><input type="submit" name="submit2" value="save and continue editing" />';
            echo '</p></form>';
        }
        else
        {
            $id = escapeSqlString($_GET['id']);
            $name = escapeSqlString($_POST['name']);
            $category = escapeSqlString($_POST['category']);
            $task = escapeSqlString($_POST['task']);
            $player_lockout_time = escapeSqlString($_POST['player_lockout_time']);
            $quest_lockout_time = escapeSqlString($_POST['quest_lockout_time']);
            $prerequisite = escapeSqlString($_POST['prerequisite']);
            $query = "UPDATE quests SET name='$name', category='$category', task='$task', player_lockout_time='$player_lockout_time', quest_lockout_time='$quest_lockout_time', prerequisite='$prerequisite' WHERE id='$id'";
            $result = mysql_query2($query);
            $script = escapeSqlString($_POST['script']);
            $query = "UPDATE quest_scripts SET script='$script' WHERE quest_id='$id'";
            $result = mysql_query2($query);
            if (isset($_POST['submit2']))
            {
                unset($_GET['commit']);
                editquest();
            }
            else
            {
                include "listquests.php";
                listquests();
            }
        }
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
?>
