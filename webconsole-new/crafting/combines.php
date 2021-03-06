<?php

function editcombine()
{
    if (checkaccess('crafting','edit') && isset($_POST['commit']) && $_POST['commit'] == "Edit Combine")
    {
        if ($_POST['item_id0'] == '')
        {
            echo '<p class="error">A combination must have at least 1 source item, this should be the top item on your list.</p>';
            return;
        }
        for ($i = 0; $i < count($_POST['id']); $i++)
        {
            $id = escapeSqlString($_POST['id'][$i]);
            $pattern_id = escapeSqlString($_POST['pattern_id']);
            $result_id = escapeSqlString($_POST['result_id']);
            $result_qty = escapeSqlString($_POST['result_qty']);
            $item_id = escapeSqlString($_POST['item_id'.$i]);
            $min_qty = escapeSqlString($_POST['min_qty'][$i]);
            $max_qty = escapeSqlString($_POST['max_qty'][$i]);
            $description = escapeSqlString($_POST['description'][$i]);
            
            if ($item_id != '')
            {
                if ($id != 0)
                {
                    $query = "UPDATE trade_combinations SET pattern_id='$pattern_id', result_id='$result_id', result_qty='$result_qty', item_id='$item_id', min_qty='$min_qty', max_qty='$max_qty', description='$description' WHERE id='$id'";
                    mysql_query2($query);
                }
                else // someone added this entry.
                {
                    $query = "INSERT INTO trade_combinations (pattern_id, result_id, result_qty, item_id, min_qty, max_qty, description) VALUES ('$pattern_id', '$result_id', '$result_qty', '$item_id', '$min_qty', '$max_qty', '$description')";
                    mysql_query2($query);
                }
            }
            else
            {
                if ($id != 0)
                {
                    $query = "DELETE FROM trade_combinations WHERE id='$id' LIMIT 1";
                    mysql_query2($query);
                }
            }
        }
        echo '<p class="error">Combination was succesfully updated.</p>';
        unset($_POST);
        if (isset($_GET['id']))
        {
            include('./crafting/patterns.php');
            editpattern(); // ID is set to pattern_id, so this method will list the info for the pattern which combination we edited.
        }
        else
        {
            // can't do anyhting if ID is not set.
        }
    }
    elseif (checkaccess('crafting','edit') && isset($_GET['id']) && isset($_GET['pattern_id']))
    {
        $id = escapeSqlString($_GET['id']);
        $pattern_id = escapeSqlString($_GET['pattern_id']);
        $query = "SELECT id, pattern_id, result_qty, item_id, min_qty, max_qty, description FROM trade_combinations WHERE result_id='$id' AND pattern_id='$pattern_id'";
        $result = mysql_query2($query);
        if (sqlNumRows($result) < 1)
        {
            echo '<p class="error">No combinations were found with result id'.$id.'</p>';
            return;
        }
        
        $patterns = PrepSelect('patterns');
        $delete_text = (checkaccess('crafting','delete') ? '<a href="./index.php?do=deletecombine&amp;pattern_id='.$pattern_id.'&amp;result_id='.$id.'">Delete Combination</a>' : "");
        $row = fetchSqlAssoc($result);
        echo '<p class="bold">Edit Combine</p>'."\n"; 
        echo 'If you set any item to "NONE", it will be removed from the combination.';
        echo '<form action="./index.php?do=editcombine&amp;id='.$pattern_id.'" method="post" ><table>'."\n"; // we set pattern_id here instead of combination ID, so we can redirect people back to where they came from.
        echo '<tr><td colspan="2">If you change this dropdown, you will move this transformation to another pattern, moving it to "NONE" will make it "patternless".</td></tr>'."\n";
        echo '<tr><td>Pattern</td><td>'.DrawSelectBox('patterns', $patterns, 'pattern_id', $row['pattern_id'], true).'</td></tr>'."\n";
        echo '<tr><td>Result Item</td><td>'.DrawItemSelectBox('result_id', $id, false, true).'</td></tr>'."\n";
        echo '<tr><td>Result Quantity</td><td><input type="text" name="result_qty" value="'.$row['result_qty'].'" /></td></tr>'."\n";
        $inputCount = 0;
        do 
        {
            echo '<tr><td><input type="hidden" name="id[]" value="'.$row['id'].'" /></td><td></td></tr>'."\n";
            echo '<tr><td>Input Item</td><td>'.DrawItemSelectBox('item_id'.$inputCount, $row['item_id'], false, true).'</td></tr>'."\n";
            echo '<tr><td>Minimum Quantity</td><td><input type="text" name="min_qty[]" value="'.$row['min_qty'].'" /></td></tr>'."\n";
            echo '<tr><td>Maximum Quantity</td><td><input type="text" name="max_qty[]" value="'.$row['max_qty'].'" /></td></tr>'."\n";
            echo '<tr><td>Description</td><td><input type="text" name="description[]" value="'.$row['description'].'" /></td></tr>'."\n";
            echo '<tr><td></td><td></td></tr>';
            $inputCount++;
        } while ($row = fetchSqlAssoc($result));
        echo '<tr><td colspan="2">If you fill in the item below, it will be added to the combination.<input type="hidden" name="id[]" value="0" /></td></tr>'."\n";
        echo '<tr><td>Input Item</td><td>'.DrawItemSelectBox('item_id'.$inputCount, false, true, true).'</td></tr>'."\n"; // add 1 more row so we can add new lines.
        echo '<tr><td>Minimum Quantity</td><td><input type="text" name="min_qty[]" value="0" /></td></tr>'."\n";
        echo '<tr><td>Maximum Quantity</td><td><input type="text" name="max_qty[]" value="0" /></td></tr>'."\n";
        echo '<tr><td>Description</td><td><input type="text" name="description[]" value="" /> </td></tr>'."\n";
        
        echo '<tr><td>'.$delete_text.'</td><td><input type="submit" name="commit" value="Edit Combine"/></td></tr>'."\n";
        echo '</table></form>'."\n";
    }
    else
    {
        echo '<p class="error">You do not have the proper rights to use this function, or made an invalid request.</p>';
    }
}

