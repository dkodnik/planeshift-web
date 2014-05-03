<?php
function findtrigger(){

    if (checkaccess('npcs', 'read') && isset($_POST['commit']))
    {
        $word = mysql_real_escape_string($_POST['word']);

        if ($_POST['commit'] == 'Search Trigger')
        {
            $query = "SELECT id, trigger_text, area FROM npc_triggers WHERE trigger_text='$word' OR trigger_text LIKE '% $word %' OR trigger_text LIKE '$word %' OR trigger_text LIKE '% $word'";
        }
        else if ($_POST['commit'] == 'Search Response')
        {
            $query = "SELECT nt.id, trigger_text, area FROM npc_triggers AS nt LEFT JOIN npc_responses AS nr ON nt.id=trigger_id WHERE response1='$word' OR response1 LIKE '% $word %' OR response1 LIKE '$word %' OR response1 LIKE '% $word'";
            $query .= " OR response2='$word' OR response2 LIKE '% $word %' OR response2 LIKE '$word %' OR response2 LIKE '% $word'";
            $query .= " OR response3='$word' OR response3 LIKE '% $word %' OR response3 LIKE '$word %' OR response3 LIKE '% $word'";
            $query .= " OR response4='$word' OR response4 LIKE '% $word %' OR response4 LIKE '$word %' OR response4 LIKE '% $word'";
            $query .= " OR response5='$word' OR response5 LIKE '% $word %' OR response5 LIKE '$word %' OR response5 LIKE '% $word'";
        }
        else
        {
            echo '<p class="error">Should not reach this.</p>';
            return;
        }
        $result = mysql_query2($query);

        if (mysql_num_rows($result) == 0)
        {
            echo '<p class="error">No results were found.</p>';
            return;
        }
        echo '<b>Triggers found</b><br /><br />';
        echo '<table border="1"><tr><th>ID</th><th>Trigger</th><th>Area</th></tr>';
        while ($line = mysql_fetch_array($result, MYSQL_NUM)){
            echo '<tr><td><b>'.htmlentities($line[0]).'</b></td><td>'.htmlentities($line[1]).'</td><td><a href="./index.php?do=ka_detail&amp;area='.htmlentities($line[2]).'">'.htmlentities($line[2]).'</a></td></tr>';
        }
        echo '</table>';
    }
    else if (checkaccess('npcs', 'read'))
    {
        echo '<p>Search Trigger</p>';
        echo '<form action="./index.php?do=findtrigger" method="post" >';
        echo '<table border="0">';
        echo '<tr><td>Enter the word to search for: </td><td><input type="text" name="word" />';
        echo '<input type="submit" name="commit" value="Search Trigger" /></td></tr>';
        echo '</table></form>';
        
        echo '<p>Search response</p>';
        echo '<form action="./index.php?do=findtrigger" method="post" >';
        echo '<table border="0">';
        echo '<tr><td>Enter the word to search for: </td><td><input type="text" name="word" />';
        echo '<input type="submit" name="commit" value="Search Response" /></td></tr>';
        echo '</table></form>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

?>