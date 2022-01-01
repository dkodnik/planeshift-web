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

    $query = 'SELECT id, name, last_login, time_connected_sec FROM characters WHERE character_type=0 AND last_login<\''.$startfrom.'\' AND time_connected_sec <='. $secondsconnected . ' ORDER BY last_login asc';
    $result = mysql_query2($query);
    echo 'Total chars: '.mysqli_num_rows($result);

    printTable($result);

}

// internal support function, expects 1 result set containing "ID" and "tip" fields.
function printTable($result)
{
    echo '<div class="table"><div class="th">Character ID</div><div class="th">Character Name</div><div class="th">Last Login Date</div><div class="th">Time connected in seconds</div>';
    while ($row = fetchSqlAssoc($result))
    {
        echo '<form action="./index.php?do=unusedchars" method="post" class="tr"><div class="td">'.$row['id'].'</div><div class="td">'."\n";
        echo $row['name'].'</div><div class="td">';
        echo $row['last_login'].'</div><div class="td">';
        echo $row['time_connected_sec'].'</div>';
        echo '</form>'."\n"; // end tr
    }
    echo '</div>'."\n"; // end table
}

?>
