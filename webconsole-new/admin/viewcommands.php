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
            echo "<tr><td><a href='index.php?do=viewcommands&group=".$row[0]."'>".$row[1]."</a></td></tr>";
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
        echo"<TR><TH ><b>Group: $groupname</b></TH></TR>";
        while ($row = mysql_fetch_array($result, MYSQL_NUM)){
            $found = true;
            echo '<TR><TD>'.$row[0].'</TD></TR>';
            // There is nothing to edit with, so drop the link for now.
            //echo '<TR><TD><a href="index.php?do=cmd_actions&group='.$row[1].'&cmd='.$row[0].'">'.$row[0].'</a></TD></TR>'; 
            //echo"<TD ><P>$row[1] </P></TD></TR>"; 
        }
        
        if(!$found && isset($_GET['group']))
        echo "<TR><TD><P>No commands found in this group</P></TD></TR>";

        echo '</TABLE></td></tr></table>';
    }
}


?>
