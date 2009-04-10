<?php
function deleteitem(){
  if (checkaccess('items', 'delete')){
    if (isset($_GET['commit']) && isset($_POST['passd'])){
      $id = mysql_real_escape_string($_GET['id']);
      $password = mysql_real_escape_string($_POST['passd']);
      $username = mysql_real_escape_string($_SESSION['username']);
      $query = "SELECT COUNT(username) FROM accounts WHERE username='$username' AND password=MD5('$password')";
      $result = mysql_query2($query);
      $row = mysql_fetch_row($result);
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
      $id = mysql_real_escape_string($_GET['item']);
      $query = "SELECT COUNT(id) FROM item_instances WHERE item_stats_id_standard=$id";
      $result = mysql_query2($query);
      $row = mysql_fetch_row($result);
      $num = $row[0];
      $query = "SELECT name FROM item_stats WHERE id=$id";
      $result = mysql_query2($query);
      $row = mysql_fetch_array($result, MYSQL_ASSOC);
      echo '<p>You are about to permanently delete item id '.$id.' and all related instances<br/>Item Name: '.$row['name'].'<br/>Number of Instances: '.$num.'</p>';
      echo '<form action="./index.php?do=deleteitem&amp;commit&amp;id='.$id.'" method="post">Enter your password to confirm: <input type="password" name="passd" /><input type="submit" name="submit" value="Confirm Delete"></form>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
