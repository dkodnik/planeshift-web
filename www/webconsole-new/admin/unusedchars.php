<?PHP
function unusedchars()
{
	if (!checkaccess('admin', 'read')) 
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }

    if(isset($_POST['startfrom']))
    {
        $startfrom = $_POST['startfrom'];
    } else
    {
        $startfrom = "2008-01-01";
    }
    
    if(isset($_POST['secondsconnected']))
    {
        $secondsconnected = $_POST['secondsconnected'];
    } else 
    {
        $secondsconnected = 0;
    }

    echo '<h3>Unused Characters Search</h3>';
    
    echo '<div class="table"><div class="th">Date of last login</div><div class="th">Time connected in seconds</div>';
    echo '<form action="./index.php?do=cleanupchars" method="post" class="tr"><div class="td"><input type="text" name="startfrom" value="'.$startfrom.'"/></div><div class="td">'."\n";
    echo '<input type="text" name="secondsconnected" value="'.$secondsconnected.'"/></div><div class="td">'."\n";
    echo '</div><div class="td">'."\n";
    echo '<input type="submit" name="action" value="Search"/>'."\n";
    echo '</div></div></form>'."\n"; // end tr
    echo '<br/><br/>';

    $query = 'SELECT id, name, last_login, time_connected_sec, money_circles + money_octas + money_hexas + money_trias as coins FROM characters WHERE character_type=0 AND last_login<\''.$startfrom.'\' AND time_connected_sec <='. $secondsconnected . ' ORDER BY last_login asc';
    $result = mysql_query2($query);
    echo 'Total chars: '.mysqli_num_rows($result);

    printTable($result);

}

// internal support function, expects 1 result set containing "ID" and "tip" fields.
function printTable($result)
{
    $idStr = '';
    echo '<div class="table"><div class="th">Character ID</div><div class="th">Character Name</div><div class="th">Last Login Date</div><div class="th">Time connected in seconds</div><div class="th">Coins</div><div class="th">Items</div>';
    while ($row = fetchSqlAssoc($result))
    {
        $query = 'SELECT count(*) as count FROM item_instances WHERE char_id_owner='.$row['id'];
        $items = mysql_query2($query);
        $items_row = fetchSqlAssoc($items);

        echo '<form action="./index.php?do=unusedchars" method="post" class="tr"><div class="td">'.$row['id'].'</div><div class="td">'."\n";
        echo $row['name'].'</div><div class="td">';
        echo $row['last_login'].'</div><div class="td">';
        echo $row['time_connected_sec'].'</div><div class="td">';
        echo $row['coins'].'</div><div class="td">';
        echo $items_row['count'].'</div>';
        echo '</form>'."\n"; // end tr
        
        // IDs
        if ($row['coins'] == 0 && $items_row['count'] == 0)
        {
            $idStr = $idStr . $row['id'] . ',';
        }
    }
    echo '</div>'."\n"; // end table
    
    // generate SQL to delete
    echo '</br><h3>SQL to delete the characters above, excluding the ones with items or coins </h3>';
    
    echo 'delete from character_discoveries where character_id IN ('.$idStr.'); <br/>';
    echo 'delete from character_events where player_id IN ('.$idStr.'); <br/>';
    echo 'delete from character_factions where character_id IN ('.$idStr.'); <br/>';
    echo 'delete from character_glyphs where player_id IN ('.$idStr.'); <br/>';
    echo 'delete from character_quests where player_id IN ('.$idStr.'); <br/>';
    echo 'delete from character_relationships where character_id IN ('.$idStr.'); <br/>';
    echo 'delete from character_skills where character_id IN ('.$idStr.'); <br/>';
    echo 'delete from character_traits where character_id IN ('.$idStr.'); <br/>';
    echo 'delete from character_variables where character_id IN ('.$idStr.'); <br/>';
    echo 'delete from item_instances where char_id_owner IN ('.$idStr.'); <br/>';
    echo 'delete from player_spells where player_id IN ('.$idStr.'); <br/>';
    echo 'delete from characters where id IN ('.$idStr.'); <br/>';
}

?>
