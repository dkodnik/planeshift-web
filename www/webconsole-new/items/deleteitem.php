<?php
function deleteitem(){
  if (checkaccess('items', 'delete')){
    if (isset($_GET['commit']) && isset($_POST['passd'])){
      $id = escapeSqlString($_GET['id']);
      $password = escapeSqlString($_POST['passd']);
      $username = escapeSqlString($_SESSION['username']);
      $query = "SELECT COUNT(username) FROM accounts WHERE username='$username' AND password=MD5('$password')";
      $result = mysql_query2($query);
      $row = fetchSqlRow($result);
      if ($row[0] == 1){
        $query = "DELETE FROM item_stats WHERE id=$id";
        $result = mysql_query2($query);
        $query = "DELETE FROM item_instances WHERE item_stats_id_standard=$id";
        $result = mysql_query2($query);
?>
        <SCRIPT language="javascript">
          document.location = "index.php?do=listitems";
        </script>
<?php
      }else{
        echo '<p class="error">Password check failed - Did Not Delete item</p>';
        include('./items/listitems.php');
        $_GET['item'] = $id;
        listitems();
        return;
      }
    }else{
      $id = escapeSqlString($_GET['item']);
      include('./items/listitems.php');
      echo 'Looking up usage for this item.';
      if (showitemusage()) 
      {
        echo '<p class="error"> You cannot delete this item because it is still in use.';
        return;
      }
      $query = "SELECT name FROM item_stats WHERE id=$id";
      $result = mysql_query2($query);
      $row = fetchSqlAssoc($result);
      echo '<p>No usage of this item was found <br /><br />You are about to permanently delete item id '.$id.' Item Name: '.$row['name'].'</p>';
      echo '<form action="./index.php?do=deleteitem&amp;commit&amp;id='.$id.'" method="post">Enter your password to confirm: <input type="password" name="passd" /><input type="submit" name="submit" value="Confirm Delete"></form>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
