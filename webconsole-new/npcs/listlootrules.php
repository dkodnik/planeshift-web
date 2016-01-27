<?php
function listlootrules()
{
    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
		return;
    }
	$query = 'SELECT l.id, lr.id AS loot_rule_id, l.item_stat_id, i.name AS item_name, l.min_item, l.max_item, l.probability, l.min_money, l.max_money, l.randomize, l.randomize_probability, lr.name FROM loot_rule_details AS l RIGHT JOIN loot_rules AS lr ON lr.id=l.loot_rule_id LEFT JOIN item_stats AS i ON i.id=l.item_stat_id';
	if (isset($_GET['id']))
	{
		$id = escapeSqlString($_GET['id']);
		$query .= ' WHERE lr.id='.$id;
	}
	$query .= ' ORDER BY lr.id';
	$result = mysql_query2($query);
	$rule_id = '';
	echo '<table border="1" cellspacing="0">';
	while ($row = fetchSqlAssoc($result))
	{
		$delete_text = '';
		if (checkaccess('npcs', 'delete'))
		{
			$delete_text = '<input type="submit" name="commit" value="Delete Rule" />';
		}
		if ($rule_id == '')
		{
			$rule_id = $row['loot_rule_id'];
			if (checkaccess('npcs', 'edit'))
			{
				echo '<tr><td colspan="9"><form action="./index.php?do=editlootrule" method="post">Rule # '.$rule_id.' named:  <input type="hidden" name="id" value="'.$row['loot_rule_id'].'"/> <input type="text" name="name" value="'.$row['name'].'" size="30"/> <input type="submit" name="commit" value="Change Name" />'.$delete_text.'</form></td></tr>';
			}
			else
			{
				echo '<tr><td colspan="9">Rule # '.$rule_id.' named: '.$row['name'].'</td></tr>';
			}
			echo '<tr><td>Item</td><td>Min Qty</td><td>Max Qty</td><td>Probability</td><td>Minimum Money</td><td>Maxiumum Money</td><td>Randomize</td><td>random percentage</td><td>actions</td></tr>';
		}
		elseif ($rule_id != $row['loot_rule_id'])
		{
			if (checkaccess('npcs', 'edit'))
			{
				echo '<tr><td colspan="9"><form action="./index.php?do=createlootruledetail&id='.$rule_id.'" method="post"><input type="submit" name="create" value="Add New Detail"></form></td></tr>';
			}
			echo '<tr><td colspan="9"></td></tr>';
			$rule_id = $row['loot_rule_id'];
			if (checkaccess('npcs', 'edit'))
			{
				echo '<tr><td colspan="9"><form action="./index.php?do=editlootrule" method="post"> Rule # '.$rule_id.' named: <input type="hidden" name="id" value="'.$row['loot_rule_id'].'"/> <input type="text" name="name" value="'.$row['name'].'" size="30"/> <input type="submit" name="commit" value="Change Name" />'.$delete_text.'</form></td></tr>';
			}
			else
			{
				echo '<tr><td colspan="9">Rule # '.$rule_id.' named: '.$row['name'].'</td></tr>';
			}
			echo '<tr><td>Item</td><td>Min Qty</td><td>Max Qty</td><td>Probability</td><td>Minimum Money</td><td>Maxiumum Money</td><td>Randomize</td><td>random percentage</td><td>actions</td></tr>';
		}
		if ($row['item_name'] == '' && $row['min_money'] == '' && $row['max_money'] == '') // empty item name means there are no details on this rule, so we display it's name, and bail out.
		{
			continue;
		}
		echo '<tr><td>'.$row['item_name'].'</td><td>'.$row['min_item'].'</td><td>'.$row['max_item'].'</td><td>'.$row['probability'].'</td><td>'.$row['min_money'].'</td><td>'.$row['max_money'].'</td><td>'.($row['randomize'] == 1 ? 'Yes' : 'No').'</td><td>'.$row['randomize_probability'].'</td><td>';
		if (checkaccess('npcs', 'edit'))
		{
			echo '<form action="./index.php?do=editlootruledetail&id='.$row['id'].'" method="post"><input type="submit" name="edit" value="Edit"><input type="submit" name="delete" value="Delete"></form>';
		}
		echo '</td></tr>';
	}
	if (sqlNumRows($result) > 0) // Check if there were results, if there were, we need to add the last "add details" function.
	{
		if (checkaccess('npcs', 'edit'))
		{
			echo '<tr><td colspan="9"><form action="./index.php?do=createlootruledetail&id='.$rule_id.'" method="post"><input type="submit" name="create" value="Add New Detail"></form></td></tr>';
		}
	}
	echo '<tr><td colspan="9"><form action="./index.php?do=editlootrule" method="post"><input type="hidden" name="id" value="0" />';
	echo '<input type="text" name="name" size="30" /><input type="submit" name="commit" value="Create New Rule"/></form></td></tr>';
	echo '</table>';

}

