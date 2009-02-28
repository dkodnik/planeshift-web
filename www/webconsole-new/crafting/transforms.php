<?
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
      $item_qty = mysql_real_escape_string($_POST['item_qty']);
      $item_id = mysql_real_escape_string($_POST['item_id']);
      $process_id = mysql_real_escape_string($_POST['process_id']);
      $result_qty = mysql_real_escape_string($_POST['result_qty']);
      $result_id = mysql_real_escape_string($_POST['result_id']);
      $trans_points = mysql_real_escape_string($_POST['trans_points']);
      $penilty_pct = mysql_real_escape_string($_POST['penilty_pct']);
      $query = "UPDATE trade_transformations SET item_qty='$item_qty', item_id='$item_id', process_id='$process_id', result_qty='$result_qty', result_id='$result_id', trans_points='$trans_points', penilty_pct='$penilty_pct' WHERE id='$id'";
      $result = mysql_query2($query);
      echo '<p class="error">Update Successful</p>';
      unset($_POST);
      edittransform();
      return;
    }else{
      $id = mysql_real_escape_string($_GET['id']);
      $Items = PrepSelect('items');
      $Process = PrepSelect('process');
      $query = "SELECT process_id, result_id, result_qty, item_id, item_qty, trans_points, penilty_pct, description FROM trade_transformations WHERE id='$id'";
      $result = mysql_query2($query);
      $row = mysql_fetch_array($result, MYSQL_ASSOC);
      echo '<p class="header">Transformation Details</p>';
      echo '<form action="./index.php?do=transform&amp;id='.$id.'" method="post"><table>';
      echo '<tr><td>Source Item</td><td><input type="text" name="item_qty" value="'.$row['item_qty'].'" size="4"/> '.DrawSelectBox('items', $Items, 'item_id', $row['item_id']).'</td></tr>';
      echo '<tr><td>Process</td><td>'.DrawSelectBox('process', $Process, 'process_id', $row['process_id']).'</td></tr>';
      echo '<tr><td>Result Item</td><td><input type="text" name="result_qty" value="'.$row['result_qty'].'" size="4"/> '.DrawSelectBox('items', $Items, 'result_id', $row['result_id']).'</td></tr>';
      echo '<tr><td>Description</td><td><textarea name="description" rows="4" cols="40">'.$row['description'].'</textarea></td></tr>';
      echo '<tr><td>Time Taken</td><td><input type="text" name="trans_points" value="'.$row['trans_points'].'"/></td></tr>';
      echo '<tr><td>Resultant Quality factor(0-1)</td><td><input type="text" name="penilty_pct" value="'.$row['penilty_pct'].'"/></td></tr>';
      echo '<tr><td><input type="submit" name="commit" value="Update Transform"/></td></tr>';
      echo '</table></form>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
