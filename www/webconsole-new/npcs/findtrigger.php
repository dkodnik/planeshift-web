<?php
function findtrigger(){

    if (checkaccess('npcs', 'read') && isset($_POST['commit']))
    {
        $word = escapeSqlString($_POST['word']);

        if ($_POST['commit'] == 'Search Trigger')
        {
            $query = "SELECT nt.id, trigger_text, area, response1, response2, response3, response4, response5 FROM npc_triggers AS nt LEFT JOIN npc_responses AS nr ON nt.id=trigger_id WHERE trigger_text='$word' OR trigger_text LIKE '% $word %' OR trigger_text LIKE '$word %' OR trigger_text LIKE '% $word'";
        }
        else if ($_POST['commit'] == 'Search Response')
        {
            $query = "SELECT nt.id, trigger_text, area , response1, response2, response3, response4, response5 FROM npc_triggers AS nt LEFT JOIN npc_responses AS nr ON nt.id=trigger_id WHERE response1='$word' OR response1 LIKE '% $word %' OR response1 LIKE '$word %' OR response1 LIKE '% $word'";
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
        $query .= " GROUP BY id";
        $result = mysql_query2($query);

        if (sqlNumRows($result) == 0)
        {
            echo '<p class="error">No results were found.</p>';
            return;
        }
        echo '<b>Triggers found</b><br /><br />';
        echo '<table border="1"><tr><th>ID</th><th>Trigger</th><th>Area</th>'.(isset($_POST['show_responses']) ? '<th>response1</th><th>response2</th><th>response3</th><th>response4</th><th>response5</th>' : '').'</tr>';
        while ($line = fetchSqlAssoc($result)){
            echo '<tr><td><b>'.htmlentities($line['id']).'</b></td><td><a href="./index.php?do=ka_detail&amp;area='.htmlentities($line['area']).'&amp;trigger='.htmlentities($line['id']).'">'.htmlentities($line['trigger_text']).
                '</a></td><td><a href="./index.php?do=ka_detail&amp;area='.htmlentities($line['area']).'">'.htmlentities($line['area']).'</a></td>'.(isset($_POST['show_responses']) ? '<td>'.htmlentities($line['response1']).'</td><td>'.
                htmlentities($line['response2']).'</td><td>'.htmlentities($line['response3']).'</td><td>'.htmlentities($line['response4']).'</td><td>'.htmlentities($line['response5']).'</td>' : '').'</tr>'."\n";
        }
        echo '</table>';
    }
    else if (checkaccess('npcs', 'read'))
    {
        echo '<p>Search Trigger</p>';
        echo '<form action="./index.php?do=findtrigger" method="post" >';
        echo '<table border="0">';
        echo '<tr><td>Enter the word to search for: </td><td><input type="text" name="word" /></td></tr>';
        echo '<tr><td></td><td><input type="checkbox" name="show_responses" /> Show responses in results </td></tr>';
        echo '<tr><td></td><td><input type="submit" name="commit" value="Search Trigger" /></td></tr>';
        echo '</table></form>';
        
        echo '<p>Search response</p>';
        echo '<form action="./index.php?do=findtrigger" method="post" >';
        echo '<table border="0">';
        echo '<tr><td>Enter the word to search for: </td><td><input type="text" name="word" /></td></tr>';
        echo '<tr><td></td><td><input type="checkbox" name="show_responses" /> Show responses in results </td></tr>';
        echo '<tr><td></td><td><input type="submit" name="commit" value="Search Response" /></td></tr>';
        echo '</table></form>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

?>