function createcombine()
{
    if (checkaccess('crafting','create') && isset($_POST['commit']) && $_POST['commit'] == "Create Combine")
    { // The user wants to submit data.
        if ($_POST['item_id0'] == '')
        {
            echo '<p class="error">A combination must have at least 1 source item, this should be the top item on your list.</p>';
            return;
        }
        for ($i = 0; $i < count($_POST['min_qty']); $i++)
        {
            $pattern_id = escapeSqlString($_POST['pattern_id']);
            $result_id = escapeSqlString($_POST['result_id']);
            $result_qty = escapeSqlString($_POST['result_qty']);
            $item_id = escapeSqlString($_POST['item_id'.$i]);
            $min_qty = escapeSqlString($_POST['min_qty'][$i]);
            $max_qty = escapeSqlString($_POST['max_qty'][$i]);
            $description = escapeSqlString($_POST['description'][$i]);
            
            if ($item_id != '') // if this is empty, the user has left this line empty because they didn't need it.
            {
                $query = "INSERT INTO trade_combinations (pattern_id, result_id, result_qty, item_id, min_qty, max_qty, description) VALUES ('$pattern_id', '$result_id', '$result_qty', '$item_id', '$min_qty', '$max_qty', '$description')";
                mysql_query2($query);
            }
        }
        echo '<p class="error">Combination was succesfully added.</p>';
        unset($_POST);
        if (isset($_GET['id']))
        {
            include('./crafting/patterns.php');
            editpattern(); // ID is set to pattern_id, so this method will list the info for the pattern to which we added a combination.
        }        
    }
    elseif (checkaccess('crafting','create') && isset($_POST['add'])) // The user wants more input fields.
    {
        $inputCount = 0;
        echo '<p class="bold">Create Combine</p>'."\n"; // new pattern
        echo 'If you set any item to "NONE", it will not be added to the combination.';
        echo '<form action="./index.php?do=createcombine&amp;id='.$_GET['id'].'" method="post" ><table>'; // we set pattern_id here so we can redirect people back to where they came from.
        echo '<tr><td>Pattern id</td><td><input type="hidden" name="pattern_id" value="'.$_POST['pattern_id'].'" />'.$_POST['pattern_id'].'</td></tr>';
        echo '<tr><td>Result Item</td><td>'.DrawItemSelectBox('result_id', $_POST['result_id'], false, false).'</td></tr>';
        echo '<tr><td>Result Quantity</td><td><input type="text" name="result_qty" value="'.$_POST['result_qty'].'" /></td></tr>';
        echo '<tr><td></td><td></td></tr>';
        for ($i = 0; $i < count($_POST['min_qty']); $i++)  // Show all previously entered values (if someone posted this form to increase the size.
        {
            echo '<tr><td>Input Item</td><td>'.DrawItemSelectBox('item_id'.$inputCount, $_POST['item_id'.$inputCount], false, true).'</td></tr>';
            echo '<tr><td>Minimum Quantity</td><td><input type="text" name="min_qty[]" value="'.$_POST['min_qty'][$i].'" /></td></tr>';
            echo '<tr><td>Maximum Quantity</td><td><input type="text" name="max_qty[]" value="'.$_POST['max_qty'][$i].'" /></td></tr>';
            echo '<tr><td>Description</td><td><input type="text" name="description[]" value="'.$_POST['description'][$i].'" /></td></tr>';
            echo '<tr><td></td><td></td></tr>';
            $inputCount++;
        }
        for ($i = 0; $i < $_POST['more_fields']; $i++)  // add the additional fields.
        { 
            echo '<tr><td>Input Item</td><td>'.DrawItemSelectBox('item_id'.$inputCount, false, ($i+1 < $_POST['more_fields'] ? false : true), true).'</td></tr>';
            echo '<tr><td>Minimum Quantity</td><td><input type="text" name="min_qty[]" value="0" /></td></tr>';
            echo '<tr><td>Maximum Quantity</td><td><input type="text" name="max_qty[]" value="0" /></td></tr>';
            echo '<tr><td>Description</td><td><input type="text" name="description[]" value="" /></td></tr>';
            echo '<tr><td></td><td></td></tr>';
            $inputCount++;
        }
        echo '<tr><td colspan="2">Add <input type="text" name="more_fields" value="0" /> more fields to this form <input type="submit" name="add" value="add"/></td></tr>';
        echo '<tr><td></td><td><input type="submit" name="commit" value="Create Combine"/></td></tr>'; 
        echo '</table></form>'."\n";
    }
    elseif (checkaccess('crafting','create') && isset($_GET['pattern_id']))
    {
        $inputCount = 0;
        echo '<p class="bold">Create Combine</p>'."\n"; // new pattern
        echo 'If you set any item to "NONE", it will not be added to the combination.';
        echo '<form action="./index.php?do=createcombine&amp;id='.$_GET['pattern_id'].'" method="post" ><table>'; // we set pattern_id here so we can redirect people back to where they came from.
        echo '<tr><td>Pattern id</td><td><input type="hidden" name="pattern_id" value="'.$_GET['pattern_id'].'" />'.$_GET['pattern_id'].'</td></tr>';
        echo '<tr><td>Result Item</td><td>'.DrawItemSelectBox('result_id', false, false, false).'</td></tr>';
        echo '<tr><td>Result Quantity</td><td><input type="text" name="result_qty" value="0" /></td></tr>';
        echo '<tr><td></td><td></td></tr>';
        for ($i = 0; $i < 2; $i++) // form wasn't used before, show 2 fields (can't make a combine with less).
        { 
            echo '<tr><td>Input Item</td><td>'.DrawItemSelectBox('item_id'.$inputCount, false, ($i+1 < 2 ? false : true), true).'</td></tr>';
            echo '<tr><td>Minimum Quantity</td><td><input type="text" name="min_qty[]" value="0" /></td></tr>';
            echo '<tr><td>Maximum Quantity</td><td><input type="text" name="max_qty[]" value="0" /></td></tr>';
            echo '<tr><td>Description</td><td><input type="text" name="description[]" value="" /></td></tr>';
            echo '<tr><td></td><td></td></tr>';
            $inputCount++;
        }
        echo '<tr><td colspan="2">Add <input type="text" name="more_fields" value="0" /> more fields to this form <input type="submit" name="add" value="add"/></td></tr>';
        echo '<tr><td></td><td><input type="submit" name="commit" value="Create Combine"/></td></tr>'; 
        echo '</table></form>'."\n";
        
    }
    else
    {
        echo '<p class="error">You do not have the proper rights to use this function, or made an invalid request.</p>';
    }
}

