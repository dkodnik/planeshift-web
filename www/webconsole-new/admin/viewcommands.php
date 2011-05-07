<?PHP
function viewcommands()
{		
    if (checkaccess('admin', 'read')) 
    {
        $group = '';
        if (isset($_GET['group']))
        {
            $group = $_GET['group'];
        }
        $query = 'SELECT * FROM command_groups';
        $result = mysql_query2($query);
        echo'<table><tr><td valign="top"><table>';
        while ($row = mysql_fetch_array($result, MYSQL_NUM)){
            echo "<tr><td><a href='index.php?do=viewcommands&amp;group=".$row[0]."'>".$row[1]."</a></td></tr>";
        }
        echo'</table>';

        // get group name
        $query = "SELECT * FROM command_groups WHERE id ='$group'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_NUM);
        $groupname=$row[1];


        $query = "SELECT * FROM command_group_assignment WHERE group_member ='$group' ORDER BY command_name";
        $result = mysql_query2($query);
        echo'</td><td valign="top"><table border="1">';

        $found = false;
        echo"<tr><th><b>Group: $groupname</b></th><th>Action</th></tr>";
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
            $found = true;
            $actions = (checkaccess('admin', 'edit') ? '<a href="./index.php?do=deletecommand&amp;group='.$group.'&amp;command='.$row['command_name'].'">Delete</a>' : '');
            echo '<tr><td>'.$row['command_name'].'</td><td>'.$actions.'</td></tr>';
        }
        if(!$found && isset($_GET['group']))
        echo "<tr><td><P>No commands found in this group</P></td></tr>";

        echo '</table>';
        echo '<form action="./index.php?do=createcommand&amp;group='.$group.'" method="post">';
        echo '<input type="text" name="command"/><input type="submit" name="create" value="Add new Command"/></form>';
        echo '</td></tr></table>';
    }
}

function deletecommand() 
{
    if (checkaccess('admin', 'edit')) 
    {
        if (isset($_GET['group']) && isset($_GET['command']))
        {
            $group = mysql_real_escape_string($_GET['group']);
            $command = mysql_real_escape_string($_GET['command']);
            $query = "DELETE FROM command_group_assignment WHERE command_name='$command' AND group_member='$group' LIMIT 1";
            mysql_query2($query);
            echo '<p class="error">update succesful</p>';
            viewcommands();
        }
        else
        {
            echo '<p class="error">Missing parameters in delete operation! No action was taken!</p>';
        }
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function createcommand()
{
    if (checkaccess('admin', 'edit')) 
    {
        if (isset($_GET['group']) && isset($_POST['command']))
        {
            $group = mysql_real_escape_string($_GET['group']);
            $command = mysql_real_escape_string($_POST['command']);
            $query = "INSERT INTO command_group_assignment (command_name, group_member) VALUES ('$command', '$group')";
            mysql_query2($query);
            echo '<p class="error">update succesful</p>';
            viewcommands();
        }
        else
        {
            echo '<p class="error">Missing parameters in create operation! No action was taken!</p>';
        }
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}


?>
