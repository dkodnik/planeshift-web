<?php
function edittransform(){
  if (checkaccess('crafting', 'edit')){
    if (!isset($_GET['id'])){
      echo '<p class="error">Error: No Transform Specified</p>';
      include('./crafting/patterns.php');
      listpatterns();
      return;
    }
    if (isset($_POST['commit']) && $_POST['commit'] == "Update Transform"){
      $id = mysql_real_escape_string($_GET['id']);
      $pattern_id = mysql_real_escape_string($_POST['pattern_id']);
      $item_qty = mysql_real_escape_string($_POST['item_qty']);
      $item_id = mysql_real_escape_string($_POST['item_id']);
      $item_id = ($item_id == '' ? 0 : $item_id); // change id to 0 if it is not provided by user ('')
      $process_id = mysql_real_escape_string($_POST['process_id']);
      $result_qty = mysql_real_escape_string($_POST['result_qty']);
      $result_id = mysql_real_escape_string($_POST['result_id']);
      $result_id = ($result_id == '' ? 0 : $result_id);
      $trans_points = mysql_real_escape_string($_POST['trans_points']);
      $penalty_pct = mysql_real_escape_string($_POST['penalty_pct']);
      if ($item_id =='0' && $result_id=='0')
      {
        echo '<p class="error">Source and Result item can not both be empty.</p>';
        return;
      }
      $query = "UPDATE trade_transformations SET pattern_id='$pattern_id', item_qty='$item_qty', item_id='$item_id', process_id='$process_id', result_qty='$result_qty', result_id='$result_id', trans_points='$trans_points', penalty_pct='$penalty_pct' WHERE id='$id'";
      $result = mysql_query2($query);
      echo '<p class="error">Update Successful</p>';
      unset($_POST);
      edittransform();
      return;
    }else{
      $id = mysql_real_escape_string($_GET['id']);
      $items = PrepSelect('items');
      $process = PrepSelect('process');
      $patterns = PrepSelect('patterns');
      $query = "SELECT pattern_id, process_id, result_id, result_qty, item_id, item_qty, trans_points, penalty_pct, description FROM trade_transformations WHERE id='$id'";
      $result = mysql_query2($query);
      $row = mysql_fetch_array($result, MYSQL_ASSOC);
      $delete_text = (checkaccess('crafting','delete') ? '<a href="./index.php?do=deletetransform&amp;id='.$id.'">Delete Transform</a>' : "");
      echo '<p class="header">Transformation Details</p>';
      echo '<form action="./index.php?do=transform&amp;id='.$id.'" method="post"><table>';
      echo '<tr><td colspan="2">If you change this dropdown, you will move this transformation to another pattern, moving it to "NONE" will make it "patternless".</td></tr>';
      echo '<tr><td>Pattern</td><td>'.DrawSelectBox('patterns', $patterns, 'pattern_id', $row['pattern_id'], true).'</td></tr>';
      echo '<tr><td>Source Item</td><td><input type="text" name="item_qty" value="'.$row['item_qty'].'" size="4"/> '.DrawItemSelectBox('item_id', $row['item_id'], false, true).'</td></tr>';
      echo '<tr><td>Process</td><td>'.DrawSelectBox('process', $process, 'process_id', $row['process_id']).'</td></tr>';
      echo '<tr><td>Result Item</td><td><input type="text" name="result_qty" value="'.$row['result_qty'].'" size="4"/> '.DrawItemSelectBox('result_id', $row['result_id'], true, true).'</td></tr>';
      echo '<tr><td>Description</td><td><textarea name="description" rows="4" cols="40">'.$row['description'].'</textarea></td></tr>';
      echo '<tr><td>Time Taken</td><td><input type="text" name="trans_points" value="'.$row['trans_points'].'"/></td></tr>';
      echo '<tr><td>Resultant Quality factor(0-1)</td><td><input type="text" name="penalty_pct" value="'.$row['penalty_pct'].'"/></td></tr>';
      echo '<tr><td><input type="submit" name="commit" value="Update Transform"/> </td><td>'.$delete_text.'</td></tr>';
      echo '</table></form>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function createtransform()
{
    if (checkaccess('crafting','create') && isset($_POST['commit']) && $_POST['commit'] == "Create Transformation")
    {
        $pattern_id = mysql_real_escape_string($_POST['pattern_id']);
        $item_id = mysql_real_escape_string($_POST['item_id']);
        $item_id = ($item_id == '' ? 0 : $item_id); // change id to 0 if it is not provided by user ('')
        $item_qty = mysql_real_escape_string($_POST['item_qty']);
        $process_id = mysql_real_escape_string($_POST['process_id']);
        $trans_points = mysql_real_escape_string($_POST['trans_points']);
        $result_id = mysql_real_escape_string($_POST['result_id']);
        $result_id = ($result_id == '' ? 0 : $result_id);
        $result_qty = mysql_real_escape_string($_POST['result_qty']);
        $penalty_pct = mysql_real_escape_string($_POST['penalty_pct']);
        if ($item_id =='0' && $result_id=='0')
        {
            echo '<p class="error">Source and Result item can not both be empty.</p>';
            return;
        }
        $query = "INSERT INTO trade_transformations ( pattern_id, item_id, item_qty, process_id, trans_points, result_id, result_qty, penalty_pct ) VALUES ('$pattern_id', '$item_id', '$item_qty', '$process_id', '$trans_points', '$result_id', '$result_qty', '$penalty_pct')";
        mysql_query2($query);
        if(isset($_GET['id'])) // "redirect the user back to where they came from (if they came from somewhere).
        {
            echo '<p class="error">Transform Creation Successful</p>';
            include('./crafting/patterns.php');
            editpattern();
        }
        else // or bring the user back to the create tranfrom from.
        {
            echo '<p class="error">Transform Creation Successful</p>';
            unset($_POST);
            createtransform();
        }
        
    }
    elseif (checkaccess('crafting','create'))
    {
        echo '<p class="bold">Create Transformation</p>'."\n"; // new transformation
        
        $redir = (isset($_GET['id']) ? "&amp;id={$_GET['id']}" : "");  // set this value if the script was called from a certain pattern, and if it was, return to there after inserting the data.
        echo '<form action="./index.php?do=createtransform'.$redir.'" method="post" /><table>';
        
        if(isset ($_GET['id']))
        {
            echo '<tr><td>Pattern id</td><td><input type="hidden" name="pattern_id" value="'.$_GET['id'].'" />'.$_GET['id'].'</td></tr>';
        }
        else
        {
            $pattern_results = PrepSelect('patterns');
            echo '<tr><td>Pattern id</td><td>'.DrawSelectBox('patterns', $pattern_results, 'pattern_id', '', false).'</td></tr>';
        }
        echo '<tr><td>(amount) Source Item</td><td><input type="text" name="item_qty" size="4" /> ';
        $items_results = PrepSelect('items');
        echo DrawSelectBox('items', $items_results, 'item_id', '', true).'</td></tr>';
        $process_results = PrepSelect('process');
        echo '<tr><td>Process</td><td>'.DrawSelectBox('process', $process_results, 'process_id', '', false).'</td></tr>';
        echo '<tr><td>Time Taken</td><td><input type="text" name="trans_points" /></td></tr>';
        echo '<tr><td>(amount) Result Item</td><td><input type="text" name="result_qty" size="4"/> ';
        echo DrawSelectBox('items', $items_results, 'result_id', '', true).'</td></tr>';
        echo '<tr><td>Resultant Quality factor(0-1)</td><td><input type="text" name="penalty_pct" /></td></tr>';
        echo '<tr><td></td><td><input type=submit name="commit" value="Create Transformation"/></td></tr>';
        echo '</table></form>'."\n";
    }
    else
    {
        echo '<p class="error">You are not authorized to do this operation</p>';
    }
}


function deletetransform()
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
            $query = "DELETE FROM trade_transformations WHERE id = $id LIMIT 1"; //limit is not needed, but if something unexpected does happen, it'll only affect 1 transform.
            mysql_query2($query);
            echo '<p class="error">Transformation with ID '.$id.' was succesfully deleted.</p>';
        }
        else
        {
            echo '<p class="error">Password check failed - Did Not Delete Transform</p>';
        }
    }
    elseif (checkaccess('crafting','delete') && isset($_GET['id']) && is_numeric($_GET['id']))
    {
        echo '<p>You are about to permanently delete transform id '.$_GET['id'].'</p>';
        echo '<form action="./index.php?do=deletetransform&amp;id='.$_GET['id'].'" method="post">Enter your password to confirm: <input type="password" name="passd" /><input type="submit" name="submit" value="Confirm Delete"></form>';
    }
    else
    {
        echo 'You do not have access to delete transformations, or did not provide a valid ID';
    }
}
?>