function deletecombine()
{
    if (checkaccess('crafting','delete') && isset($_POST['submit']) && isset($_GET['id']) && isset($_GET['result_id']))
    {
        if(!is_numeric($_GET['id']) && !is_numeric($_GET['result_id']))
        {
            echo '<p class="error">Non-numeric ID provided, aborting.</p>';
            return;
        }
        $password = escapeSqlString($_POST['passd']);
        $username = escapeSqlString($_SESSION['username']);
        $query = "SELECT COUNT(username) FROM accounts WHERE username='$username' AND password=MD5('$password')";
        $result = mysql_query2($query);
        $row = fetchSqlRow($result);
        if ($row[0] == 1)
        {
            $pattern_id = escapeSqlString($_GET['id']);
            $result_id = escapeSqlString($_GET['result_id']);
            $query = "DELETE FROM trade_combinations WHERE pattern_id='$pattern_id' AND result_id='$result_id'";
            mysql_query2($query);
            echo '<p class="error">Combination was succesfully deleted.</p>';
            unset($_POST);
            if (isset($_GET['id']))
            {
                include('./crafting/patterns.php');
                editpattern(); // ID is set to pattern_id, so this method will list the info for the pattern to which we added a combination.
            }                    
        }
        else
        {
            echo '<p class="error">Password check failed - Did Not Delete Combination</p>';
        }
    }
    elseif (checkaccess('crafting','delete') && isset($_GET['pattern_id']) && isset($_GET['result_id']))
    {
        $query = "SELECT id, name FROM item_stats";
        $temp = mysql_query2($query);
        $items = "";
        while ($row = fetchSqlAssoc($temp))
        {
            $iid = $row['id'];
            $items["$iid"] = $row['name'];
        }
        $pattern_id = escapeSqlString($_GET['pattern_id']);
        $result_id = escapeSqlString($_GET['result_id']);
        echo '<p>You are about to permanently delete the following combinations:</p>';
        $query = "SELECT result_id, result_qty, item_id, min_qty, max_qty, description FROM trade_combinations WHERE pattern_id='$pattern_id' AND result_id='$result_id'";
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        echo '<table><tr><th>Result Item</th><th>Source Items</th></tr>';
        echo '<td>'.$row['result_qty'].' '.$items[$row['result_id']].'</td>';
        echo '<td>'.$row['min_qty'].' to '.$row['max_qty'].' '.$items[$row['item_id']].'<br>';
        while ($row = fetchSqlAssoc($result))
        {
            echo $row['min_qty'].' to '.$row['max_qty'].' '.$items[$row['item_id']].'<br>';
        }
        echo '</td></tr></table>';
        echo '<form action="./index.php?do=deletecombine&amp;id='.$pattern_id.'&amp;result_id='.$result_id.'" method="post">Enter your password to confirm: <input type="password" name="passd" /><input type="submit" name="submit" value="Confirm Delete"></form>';

    }
    else
    {
        echo '<p class="error">You do not have access to delete combinations, or did not provide a valid pattern/result ID</p>';
    }
}
?>
