<?php

function listpatterns(){
    if (!checkaccess('crafting', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
    echo '<p class="header">Available Crafting Patterns</p>'."\n";
    $query = "SELECT t.id, t.pattern_name, t.description, i.name FROM trade_patterns AS t LEFT JOIN item_stats AS i ON t.designitem_id=i.id ORDER BY t.id";
    $r = mysql_query2($query);
    $alt = false;
    echo '<table><tr><th>ID</th><th>Pattern Name</th><th>Description</th><th>Design Item</th>';
    echo '<th>Actions</th>'."\n";
    echo '</tr>'; // next line shows all transforms/combines without pattern. (general knowledge)
    echo '<tr class="color_b"><td>0</td><td>Patternless </td><td>transforms/combinations </td><td>none</td><td><a href="./index.php?do=editpattern&amp;id=0">Details</a></td></tr>';
    while ($row = fetchSqlAssoc($r)){
        echo '<tr class="color_'.(($alt = !$alt) ? 'a' : 'b').'">';
        echo '<td>'.$row['id'].'</td>';
        echo '<td>'.htmlentities($row['pattern_name']).'</td>';
        echo '<td>'.htmlentities($row['description']).'</td>';
        echo '<td>'.htmlentities($row['name']).'</td>';
        echo '<td><a href="./index.php?do=editpattern&amp;id='.$row['id'].'">Details</a></td>';
        echo '</tr>'."\n";
    }
    echo '</table>'."\n";    
}

function editpattern(){
    if (!checkaccess('crafting','read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    echo '<p class="header">Pattern Details</p>';
    if (isset($_GET['id']))
    {
        $pattern_id = escapeSqlString($_GET['id']);
    }
    else
    {
        echo '<p class="error">No Pattern ID specified</p>';
        listpatterns();
        return;
    }
    if ((checkaccess('crafting', 'edit')) && isset($_POST['commit']) && ($_POST['commit']=='Update Pattern'))
    {
        $pattern_name = escapeSqlString($_POST['pattern_name']);
        $description = escapeSqlString($_POST['description']);
        $k_factor = escapeSqlString($_POST['k_factor']);
        $id = escapeSqlString($_GET['id']);
        $query = "UPDATE trade_patterns SET pattern_name='$pattern_name', description='$description', k_factor='$k_factor' WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        editpattern();
        return;
    }
    if ($pattern_id != 0) // show pattern details only if pattern_id is not 0. (patternless/general knowledge combines/transforms.
    {
        $query = "SELECT tp.pattern_name, tp.description, tp.designitem_id, tp.group_id, i.name AS item_name, i.category_id, tp.k_factor FROM trade_patterns AS tp LEFT JOIN item_stats AS i ON tp.designitem_id=i.id WHERE tp.id='$pattern_id'";
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        if ($row['group_id']) {
          $groupid = $row['group_id'];
          $query2 = "SELECT pattern_name FROM trade_patterns WHERE id='$groupid'";
          $result2 = mysql_query2($query2);
          $row2 = fetchSqlAssoc($result2);
          $groupname = $row2['pattern_name'];
        }
        if (checkaccess('crafting', 'edit'))
        {
            $delete_text = (checkaccess('crafting','delete') ? '<a href="./index.php?do=deletepattern&amp;id='.$pattern_id.'">Delete Pattern</a>' : "");
            echo '<form action="./index.php?do=editpattern&amp;id='.$pattern_id.'" method="post">'."\n";
            echo '<table><tr><td>Pattern Name:</td><td><input type="text" name="pattern_name" value="'.htmlentities($row['pattern_name']).'"/></td></tr>'."\n";
            echo '<tr><td>Pattern Description:</td><td><textarea name="description" rows="5" cols="40">'.htmlentities($row['description']).'</textarea></td></tr>'."\n";
            echo '<tr><td>Design Item:</td><td><a href="./index.php?do=listitems&amp;override1&amp;category='.$row['category_id'].'&amp;item='.$row['designitem_id'].'">'.htmlentities($row['item_name']).'</a></td></tr>'."\n";
            echo '<tr><td>Difficulty Factor:</td><td><input type="text" name="k_factor" value="'.htmlentities($row['k_factor']).'"/></td></tr>'."\n";
            echo '<tr><td>Parent Pattern:</td><td>'.(isset($groupname) ? $groupname : '').'</td></tr>'."\n";
            echo '<tr><td><input type="submit" name="commit" value="Update Pattern"/></td><td>'.$delete_text.'</td></tr>'."\n";
            echo '</table>'."\n";
            echo '</form>'."\n";
        }
        else
        {
            echo '<table>';
            echo '<tr><td>Pattern Name:</td><td>'.htmlentities($row['pattern_name']).'</td></tr>'."\n";
            echo '<tr><td>Pattern Description:</td><td>'.htmlentities($row['description']).'</td></tr>'."\n";
            $i = $row['designitem_id'];
            echo '<tr><td>Design Item:</td><td>'.htmlentities($Items["$i"]).'</td></tr>'."\n";
            echo '<tr><td>Difficulty Factor:</td><td>'.htmlentities($row['k_factor']).'</td></tr>'."\n";
            echo '<tr><td>Parent Pattern:</td><td>'.(isset($groupname) ? $groupname : '').'</td></tr>'."\n";
            echo '</table>';
        }
    }
    else
    {
        echo '<p>Please take note that this is not technically a pattern, it is the lack of it. This collection of combines and transforms is considered "general knowledge" and because of that is not associated with any pattern. You are free to add/edit any of these as long as you keep this in mind.</p>'."\n";
    }
    if (!isset($_GET['showids']))
    {
        echo '<p>Showing transformation and combinations without ID, to show them, click <a href="./index.php?do=editpattern&amp;id='.$pattern_id.'&amp;showids">here</a></p>'."\n";
    }
    echo '<p class="bold">Available Transforms</p>'."\n";
    $query = "SELECT t.id, t.process_id, p.name, t.result_id, i.name AS result_name, c.name AS result_cat, c.category_id AS result_cat_id, t.result_qty, t.item_id, ii.name AS item_name, cc.name AS item_cat, cc.category_id AS item_cat_id, t.item_qty, t.trans_points, t.penalty_pct, t.description FROM trade_transformations AS t LEFT JOIN item_stats AS i ON i.id=t.result_id LEFT JOIN item_stats AS ii ON ii.id=t.item_id LEFT JOIN trade_processes AS p ON t.process_id=p.process_id LEFT JOIN item_categories AS c ON i.category_id=c.category_id LEFT JOIN item_categories AS cc ON ii.category_id=cc.category_id WHERE pattern_id='$pattern_id' GROUP BY id ORDER BY p.name, ii.name, i.name";
    $result = mysql_query2($query);
    echo '<table><tr>'.(isset($_GET['showids']) ? '<th>ID</th>' : '').'<th colspan="2">Source Item</th><th>Category</th><th>Process</th><th colspan="2">Result Item</th><th>Category</th><th>Time</th><th>Result Q</th>';
    if (checkaccess('crafting', 'edit'))
    {
        echo '<th>Actions</th>';
    }
    echo '</tr>'."\n";
    $alt = false;
    while ($row=fetchSqlAssoc($result))
    {
        echo '<tr class="color_'.(($alt = !$alt) ? 'a' : 'b').'">';
        echo (isset($_GET['showids']) ? '<td>'.$row['id'].'</td>' : '');
        $item_name = ($row['item_name'] == "NULL" ? ($row['item_id'] != 0 ? "BROKEN" : "") : htmlentities($row['item_name'])); // Item name is broken if NULL was returned and ID is not 0, if ID was 0, name is "", else name the name found in the database.
        if (checkaccess('items','edit'))
        {
            echo '<td>'.$row['item_qty'].' </td><td> <a href="./index.php?do=listitems&amp;override1&amp;category='.$row['item_cat_id'].'&amp;item='.$row['item_id'].'">'.$item_name.'</a> </td>';
        }
        else
        {
            echo '<td>'.$row['item_qty'].' </td><td> '.$item_name.' </td>';
        }
        echo '<td>'.$row['item_cat'].'</td>';
        echo '<td><a href="./index.php?do=process&amp;id='.$row['process_id'].'">'.$row['name'].'</a></td>';
        $result_name = ($row['result_name'] == "NULL" ? ($row['result_id'] != 0 ? "BROKEN" : "") : htmlentities($row['result_name'])); // Item name is broken if NULL was returned and ID is not 0, if ID was 0, name is "", else name the name found in the database.
        if (checkaccess('items','edit'))
        {
            echo '<td>'.$row['result_qty'].' </td><td> <a href="./index.php?do=listitems&amp;override1&amp;category='.$row['result_cat_id'].'&amp;item='.$row['result_id'].'">'.$result_name.'</a> </td>';
        }
        else
        {
            echo '<td>'.$row['result_qty'].' </td><td> '.$result_name.'</td>';
        }
        echo '<td>'.htmlentities($row['result_cat']).'</td>';
        echo '<td>'.htmlentities($row['trans_points']).'</td>';
        echo '<td>'.htmlentities($row['penalty_pct']).'</td>';
        if (checkaccess('crafting', 'edit')) 
        {
            echo '<td><a href="./index.php?do=transform&amp;id='.$row['id'].'">Edit</a></td>';
        }
        echo '</tr>'."\n";
    }
    echo '</table>'."\n";
    echo '<a href="./index.php?do=createtransform&amp;id='.$pattern_id.'">Create new transform for this pattern </a><br />'."\n";
    echo '<p class="bold">Available Combinations</p>'."\n";
    $alt = false;
    $item = -1;
    $query = "SELECT t.id, t.result_id, c.name AS result_cat, c.category_id AS result_cat_id, i.name AS result_name, t.result_qty, t.item_id, ii.name AS item_name, cc.name AS item_cat, cc.category_id AS item_cat_id, t.min_qty, t.max_qty, t.description FROM trade_combinations AS t LEFT JOIN item_stats AS i ON i.id=t.result_id LEFT JOIN item_stats AS ii ON ii.id=t.item_id LEFT JOIN item_categories AS c ON i.category_id=c.category_id LEFT JOIN item_categories AS cc ON ii.category_id=cc.category_id WHERE pattern_id='$pattern_id' ORDER BY i.name";
    $result = mysql_query2($query);
    if (sqlNumRows($result) != 0)
    {
        echo '<table><tr><th colspan="2">Result Item</th><th>Category</th><th>'.(isset($_GET['showids']) ? 'ID -- ' : '').'Source Items</th>';
        if (checkaccess('crafting', 'edit'))
        {
            echo '<th>Actions</th>';
        }
        echo '</tr>'."\n";
        while ($row = fetchSqlAssoc($result))
        {
            if ($item != $row['result_id'])
            {
                if ($item != '-1')
                {
                    if (checkaccess('crafting', 'edit'))
                    {
                        echo '</td><td><a href="./index.php?do=editcombine&amp;id='.$item.'&amp;pattern_id='.$_GET['id'].'">Edit</a>';
                    }
                    echo '</td></tr>'."\n";
                }
                $item = $row['result_id'];
                echo '<tr class="color_'.(($alt = !$alt) ? 'a' : 'b').'">';
                $result_id = $row['result_id'];
                $result_name = ($row['result_name'] == "NULL" ? ($row['result_id'] != 0 ? "BROKEN" : "") : htmlentities($row['result_name'])); // Item name is broken if NULL was returned and ID is not 0, if ID was 0, name is "", else name the name found in the database.
                if (checkaccess('items','edit'))
                {
                    echo '<td>'.$row['result_qty'].' </td><td> <a href="./index.php?do=listitems&amp;override1&amp;category='.$row['result_cat_id'].'&amp;item='.$row['result_id'].'">'.$result_name.'</a> </td>';
                }
                else
                {
                    echo '<td>'.$row['result_qty'].' </td><td> '.$result_name.'</td>';
                }
                echo '<td>'.$row['result_cat'].'</td>';
                $item_name = ($row['item_name'] == "NULL" ? ($row['item_id'] != 0 ? "BROKEN" : "") : htmlentities($row['item_name'])); // Item name is broken if NULL was returned and ID is not 0, if ID was 0, name is "", else name the name found in the database.
                if (checkaccess('items','edit'))
                {
                    echo '<td>'.(isset($_GET['showids']) ? $row['id'].' -- ' : '').$row['min_qty'].' to '.$row['max_qty'].' <a href="./index.php?do=listitems&amp;override1&amp;category='.$row['item_cat_id'].'&amp;item='.$row['item_id'].'">'.$item_name.'</a> ('.$row['item_cat'].')';
                }
                else
                {
                    echo '<td>'.(isset($_GET['showids']) ? $row['id'].' -- ' : '').$row['min_qty'].' to '.$row['max_qty'].' '.$item_name.' ('.$row['item_cat'].')';
                }
            }
            else
            {
                echo '<br/>';
                $item_name = ($row['item_name'] == "NULL" ? ($row['item_id'] != 0 ? "BROKEN" : "") : htmlentities($row['item_name'])); // Item name is broken if NULL was returned and ID is not 0, if ID was 0, name is "", else name the name found in the database.
                if (checkaccess('items','edit'))
                {
                    echo (isset($_GET['showids']) ? $row['id'].' -- ' : '').$row['min_qty'].' to '.$row['max_qty'].' <a href="./index.php?do=listitems&amp;override1&amp;category='.$row['item_cat_id'].'&amp;item='.$row['item_id'].'">'.$item_name.'</a> ('.$row['item_cat'].')';
                }
                else
                {
                    echo (isset($_GET['showids']) ? $row['id'].' -- ' : '').$row['min_qty'].' to '.$row['max_qty'].' '.$item_name.' ('.$row['item_cat'].')';
                }
            }
        }
        echo '</td><td><a href="./index.php?do=editcombine&amp;id='.$item.'&amp;pattern_id='.$_GET['id'].'">Edit</a></td></tr></table>'."\n";
        echo '<a href="./index.php?do=createcombine&amp;pattern_id='.$pattern_id.'">Create new combine for this pattern </a><br />'."\n";
    }
    else
    {
        echo '<p class="error">No available Combines</p>'."\n";
        echo '<a href="./index.php?do=createcombine&amp;pattern_id='.$pattern_id.'">Create new combine for this pattern </a><br />'."\n";
    }
}

function createpattern() 
{
    if (checkaccess('crafting','create') && isset($_POST['commit']) && ($_POST['commit']=='Create Pattern')) // submit pattern
    {
        $pattern_name = escapeSqlString($_POST['pattern_name']);
        $group_id = escapeSqlString($_POST['group_id']);
        $designitem_id = escapeSqlString($_POST['designitem_id']);
        $k_factor = escapeSqlString($_POST['k_factor']);
        $description = escapeSqlString($_POST['description']);
        $query = "INSERT INTO trade_patterns (pattern_name, group_id, designitem_id, k_factor, description) VALUES ('$pattern_name', '$group_id', '$designitem_id', '$k_factor', '$description')";
        mysql_query2($query);
        echo '<p class="error">Pattern added succesfully</p>';
        unset($_POST);
        createpattern();
    }
    elseif (checkaccess('crafting','create'))
    {
        echo '<p class="bold">Create Pattern</p>'."\n"; // new pattern
        echo '<form action="./index.php?do=createpattern" method="post" /><table>';
        echo '<tr><td>Pattern Name</td><td><input type="text" name="pattern_name" /> </td></tr>';
        echo '<tr><td>Group id</td><td><input type="text" name="group_id" value="0" /> </td></tr>';
        $items_results = PrepSelect('mind_slot_items');
        echo '<tr><td>Design Item </td><td>'.DrawSelectBox('items', $items_results, 'designitem_id', '', false).'</td></tr>';
        echo '<tr><td>Difficulty Factor</td><td><input type="text" name="k_factor" value="0" /> </td></tr>';
        echo '<tr><td>Description</td><td><textarea name="description" rows="5" cols="40"></textarea> </td></tr>';
        echo '<tr><td></td><td><input type=submit name="commit" value="Create Pattern"/></td></tr>';
        echo '</table></form>'."\n";
    }
    else
    {
        echo '<p class="error">You do not have the required access to create a pattern.</p>';
    }
}

function deletepattern()
{
    if (checkaccess('crafting','delete') && isset($_POST['submit']) && isset($_GET['id']) && is_numeric($_GET['id']))
    {
        $password = escapeSqlString($_POST['passd']);
        $username = escapeSqlString($_SESSION['username']);
        $query = "SELECT COUNT(username) FROM accounts WHERE username='$username' AND password=MD5('$password')";
        $result = mysql_query2($query);
        $row = fetchSqlRow($result);
        if ($row[0] == 1)
        {
            $id = escapeSqlString($_GET['id']);
            $query = "DELETE FROM trade_patterns WHERE id = $id LIMIT 1"; //limit is not needed, but if something unexpected does happen, it'll only affect 1 transform.
            mysql_query2($query);
            $query = "DELETE FROM trade_transformations WHERE pattern_id = $id"; // the following queries are not limited since we don't know how many they will delete.
            mysql_query2($query);
            $query = "DELETE FROM trade_combinations WHERE pattern_id = $id";
            mysql_query2($query);
            echo '<p class="error">Pattern with ID '.$id.' and all associated transformations/combinations were succesfully deleted.</p>';
            unset($_POST);
            listpatterns();
        }
        else
        {
            echo '<p class="error">Password check failed - Did Not Delete Transform</p>';
        }

    }
    elseif (checkaccess('crafting','delete') && isset($_GET['id']) && is_numeric($_GET['id']))
    {
        $pattern_id = $_GET['id'];
        echo '<p class="error">Warning, deleting this pattern will also delete *ALL* of the combinations and transformations listed below.</p>';
        $query = "SELECT id, name FROM item_stats";
        $temp = mysql_query2($query);
        while ($row = fetchSqlAssoc($temp))
        {
            $iid = $row['id'];
            $items["$iid"] = $row['name'];
        }
        $query = "SELECT pattern_name, description, designitem_id FROM trade_patterns WHERE id='$pattern_id'";
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);

        echo '<table><tr><td>Pattern Name:</td><td>'.$row['pattern_name'].'</td></tr>';
        echo '<tr><td>Pattern Description:</td><td>'.$row['description'].'</td></tr>';
        $i = $row['designitem_id'];
        echo '<tr><td>Design Item:</td><td>'.$items["$i"].'</td></tr>';
        echo '</table>';

        echo '<p class="bold">Available Transforms</p>';
        $query = "SELECT t.id, t.process_id, p.name, t.result_id, i.name AS item_name, t.result_qty, t.item_id, t.item_qty, t.trans_points, t.penalty_pct, t.description FROM trade_transformations AS t LEFT JOIN item_stats AS i ON i.id=t.result_id LEFT JOIN trade_processes AS p ON t.process_id=p.process_id WHERE pattern_id='$pattern_id' GROUP BY id ORDER BY p.name, i.name";
        $result = mysql_query2($query);
        echo '<table><tr><th colspan="2">Source Item</th><th>Process</th><th colspan="2">Result Item</th><th>Time</th><th>Result Q</th></tr>';
        $alt = FALSE;
        while ($row = fetchSqlAssoc($result))
        {
            $alt = !$alt;
            if ($alt){
                echo '<tr class="color_a">';
            }
            else
            {
                echo '<tr class="color_b">';
            }
            $item_id = $row['item_id'];
            echo '<td>'.$row['item_qty'].' </td><td> '.$items["$item_id"].'</td>';
            echo '<td><a href="./index.php?do=process&amp;id='.$row['process_id'].'">'.$row['name'].'</a></td>';
            $result_id=$row['result_id'];
            echo '<td>'.$row['result_qty'].' </td><td> '.$items["$result_id"].'</td>';
            echo '<td>'.$row['trans_points'].'</td>';
            echo '<td>'.$row['penalty_pct'].'</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '<p class="bold">Available Combinations</p>';
        $alt = FALSE;
        $item = -1;
        $query = "SELECT result_id, result_qty, item_id, min_qty, max_qty, description FROM trade_combinations WHERE pattern_id='$pattern_id' ORDER BY result_id";
        $result = mysql_query2($query);
        if (sqlNumRows($result) != 0)
        {
            echo '<table><tr><th colspan="2">Result Item</th><th>Source Items</th></tr>';
            while ($row = fetchSqlAssoc($result)){
                if ($item != $row['result_id'])
                {
                    if ($item != '-1')
                    {
                        echo '</td></tr>'."\n";
                    }
                    $item = $row['result_id'];
                    $alt = !$alt;
                    if ($alt){
                        echo '<tr class="color_a">';
                    }
                    else
                    {
                        echo '<tr class="color_b">';
                    }
                    $result_id = $row['result_id'];
                    echo '<td>'.$row['result_qty'].' </td><td> '.$items["$result_id"].'</td>';
                    $item_id = $row['item_id'];
                    echo '<td>'.$row['min_qty'].' to '.$row['max_qty'].' '.$items["$item_id"];
                }
                else
                {
                    echo '<br/>';
                    $item_id = $row['item_id'];
                    echo $row['min_qty'].' to '.$row['max_qty'].' '.$items["$item_id"];
                }
            }
            echo '</tr></table>';
        }
        else
        {
            echo '<p class="error">No available Combines</p>';
        }
        
        echo '<form action="./index.php?do=deletepattern&amp;id='.$_GET['id'].'" method="post">Enter your password to confirm deletion of *ALL* items listed above: <input type="password" name="passd" /><input type="submit" name="submit" value="Confirm Delete"></form>';

    }
    else
    {
        echo 'You do not have access to delete patterns, or did not provide a valid ID';
    }
}

?>
