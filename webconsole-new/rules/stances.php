<?php

function liststances()
{
    if (!checkaccess('natres', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    $query = 'SELECT * FROM stances';
    $result = mysql_query2($query);
    $alt = false;
    echo "<table>\n";
    echo '<tr><th>ID</th><th>name</th><th>stamina drain P</th><th>stamina drain M</th><th>attack speed mod</th><th>attack damage mod</th><th>defense avoid mod</th><th>defense absorb mod</th>';
    if (checkaccess('natres', 'edit'))
    {
        echo '<th>actions</th>';
    }
    echo "</tr>\n";
    while($row = mysql_fetch_array($result, MYSQL_ASSOC))
    {
        echo '<tr class="color_'.(($alt = !$alt) ? 'a' : 'b').'">';
        echo '<td>'.$row['id'].'</td>';
        echo '<td>'.$row['name'].'</td>';
        echo '<td>'.$row['stamina_drain_P'].'</td>';
        echo '<td>'.$row['stamina_drain_M'].'</td>';
        echo '<td>'.$row['attack_speed_mod'].'</td>';
        echo '<td>'.$row['attack_damage_mod'].'</td>';
        echo '<td>'.$row['defense_avoid_mod'].'</td>';
        echo '<td>'.$row['defense_absorb_mod'].'</td>';
        if (checkaccess('natres', 'edit'))
        {
            echo '<td><a href="./index.php?do=editstances&amp;id='.$row['id'].'">Edit</a></td>';
        }
        echo "</tr>\n";
    }
    echo "</table>\n";
}

function editstances()
{
    if (!checkaccess('natres', 'edit'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    $id = -1;
    if (isset($_GET['id']))
    {
        $id = mysql_real_escape_string($_GET['id']);
    }
    elseif (isset($_POST['id']))
    {
        $id = mysql_real_escape_string($_POST['id']);
    }
    else
    {
        echo '<p class="error">Cannot edit without ID</p>';
        return;
    }
    if (!is_numeric($id) || 0 > $id)
    {
        echo '<p class="error">Invalid ID</p>';
        return;
    }
    if (isset($_POST['commit']) && ($_POST['commit'] == "Update Stance"))
    {
        $stamina_drain_P = mysql_real_escape_string($_POST['stamina_drain_P']);
        $stamina_drain_M = mysql_real_escape_string($_POST['stamina_drain_M']);
        $attack_speed_mod = mysql_real_escape_string($_POST['attack_speed_mod']);
        $attack_damage_mod = mysql_real_escape_string($_POST['attack_damage_mod']);
        $defense_avoid_mod = mysql_real_escape_string($_POST['defense_avoid_mod']);
        $defense_absorb_mod = mysql_real_escape_string($_POST['defense_absorb_mod']);
        $query = "UPDATE stances SET stamina_drain_P='$stamina_drain_P', stamina_drain_M='$stamina_drain_M', attack_speed_mod='$attack_speed_mod', attack_damage_mod='$attack_damage_mod', defense_avoid_mod='$defense_avoid_mod', defense_absorb_mod='$defense_absorb_mod' WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        editstances();
    }
    else
    {
        $query = "SELECT * FROM stances WHERE id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<p class="header">Edit Stance</p>';
        echo '<form action="./index.php?do=editstances&amp;id='.$id.'" method="post">';
        echo "<table>\n";
        echo "<tr><th>Field</th><th>Value</th></tr>\n";
        echo '<tr><td>ID</td><td>'.$row['id']."</td></tr>\n";
        echo '<tr><td>name</td><td>'.$row['name']."</td></tr>\n";
        echo '<tr><td>stamina drain P</td><td><input type="text" name="stamina_drain_P" value="'.$row['stamina_drain_P'].'"></td></tr>'."\n";
        echo '<tr><td>stamina drain M</td><td><input type="text" name="stamina_drain_M" value="'.$row['stamina_drain_M'].'"></td></tr>'."\n";
        echo '<tr><td>attack speed mod</td><td><input type="text" name="attack_speed_mod" value="'.$row['attack_speed_mod'].'"></td></tr>'."\n";
        echo '<tr><td>attack damage mod</td><td><input type="text" name="attack_damage_mod" value="'.$row['attack_damage_mod'].'"></td></tr>'."\n";
        echo '<tr><td>defense avoid mod</td><td><input type="text" name="defense_avoid_mod" value="'.$row['defense_avoid_mod'].'"></td></tr>'."\n";
        echo '<tr><td>defense absorb mod</td><td><input type="text" name="defense_absorb_mod" value="'.$row['defense_absorb_mod'].'"></td></tr>'."\n";
        echo '<tr><td></td><td><input type="submit" name="commit" value="Update Stance"/></td></tr>'."\n";
        echo "</table></form>\n";
    }
}

?>