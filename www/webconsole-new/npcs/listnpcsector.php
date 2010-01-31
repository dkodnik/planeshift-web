<?php
function listnpcsector()
{
    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
  
    echo '<h1>List of invulnerable NPCs divided by sector</h1>';
    $query = "SELECT count(r.name) AS num, s.name AS sector, r.name AS race FROM characters c, race_info r, sectors s WHERE c.racegender_id=r.id AND s.id=c.loc_sector_id AND c.npc_impervious_ind='N' AND c.character_type='1' GROUP BY s.name,r.name ORDER BY s.name";    
    $result = mysql_query2($query);
    if (mysql_num_rows($result) > 0)
    {
        echo '<table border="1">';
        echo '<th>Count</th><th>Sector</th><th>Race</th>';
        while ($row = mysql_fetch_array($result))
        {
            echo '<tr>';
            echo '<td>'.$row['num'].'</td>';
            echo '<td>'.$row['sector'].'</td>';
            echo '<td>'.$row['race'].'</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    else
    {
        echo '</table>';
        echo '<p class="error">No NPCs Found</p>';
    }
}
?>