// This method handles the "edit and delete loot_rule_detail"
function editlootruledetail()
{
    if (checkaccess('npcs', 'edit') && isset($_POST['commit']) && $_POST['commit'] == 'Update Rule Detail')
    {
        $id = escapeSqlString($_POST['id']);
        $item_stat_id = escapeSqlString($_POST['item_stat_id']);
		$min_item = escapeSqlString($_POST['min_item']);
        $max_item = escapeSqlString($_POST['max_item']);
		$probability = escapeSqlString($_POST['probability']);
        $min_money = escapeSqlString($_POST['min_money']);
        $max_money = escapeSqlString($_POST['max_money']);
        $randomize = escapeSqlString($_POST['randomize']);
		$randomize_probability = escapeSqlString($_POST['randomize_probability']);
        $query = "UPDATE loot_rule_details SET item_stat_id='$item_stat_id', min_item='$min_item', max_item='$max_item', probability='$probability', min_money='$min_money', max_money='$max_money', randomize='$randomize', randomize_probability='$randomize_probability' WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        listlootrules();
    }
    elseif (checkaccess('npcs', 'edit') && isset($_POST['delete']) && $_POST['delete'] == 'Confirm Delete')
    {
        $id = escapeSqlString($_POST['id']);
        $query = "DELETE FROM loot_rule_details WHERE id='$id' LIMIT 1";
        $result = mysql_query2($query);
        echo '<p class="error">Delete Successful</p>';
        unset($_POST);
        listlootrules();
    }
    elseif (checkaccess('npcs', 'edit') && isset($_POST['edit']))
    {
        if (isset($_GET['id']))
        {
            $id = escapeSqlString($_GET['id']);
            if (!is_numeric($id))
            {
                echo '<p class="error">Invalid (non numeric) ID</p>';
                return;
            }
        }
        else
        {
            echo '<p class="error">No ID specified</p>';
            return;
        }
        $query = "SELECT * FROM loot_rule_details WHERE id='$id'";
        $result = mysql_query2($query);
        if (sqlNumRows($result) != 1)
        {
            echo '<p class="error">No loot rule detail found in the database with this ID ('.$id.')</p>';
            return;
        }
        $item_result = PrepSelect('items');
        $row = fetchSqlAssoc($result);
        echo '<table><tr><th>'; // We set the ID in the form to the rule ID instead of the detail, so we can redirect back to ListRules.
        echo '<form action="./index.php?do=editlootruledetail&id='.$row['loot_rule_id'].'" method="post"><input type="hidden" name="id" value="'.$id.'"/>Item</th><th>Min Quantity</th><th>Max Quantity</th><th>Probability</th><th>Minimum Money</th><th>Maxiumum Money</th><th>Randomize</th><th>Random %</th><th>Action</th></tr>';
        echo '<tr><td>'.DrawSelectBox('items', $item_result, 'item_stat_id', $row['item_stat_id'], true).'</td>';
		echo '<td><input type="text" name="min_item" value="'.$row['min_item'].'" size="8"/></td>';
		echo '<td><input type="text" name="max_item" value="'.$row['max_item'].'" size="8"/></td>';
        echo '<td><input type="text" name="probability" value="'.$row['probability'].'" size="8"/></td>';
        echo '<td><input type="text" name="min_money" value="'.$row['min_money'].'" size="11"/></td>';
        echo '<td><input type="text" name="max_money" value="'.$row['max_money'].'" size="11"/></td>';
        echo '<td><select name="randomize">';
        if ($row['randomize'] == 1)
        {
            echo '<option value="0">No</option><option value="1" selected="true">Yes</option>';
        }
        else
        {
            echo '<option value="0" selected="true">No</option><option value="1">Yes</option>';
        }
        echo '</select></td>';
		echo '<td><input type="text" name="randomize_probability" value="'.$row['randomize_probability'].'" size="8"/></td>';
        echo '<td><input type="submit" name="commit" value="Update Rule Detail"/></form></td></tr>';
        echo '</table>';
    }
    elseif (checkaccess('npcs', 'edit') && isset($_POST['delete']))
    {
        if (isset($_GET['id']))
        {
            $id = escapeSqlString($_GET['id']);
            if (!is_numeric($id))
            {
                echo '<p class="error">Invalid (non numeric) ID</p>';
                return;
            }
        }
        else
        {
            echo '<p class="error">No ID specified</p>';
            return;
        }
        $query = "SELECT i.name AS item_name, l.probability, l.loot_rule_id, l.min_money, l.max_money, l.randomize FROM loot_rule_details AS l LEFT JOIN item_stats AS i ON i.id=l.item_stat_id WHERE l.id='$id'";
        $result = mysql_query2($query);
        if (sqlNumRows($result) != 1)
        {
            echo '<p class="error">No loot rule detail found in the database with this ID ('.$id.')</p>';
            return;
        }
        $row = fetchSqlAssoc($result);
        echo '<p class="error">You are about to permanently delete Loot Rule Detail '.$id.' </p>';
        echo '<table><tr><td>'.$row['item_name'].'</td><td>'.$row['probability'].'</td><td>'.$row['min_money'].'</td><td>'.$row['max_money'].'</td><td>'.($row['randomize'] == 1 ? 'Yes' : 'No').'</td></tr></table>';
        echo '<form action="./index.php?do=editlootruledetail&id='.$row['loot_rule_id'].'" method="post"><input type="hidden" name="id" value="'.$id.'"/><input type="submit" name="delete" value="Confirm Delete"></form>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function createlootruledetail()
{
    if (checkaccess('npcs', 'create') && isset($_POST['commit']) && $_POST['commit'] == 'Create Rule Detail')
    {
        $loot_rule_id = escapeSqlString($_POST['loot_rule_id']);
        $item_stat_id = escapeSqlString($_POST['item_stat_id']);
        $probability = escapeSqlString($_POST['probability']);
        $min_money = escapeSqlString($_POST['min_money']);
        $max_money = escapeSqlString($_POST['max_money']);
        $randomize = escapeSqlString($_POST['randomize']);
        $query = "INSERT INTO loot_rule_details (loot_rule_id, item_stat_id, probability, min_money, max_money, randomize) VALUES ('$loot_rule_id', '$item_stat_id', '$probability', '$min_money', '$max_money', '$randomize')";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        listlootrules();
    }
    elseif (checkaccess('npcs', 'create') && isset($_POST['create']))
    {
        $item_result = PrepSelect('items');        
        $item_box = DrawSelectBox('items', $item_result, 'item_stat_id', '', true);
        $loot_rule_id = escapeSqlString($_GET['id']);
        echo '<table border="1"><form action="./index.php?do=createlootruledetail&id='.$loot_rule_id.'" method="post"><input type="hidden" name="loot_rule_id" value="'.$loot_rule_id.'"/>';
        echo '<tr><th>Item</th><th>Probability</th><th>Minimum Money</th><th>Maxiumum Money</th><th>Randomize</th></tr>';
        echo '<tr><td>'.$item_box.'</td>';
        echo '<td><input type="text" name="probability" value="0" size="8"/></td>';
        echo '<td><input type="text" name="min_money" value="0" size="11"/></td>';
        echo '<td><input type="text" name="max_money" value="0" size="11"/></td>';
        echo '<td><select name="randomize">';
        echo '<option value="0" selected="selected">No</option><option value="1">Yes</option>';
        echo '</select></td></tr>';
        echo '</table><input type="submit" name="commit" value="Create Rule Detail"/></form>';
    
    }
}

// Handles the "delete and rename rule" part
function editlootrule()
{
    if (checkaccess('npcs', 'edit'))
    {
        if (isset($_POST['id']))
        {
            $id = escapeSqlString($_POST['id']);
            if ($_POST['commit'] == 'Change Name')
            {
                $name = escapeSqlString($_POST['name']);
                $query = "UPDATE loot_rules SET name='$name' WHERE id='$id'";
                $result = mysql_query2($query);
                echo '<p class="error">Update Successful</p>';
                listlootrules();
            }
            elseif ($_POST['commit'] == 'Delete Rule')
            {   
                // List all affected details here, check if the rule is still in use by NPCs (can't delete if it is), and finally confirm (with pass)
                if (!checkaccess('npcs', 'delete'))
                {
                    echo '<p class="error">You are not authorized to use this feature.</p>';
                    return;
                }
                $query = "SELECT id, name, lastname FROM characters WHERE npc_addl_loot_category_id='$id'";
                $result = mysql_query2($query);
                if (sqlNumRows($result) > 0)
                {
                    echo '<p class="error">You can not delete this Loot Rule, it is still in use by the following NPCs:</p>';
                    echo '<table border="1"><tr><th>ID</th><th>Name</th></tr>';
                    while ($row = fetchSqlAssoc($result))
                    {
                        echo '<tr><td>'.$row['id'].'</td><td><a href="./index.php?do=npc_details&sub=main&npc_id='.$row['id'].'">'.$row['name'].' '.$row['lastname'].'</a></td></tr>';
                    }
                    echo '</table>';
                    return;
                }
                $query = "SELECT name FROM loot_rules WHERE id='$id'";
                $result = mysql_query2($query);
                if (sqlNumRows($result) > 0)
                {
                    $row = fetchSqlAssoc($result);
                    echo '<p class="error">You are about to permanently delete rule: '.$row['name'].' with ID: '.$id.'</p>';
                }
                else
                {
                    echo '<p class="error">No rule exists with ID: '.$id.'</p>';
                }
                $query = "SELECT i.name AS item_name, l.probability, l.loot_rule_id, l.min_money, l.max_money, l.randomize FROM loot_rule_details AS l LEFT JOIN item_stats AS i ON i.id=l.item_stat_id WHERE l.loot_rule_id='$id'";
                $result = mysql_query2($query);
                if (sqlNumRows($result) > 0)
                {
                    echo '<p class="error">Deleting this rule will also delete the following Loot Rule Details:</p>';
                    echo '<table border="1"><tr><td>Item</td><td>Probability</td><td>Minimum Money</td><td>Maxiumum Money</td><td>Randomize</td></tr>';
                    while ($row = fetchSqlAssoc($result))
                    {
                        echo '<tr><td>'.$row['item_name'].'</td><td>'.$row['probability'].'</td><td>'.$row['min_money'].'</td><td>'.$row['max_money'].'</td><td>'.($row['randomize'] == 1 ? 'Yes' : 'No').'</td></tr>';
                    }
                    echo '</table>';
                }
                echo '<form action="./index.php?do=editlootrule" method="post">Enter your password to confirm: <input type="password" name="passd" /><input type="hidden" name="id" value="'.$id.'" /><input type="submit" name="commit" value="Confirm Delete" /></form>';
            }
            elseif ($_POST['commit'] == 'Confirm Delete')
            {   
                // List all affected details here, check if the rule is still in use by NPCs (can't delete if it is), and finally confirm (with pass)
                if (!checkaccess('npcs', 'delete'))
                {
                    echo '<p class="error">You are not authorized to use this feature.</p>';
                    return;
                }
                $query = "DELETE FROM loot_rule_details WHERE loot_rule_id='$id'";
                $result = mysql_query2($query);
                $query = "DELETE FROM loot_rules WHERE id='$id' LIMIT 1";
                $result = mysql_query2($query);
                echo '<p class="error">Delete Succesful.</p>';
                unset($_POST);
                listlootrules();
            }
            else if ($_POST['commit'] == "Create New Rule")
            {
                $name = escapeSqlString($_POST['name']);
                $query = "INSERT INTO loot_rules (name) VALUES ('$name')";
                $result = mysql_query2($query);
                echo '<p class="error">Update Successful</p>';
                unset($_POST);
                listlootrules();
            }
            else
            {
                echo '<p class="error">Unknown command.</p>';
            }
        }
        else
        {
            echo '<p class="error">Error: No ID specified</p>';
            listlootrules();
        }
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
     
?>
