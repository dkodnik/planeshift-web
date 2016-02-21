<?php
function ka_scripts()
{
    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    if (!isset($_GET['sub']))
    {
        $query = "SELECT id, script FROM quest_scripts WHERE quest_id='-1'";
        $result = mysql_query2($query);
        echo '<table border="1">';
        echo '<tr><th>Name</th><th>Action</th></tr>';
        while ($row = fetchSqlAssoc($result))
        {
            $pos1 = strpos($row['script'], "\n")+1;
            $string = substr($row['script'], $pos1);
            $pos2 = strpos($string, ":");
            $name = substr($string, 0, $pos2);   
            echo '<tr><td>'.htmlentities($name).'</td>';
            echo '<td><a href="./index.php?do=ka_scripts&amp;sub=Read&amp;areaid='.$row['id'].'">Read</a>';
            if (checkaccess('npcs', 'edit'))
            {
                echo '<br/><a href="./index.php?do=ka_scripts&amp;sub=Edit&amp;areaid='.$row['id'].'">Edit</a>';
                echo '<br/><a href="./index.php?do=ka_scripts&amp;sub=Delete&amp;areaid='.$row['id'].'">Delete</a>';
            }
            echo '</td></tr>';
        }
        if (checkaccess('npcs', 'edit'))
        {
            echo '<tr><td><form action="./index.php?do=ka_scripts&amp;sub=New" method="post">';
            echo '<div><input type="text" name="name" /><br/><input type="submit" name="commit" value="Create New KA Script" /></div>';
            echo '</form></td><td>&nbsp;</td></tr>';
        }
      echo '</table>';
    }
    else
    {
        if (!checkaccess('npcs', 'edit')) 
        {
            echo '<p class="error">Error: You are not authorized to use this function.</p>';
            return;
        }

        $areaid = (isset($_GET['areaid']) ? escapeSqlString($_GET['areaid']) : '');

        if ($_GET['sub'] == 'Read')
        {
            $query = "SELECT script FROM quest_scripts WHERE id='$areaid'";
            $result = mysql_query2($query);
            $row = fetchSqlAssoc($result);
            $pos1 = strpos($row['script'], "\n")+1;
            $string = substr($row['script'], $pos1);
            $pos2 = strpos($string, ":");
            $name = substr($string, 0, $pos2);
            echo 'Reading KA Script: '.htmlentities($name).'<hr/>';
            $script = str_replace("\n", "<br/>\n", htmlentities($row['script']));
            echo $script.'<br/>';
        }
        elseif ($_GET['sub'] == 'Edit')
        {
            if (isset($_POST['commit']))
            {
                $script = escapeSqlString($_POST['script']);
                $query = "UPDATE quest_scripts SET script='$script' WHERE id='$areaid'";
                $result = mysql_query2($query);
                unset($_POST);
                unset($_GET);
                ka_scripts();
                return;
            }
            else
            {
                $query = "SELECT script FROM quest_scripts WHERE id='$areaid'";
                $result = mysql_query2($query);
                $row = fetchSqlAssoc($result);
                $pos1 = strpos($row['script'], "\n")+1;
                $string = substr($row['script'], $pos1);
                $pos2 = strpos($string, ":");
                $name = substr($string, 0, $pos2);
                echo 'Editing KA Script: '.htmlentities($name).'<hr/>';
                echo '<form action="./index.php?do=ka_scripts&amp;sub=Edit&amp;areaid='.htmlentities($areaid).'" method="post">';
                echo '<div><textarea name="script" rows="20" cols="70">'.htmlentities($row['script']).'</textarea><br/>';
                echo '<input type="submit" name="commit" value="Update Script"/></div>';
                echo '</form>';
            }
        }
        elseif ($_GET['sub'] == 'Delete')
        {
            if (isset($_POST['commit']))
            {
                $query = "DELETE FROM quest_scripts WHERE id='$areaid'";
                $result = mysql_query2($query);
                unset($_POST);
                unset($_GET);
                ka_scripts();
                return;
            }
            else
            {
                $query = "SELECT script FROM quest_scripts WHERE id='$areaid'";
                $result = mysql_query2($query);
                $row = fetchSqlAssoc($result);
                $pos1 = strpos($row['script'], "\n")+1;
                $string = substr($row['script'], $pos1);
                $pos2 = strpos($string, ":");
                $name = substr($string, 0, $pos2);
                echo '<p>You are about to delete the following KA Script: '.htmlentities($name).'</p>';
                echo '<form action="./index.php?do=ka_scripts&amp;sub=Delete&amp;areaid='.htmlentities($areaid).'" method="post">';
                echo '<div><input type="submit" name="commit" value="Confirm Delete"/></div></form>';
            }
        }
        elseif ($_GET['sub'] == 'New')
        {
            $name = escapeSqlString($_POST['name']);
            if ($name == '') // should only happen if people are fooling around with their own forms/post variables.
            {
                echo '<p class="error">cannot create KA without a name.</p>';
                return;
            }
            $script = " \n"."$name:\n#This is a temporary Entry -Needs to be changed";
            $query = "INSERT INTO quest_scripts (quest_id, script) VALUES ('-1', '$script')";
            $result = mysql_query2($query);
            unset($_GET);
            unset($_POST);
            $_GET['sub'] = 'Edit';
            $_GET['areaid'] = sqlInsertId();
            ka_scripts();
            return;
        }
        else
        {
            echo '<p class="error">Error: No action specified</p>';
        }
    }
}
?>