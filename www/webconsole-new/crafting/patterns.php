<?php

function listpatterns(){
  if (checkaccess('crafting', 'read')){
    echo '<p class="header">Available Crafting Patterns</p>';
    $query = "SELECT t.id, t.pattern_name, t.description, i.name FROM trade_patterns AS t LEFT JOIN item_stats AS i ON t.designitem_id=i.id";
    $r = mysql_query2($query);
    $Alt = FALSE;
    echo '<table><tr><th>ID</th><th>Pattern Name</th><th>Description</th><th>Design Item</th>';
    echo '<th>Actions</th>';
    echo '</tr>';
    while ($row = mysql_fetch_array($r, MYSQL_ASSOC)){
      $Alt = !$Alt;
      if ($Alt){
        echo '<tr class="color_a">';
      }else{
        echo '<tr class="color_b">';
      }
      echo '<td>'.$row['id'].'</td>';
      echo '<td>'.$row['pattern_name'].'</td>';
      echo '<td>'.$row['description'].'</td>';
      echo '<td>'.$row['name'].'</td>';
      echo '<td><a href="./index.php?do=editpattern&amp;id='.$row['id'].'">Details</a></td>';
      echo '</tr>';
    }
    echo '</table>';
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function editpattern(){
  if (checkaccess('crafting','read')){
    echo '<p class="header">Pattern Details</p>';
    if (isset($_GET['id'])){
      $pattern_id = mysql_real_escape_string($_GET['id']);
    }else{
      echo '<p class="error">No Pattern ID specified</p>';
      listpatterns();
      return;
    }
    if ((checkaccess('crafting', 'edit')) && isset($_POST['commit']) && ($_POST['commit']=='Update Pattern')){
      $pattern_name = mysql_real_escape_string($_POST['pattern_name']);
      $description = mysql_real_escape_string($_POST['description']);
      $id = mysql_real_escape_string($_GET['id']);
      $query = "UPDATE trade_patterns SET pattern_name='$pattern_name', description='$description' WHERE id='$id'";
      $result = mysql_query2($query);
      echo '<p class="error">Update Successful</p>';
      unset($_POST);
      editpattern();
      return;
    }else{
      $query = "SELECT id, name FROM item_stats";
      $Temp = mysql_query2($query);
      while ($row=mysql_fetch_array($Temp, MYSQL_ASSOC)){
        $iid=$row['id'];
        $Items["$iid"]=$row['name'];
      }
      $query = "SELECT pattern_name, description, designitem_id FROM trade_patterns WHERE id='$pattern_id'";
      $result = mysql_query2($query);
      $row = mysql_fetch_array($result, MYSQL_ASSOC);
      if (checkaccess('crafting', 'edit')){
        $delete_text = (checkaccess('crafting','delete') ? '<a href="./index.php?do=deletepattern&id='.$pattern_id.'">Delete Pattern</a>' : "");
        echo '<form action="./index.php?do=editpattern&amp;id='.$pattern_id.'" method="post">';
        echo '<table><tr><td>Pattern Name:</td><td><input type="text" name="pattern_name" value="'.$row['pattern_name'].'"/></td></tr>';
        echo '<tr><td>Pattern Description:</td><td><textarea name="description" rows="5" cols="40">'.$row['description'].'</textarea></td></tr>';
        $i = $row['designitem_id'];
        echo '<tr><td>Design Item:</td><td>'.$Items["$i"].'</td></tr>';
        echo '<tr><td><input type="submit" name="commit" value="Update Pattern"/></td><td>'.$delete_text.'</td></tr>';
        echo '</table>';
        echo '</form>';
      }else{
        echo '<table>';
        echo '<tr><td>Pattern Name:</td><td>'.$row['pattern_name'].'</td></tr>';
        echo '<tr><td>Pattern Description:</td><td>'.$row['description'].'</td></tr>';
        $i = $row['designitem_id'];
        echo '<tr><td>Design Item:</td><td>'.$Items["$i"].'</td></tr>';
      }
      echo '<p class="bold">Available Transforms</p>';
      $query = "SELECT t.id, t.process_id, p.name, t.result_id, t.result_qty, t.item_id, t.item_qty, t.trans_points, t.penilty_pct, t.description FROM trade_transformations AS t LEFT JOIN trade_processes AS p ON t.process_id=p.process_id WHERE pattern_id='$pattern_id' GROUP BY id";
      $result = mysql_query2($query);
      echo '<table><tr><th>Source Item</th><th>Process</th><th>Result Item</th><th>Time</th><th>Resultant Quality</th><th>Actions</th></tr>';
      $Alt = FALSE;
      while ($row=mysql_fetch_array($result, MYSQL_ASSOC)){
        $Alt = !$Alt;
        if ($Alt){
          echo '<tr class="color_a">';
        }else{
          echo '<tr class="color_b">';
        }
        $item_id=$row['item_id'];
        echo '<td>'.$row['item_qty'].' '.$Items["$item_id"].'</td>';
        echo '<td><a href="./index.php?do=process&amp;id='.$row['process_id'].'">'.$row['name'].'</a></td>';
        $result_id=$row['result_id'];
        echo '<td>'.$row['result_qty'].' '.$Items["$result_id"].'</td>';
        echo '<td>'.$row['trans_points'].'</td>';
        echo '<td>'.$row['penilty_pct'].'</td>';
        echo '<td><a href="./index.php?do=transform&amp;id='.$row['id'].'">Edit</a></td>';
        echo '</tr>';
      }
      echo '</table>';
      echo '<a href="./index.php?do=createtransform&amp;id='.$pattern_id.'">Create new transform for this pattern </a><br />';
      echo '<p class="bold">Available Combinations</p>';
      $Alt = FALSE;
      $item = -1;
      $query = "SELECT result_id, result_qty, item_id, min_qty, max_qty, description FROM trade_combinations WHERE pattern_id='$pattern_id' ORDER BY result_id";
      $result = mysql_query2($query);
      if (mysql_num_rows($result)!= 0){
        echo '<table><tr><th>Result Item</th><th>Source Items</th><th>Actions</th></tr>';
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
          if ($item != $row['result_id']){
            if ($item != '-1'){
              echo '</td><td><a href="./index.php?do=editcombine&amp;id='.$item.'&amp;pattern_id='.$_GET['id'].'">Edit</a></td></tr>'."\n";
            }
            $item = $row['result_id'];
            $Alt = !$Alt;
            if ($Alt){
              echo '<tr class="color_a">';
            }else{
              echo '<tr class="color_b">';
            }
            $result_id = $row['result_id'];
            echo '<td>'.$row['result_qty'].' '.$Items["$result_id"].'</td>';
            $item_id = $row['item_id'];
            echo '<td>'.$row['min_qty'].' to '.$row['max_qty'].' '.$Items["$item_id"];
          }else{
            echo '<br/>';
            $item_id = $row['item_id'];
            echo $row['min_qty'].' to '.$row['max_qty'].' '.$Items["$item_id"];
          }
        }
        echo '<td><a href="./index.php?do=editcombine&amp;id='.$item.'&amp;pattern_id='.$_GET['id'].'">Edit</a></td></tr></table>';
        echo '<a href="./index.php?do=createcombine&amp;pattern_id='.$pattern_id.'">Create new combine for this pattern </a><br />';
      }else{
        echo '<p class="error">No available Combines</p>';
        echo '<a href="./index.php?do=createcombine&amp;pattern_id='.$pattern_id.'">Create new combine for this pattern </a><br />';
      }
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function createpattern() 
{
    if (checkaccess('crafting','create') && isset($_POST['commit']) && ($_POST['commit']=='Create Pattern')) // submit pattern
    {
        $pattern_name = mysql_real_escape_string($_POST['pattern_name']);
        $group_id = mysql_real_escape_string($_POST['group_id']);
        $designitem_id = mysql_real_escape_string($_POST['designitem_id']);
        $k_factor = mysql_real_escape_string($_POST['k_factor']);
        $description = mysql_real_escape_string($_POST['description']);
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
        $password = mysql_real_escape_string($_POST['passd']);
        $username = mysql_real_escape_string($_SESSION['username']);
        $query = "SELECT COUNT(username) FROM accounts WHERE username='$username' AND password=MD5('$password')";
        $result = mysql_query2($query);
        $row = mysql_fetch_row($result);
        if ($row[0] == 1)
        {
            $id = mysql_real_escape_string($_GET['id']);
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
        $Temp = mysql_query2($query);
        while ($row=mysql_fetch_array($Temp, MYSQL_ASSOC)){
            $iid=$row['id'];
            $items["$iid"]=$row['name'];
        }
        $query = "SELECT pattern_name, description, designitem_id FROM trade_patterns WHERE id='$pattern_id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);

        echo '<table><tr><td>Pattern Name:</td><td>'.$row['pattern_name'].'</td></tr>';
        echo '<tr><td>Pattern Description:</td><td>'.$row['description'].'</td></tr>';
        $i = $row['designitem_id'];
        echo '<tr><td>Design Item:</td><td>'.$items["$i"].'</td></tr>';
        echo '</table>';

        echo '<p class="bold">Available Transforms</p>';
        $query = "SELECT t.id, t.process_id, p.name, t.result_id, t.result_qty, t.item_id, t.item_qty, t.trans_points, t.penilty_pct, t.description FROM trade_transformations AS t LEFT JOIN trade_processes AS p ON t.process_id=p.process_id WHERE pattern_id='$pattern_id' GROUP BY id";
        $result = mysql_query2($query);
        echo '<table><tr><th>Source Item</th><th>Process</th><th>Result Item</th><th>Time</th><th>Resultant Quality</th></tr>';
        $alt = FALSE;
        while ($row=mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $alt = !$alt;
            if ($alt){
                echo '<tr class="color_a">';
            }
            else
            {
                echo '<tr class="color_b">';
            }
            $item_id=$row['item_id'];
            echo '<td>'.$row['item_qty'].' '.$items["$item_id"].'</td>';
            echo '<td><a href="./index.php?do=process&amp;id='.$row['process_id'].'">'.$row['name'].'</a></td>';
            $result_id=$row['result_id'];
            echo '<td>'.$row['result_qty'].' '.$items["$result_id"].'</td>';
            echo '<td>'.$row['trans_points'].'</td>';
            echo '<td>'.$row['penilty_pct'].'</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '<p class="bold">Available Combinations</p>';
        $alt = FALSE;
        $item = -1;
        $query = "SELECT result_id, result_qty, item_id, min_qty, max_qty, description FROM trade_combinations WHERE pattern_id='$pattern_id' ORDER BY result_id";
        $result = mysql_query2($query);
        if (mysql_num_rows($result) != 0)
        {
            echo '<table><tr><th>Result Item</th><th>Source Items</th></tr>';
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
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
                    echo '<td>'.$row['result_qty'].' '.$items["$result_id"].'</td>';
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
        
        echo '<form action="./index.php?do=deletepattern&id='.$_GET['id'].'" method="post">Enter your password to confirm deletion of *ALL* items listed above: <input type="password" name="passd" /><input type="submit" name="submit" value="Confirm Delete"></form>';

    }
    else
    {
        echo 'You do not have access to delete patterns, or did not provide a valid ID';
    }
}

?>
