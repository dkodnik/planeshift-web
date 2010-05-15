<?php
function deletequest()
{
    if (checkaccess('quests', 'delete'))
    {
        if (!isset($_GET['id']))
        {
            echo '<p class="error">Quest ID not specified, returning to quest list</p>';
            include('./quests/listquests.php');
            listquests();
        }
        else
        {
            if (!isset($_GET['commit']))
            {
                $id = mysql_real_escape_string($_GET['id']);
                $query = 'SELECT name, task FROM quests WHERE id='.$id;
                $result = mysql_query2($query);
                $row = mysql_fetch_array($result, MYSQL_ASSOC);
                echo '<p> you are about to permanently delete quest '.$id.' from the database<br />';
                echo 'Quest Name: '.$row['name'].'<br/>Quest Description: '.$row['task'].'</p>';
                echo '<form action="./index.php?do=deletequest&amp;id='.$id.'&amp;commit" method="post">';
                echo '<p>Enter your password to confirm: <input type="password" name="pwd" /><input type="submit" name="submit" value="Confirm Delete" /></p></form>';
            }
            else
            {
                //First we double check the password
                $id = mysql_real_escape_string($_GET['id']);
                $password=mysql_real_escape_string($_POST['pwd']);
                $username=$_SESSION['username'];
                $query = "SELECT count(*) FROM accounts WHERE username='$username' AND password=MD5('$password')";
                $result = mysql_query2($query);
                $row = mysql_fetch_row($result);
                if ($row[0] == 1)
                {
                    echo '<p class="error">Quest ID: '.$id.' has been removed from the database - Returning to Quest Listing</p>';
                    $query = 'DELETE FROM quests WHERE id ='.$id;
                    $result = mysql_query2($query);
                    $query = 'DELETE FROM quest_scripts WHERE quest_id ='.$id;
                    $result = mysql_query2($query);
                    include('./quests/listquests.php');
                    listquests();
                }
                else
                {
                    echo '<p class="error">Password Incorrect - Delete aborted - returning to Quest List</p>';
                    include('./quests/listquests.php');
                    listquests();
                }
            } 
        }
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    } 
}
?>
