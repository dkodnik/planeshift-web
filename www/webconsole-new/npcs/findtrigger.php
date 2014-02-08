<?php
function findtrigger(){

    if (checkaccess('npcs', 'read') && isset($_POST['commit']))
    {
        $word = $_POST['word'];

        $query = "SELECT id,trigger_text,area FROM npc_triggers WHERE trigger_text='$word' OR trigger_text LIKE '% $word %' OR trigger_text LIKE '$word %' OR trigger_text LIKE '% $word'";
        $result = mysql_query2($query);

        if (mysql_num_rows($result) == 0)
        {
            echo '<p class="error">No results were found.</p>';
            return;
        }
        echo '<b>Triggers found</b><br><br>';
        echo '<table border=1><th>ID</th><th>Trigger</th><th>Area</th>';
        while ($line = mysql_fetch_array($result, MYSQL_NUM)){
            echo '<TR><TD><b>'.$line[0].'</b></TD><TD>'.$line[1].'</TD><TD><a href="./index.php?do=ka_detail&area='.$line[2].'">'.$line[2].'</a></TD></TR>';
        }
        echo '</TABLE><br>';
    }
    else if (checkaccess('npcs', 'read'))
    {
        echo '<FORM ACTION="./index.php?do=findtrigger" METHOD="POST" >';
        echo '<br><TABLE BORDER=0>';
        echo '<tr><td>Enter the word to search for: </td><td><INPUT TYPE=text NAME=word>';
        echo '<INPUT TYPE="SUBMIT" NAME="commit" VALUE="Search"></td></tr>';
        echo '</TABLE></FORM>';

    echo '<br><br>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

?>