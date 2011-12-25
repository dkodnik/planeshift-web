<?php

function listtribemembers()
{
    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    
    $query = 'SELECT tm.tribe_id, t.name AS tribe_name, tm.member_id, tm.member_type, c.name FROM tribe_members AS tm LEFT JOIN characters AS c ON c.id=tm.member_id LEFT JOIN tribes AS t ON t.id=tm.tribe_id';

    if (isset($_GET['id']) && is_numeric($_GET['id'])) 
    {
        $id = mysql_real_escape_string($_GET['id']);
        $query .= " WHERE tm.tribe_id='$id'";
    }
    
    $query .= ' ORDER BY c.name';
    
    $result = mysql_query2($query);
    if (mysql_num_rows($result) > 0)
    {
        echo '<table border="1">';
        echo '<tr><th>Tribe</th><th>Member Name</th><th>Member Type</th></tr>';
        
        while ($row = mysql_fetch_array($result))
        {
            echo '<tr>';
            echo '<td>'.$row['tribe_name'].'</td>';
            echo '<td><a href="./index.php?do=npc_details&amp;sub=main&amp;npc_id='.$row['member_id'].'">'.$row['name'].'</a></td>';
            echo '<td>'.$row['member_type'].'</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    else
    {
        echo '<p class="error">No Tribe Members Found</p>';
    }
}

?>
