<?PHP
function listtips(){

	if (checkaccess('admin', 'read')) 
    {
        $query = 'SELECT * FROM tips ORDER BY tip';
        $result = mysql_query2($query);
        echo'<table border="1">';
        while ($row = mysql_fetch_array($result))
        {
            echo '<tr><td>';
            if(checkaccess('admin', 'edit')) 
            {
                echo '<form action="./index.php?do=edittips" method="post">';
                echo '<textarea cols="50" rows="2" name="tip">'.$row['tip'].'</textarea>';
                echo '<input type="hidden"  name="id" value ="'.$row['id'].'"/>';
                echo '</td><td>';
                echo '<input type="submit" name="action" value="Save"/>';
                if(checkaccess('admin', 'delete')) 
                {
                    echo '<br/><input type="submit" name="action" value="Delete"/>';
                }
                echo '</form>';
            }
            else 
            {
                echo $row['tip'].'</td><td>';
            }
            echo '</td></tr>';
        }
        if(checkaccess('admin', 'create')) 
        {
            echo '<tr><td>';
            echo '<form action="./index.php?do=edittips" method="post">';
            echo '<textarea cols="50" rows="2" name="tip"></textarea>';
            echo '</td><td>';
            echo '<input type="submit" name="action" value="Add"/>';
//            echo '</td></tr>';
            echo '</form>';
        }
        echo '</td></tr>';
        echo '</table>';
	}
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function edittips(){
// check access, isset post, etc
    $action = '';
    $id = '';
    $tip = '';
    if (isset($_POST['action']))
    {
        $action = $_POST['action'];
    }

    if (isset($_POST['tip']))
    {
        $tip = mysql_real_escape_string($_POST['tip']);
    }
    if (isset($_POST['id']))
    {
        $id = mysql_real_escape_string($_POST['id']);
    }
    if ((trim($tip) == '' && !isset($_POST['submit'])) || (!is_numeric($id) && $action != 'Add'))
    {
        echo '<p class="error">Tip or ID invalid, no action has been performed.</p>';
        return;
    }
    
	if ($action == 'Add'){
		if (checkaccess('admin', 'create'))
		{
            mysql_query2("INSERT INTO tips (tip) VALUES ('$tip')");
            echo '<p class="error">Tip was successfully added.</p>';
            unset($_POST);
            listtips();
		}
	
	}
	elseif ($action == 'Save'){
		if (checkaccess('admin', 'edit'))
        {
            mysql_query2("UPDATE tips SET tip = '$tip' WHERE id = '$id'");
            echo '<p class="error">Tip was successfully edited.</p>';
            unset($_POST);
            listtips();
        }
	
	}
    elseif ($action == 'Delete' && isset($_POST['submit']) && $_POST['submit'] == 'Confirm Delete')
    {
        $password = mysql_real_escape_string($_POST['passd']);
        $username = mysql_real_escape_string($_SESSION['username']);
        $query = "SELECT COUNT(username) FROM accounts WHERE username='$username' AND password=MD5('$password')";
        $result = mysql_query2($query);
        $row = mysql_fetch_row($result);
        if ($row[0] == 1)
        {
            mysql_query2("DELETE FROM tips WHERE id ='$id' LIMIT 1");
            echo '<p class="error">Tip was successfully deleted.</p>';
            unset($_POST);
            listtips();
        }
        else
        {
            echo '<p class="error">Password check failed - Did Not Delete Tip</p>';
        }
    }
	elseif ($action == 'Delete')
    {
        if (checkaccess('admin', 'delete'))
        {
            echo '<p class="error">Warning, you are about to permanently delete the following Tip:</p>';
            echo '<p>'.$tip.'</p>';
            echo '<form action="./index.php?do=edittips" method="post"><input type="hidden" name="action" value="Delete"/><input type="hidden" name="id" value="'.$id.'"/>';
            echo 'Enter your password to confirm deletion the Tip listed above: <input type="password" name="passd"/><input type="submit" name="submit" value="Confirm Delete"/></form>';
		}
	}
	else
    {
        echo '<p class="error">Invalid action or insufficient access.</p>';
    }
}	

?>
