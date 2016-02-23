<?PHP
function listtips()
{
	if (!checkaccess('admin', 'read')) 
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    $query = 'SELECT id, tip FROM tips WHERE id < 1000 ORDER BY id';
    $result = mysql_query2($query);
    echo '<h3>Listing Tips</h3>';
    printTable($result);
    
    $query = 'SELECT id, tip FROM tips WHERE id >= 1000 ORDER BY id';
    $result = mysql_query2($query);
    echo '<h3>Listing Tutorial Tips</h3>';
    printTable($result);

    if(checkaccess('admin', 'create')) 
    {
        echo '<h3>Add a new enty </h3>'."\n";
        echo '<div class="table">'."\n";
        echo '<div class="tr"><div class="td">ID (leave empty for auto)</div><div class="td">Tip</div><div class="td"></div></div>'."\n";
        echo '<form action="./index.php?do=edittips" method="post" class="tr"><div class="td"><input type="text" name="id"/></div><div class="td">'."\n";
        echo '<textarea cols="50" rows="2" name="tip"></textarea>'."\n";
        echo '</div><div class="td">'."\n";
        echo '<input type="submit" name="action" value="Add"/>'."\n";
        echo '</div></form>'."\n"; // end tr
        echo '</div>'."\n"; // end table
    }
}

// internal support function, expects 1 result set containing "ID" and "tip" fields.
function printTable($result)
{
    echo '<div class="table">';
    while ($row = fetchSqlAssoc($result))
    {
        echo '<form action="./index.php?do=edittips" method="post" class="tr"><div class="td">'.$row['id'].'</div><div class="td">'."\n";
        if(checkaccess('admin', 'edit')) 
        {
            echo '<textarea cols="50" rows="4" name="tip">'.htmlentities($row['tip']).'</textarea>'."\n";
            echo '<input type="hidden"  name="id" value ="'.$row['id'].'" />'."\n";
            echo '</div><div class="td">'."\n";
            echo '<input type="submit" name="action" value="Save" />'."\n";
            if(checkaccess('admin', 'delete')) 
            {
                echo '<br/><input type="submit" name="action" value="Delete" />'."\n";
            }
        }
        else 
        {
            echo $row['tip'].'</div><div class="td">'."\n";
        }
        echo '</div></form>'."\n"; // end tr
    }
    echo '</div>'."\n"; // end table
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
        $tip = escapeSqlString($_POST['tip']);
    }
    if (isset($_POST['id']))
    {
        $id = escapeSqlString($_POST['id']);
    }
    if ((trim($tip) == '' && !isset($_POST['submit'])) || (!is_numeric($id) && $action != 'Add'))
    {
        echo '<p class="error">Tip or ID invalid, no action has been performed.</p>';
        return;
    }
    
	if ($action == 'Add'){
		if (checkaccess('admin', 'create'))
		{
            if ($id != '' && is_numeric($id))
            {
                mysql_query2("INSERT INTO tips (id, tip) VALUES ('$id', '$tip')");
            }
            else
            {
                mysql_query2("INSERT INTO tips (tip) VALUES ('$tip')");
            }
            echo '<p class="error">Tip was successfully added.</p>'."\n";
            unset($_POST);
            listtips();
		}
	
	}
	elseif ($action == 'Save'){
		if (checkaccess('admin', 'edit'))
        {
            mysql_query2("UPDATE tips SET tip = '$tip' WHERE id = '$id'");
            echo '<p class="error">Tip was successfully edited.</p>'."\n";
            unset($_POST);
            listtips();
        }
	
	}
    elseif ($action == 'Delete' && isset($_POST['submit']) && $_POST['submit'] == 'Confirm Delete')
    {
        if (checkaccess('admin', 'delete'))
        {
            mysql_query2("DELETE FROM tips WHERE id ='$id' LIMIT 1");
            echo '<p class="error">Tip was successfully deleted.</p>'."\n";
            unset($_POST);
            listtips();
        }
    }
	elseif ($action == 'Delete')
    {
        if (checkaccess('admin', 'delete'))
        {
            echo '<p class="error">Warning, you are about to permanently delete the following Tip:</p>'."\n";
            echo '<p>'.htmlentities($tip).'</p>'."\n";
            echo '<p>Are you sure?</p>'."\n";
            echo '<form action="./index.php?do=edittips" method="post"><div><input type="hidden" name="action" value="Delete"/>'."\n";
            echo '<input type="hidden" name="id" value="'.$id.'"/>'."\n";
            echo '<input type="submit" name="submit" value="Confirm Delete"/></div></form>'."\n";
		}
	}
	else
    {
        echo '<p class="error">Invalid action or insufficient access.</p>';
    }
}	

?>
