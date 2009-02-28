<?
function editcategory(){
  if (checkaccess('items', 'create')){
    if (isset($_GET['commit'])){
      $id = mysql_real_escape_string($_GET['id']);
      $name = mysql_real_escape_string($_POST['name']);
      $query = "UPDATE item_categories SET name='$name' WHERE category_id=".$id;
      $result = mysql_query2($query);
?>
    <SCRIPT language="javascript">
      document.location = "index.php?do=editcategory";
    </script>
<?
    exit;
    }else{
      $query = "SELECT category_id, name FROM item_categories ORDER BY name ASC";
      $result = mysql_query2($query);
      $q2 = 'SELECT c.category_id, COUNT(i.id) AS items FROM item_categories AS c LEFT JOIN item_stats AS i ON c.category_id=i.category_id GROUP by c.category_id';
      $r2 = mysql_query2($q2);
      while ($i_row = mysql_fetch_array($r2, MYSQL_ASSOC)){
        $C_id=$i_row['category_id'];
        $Count["$C_id"]['items']= $i_row['items'];
      }
      unset($r2);
      unset($i_row);
      $q2 = 'SELECT c.category_id, COUNT(i.category_id) AS merchants FROM item_categories AS c LEFT JOIN merchant_item_categories AS i ON c.category_id=i.category_id GROUP by c.category_id';
      $r2 = mysql_query2($q2);
      while ($i_row = mysql_fetch_array($r2, MYSQL_ASSOC)){
        $C_id=$i_row['category_id'];
        $Count["$C_id"]['merchants']= $i_row['merchants'];
      }
      echo '<table border="1"><tr><th>ID</th><th>Items</th><th>merchants</th><th>Category Name /Actions</th></tr>'."\n";
      while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        $C_id = $row['category_id'];
        echo '<tr><td>'.$row['category_id'].'</td><td>'.$Count["$C_id"]['items'].'</td><td>'.$Count["$C_id"]['merchants'].'</td><td class="center"><form action="./index.php?do=editcategory&amp;commit&amp;id='.$row['category_id'].'" method="post"><input type="text" name="name" value="'.$row['name'].'" /><br/>';
        if (($Count["$C_id"]['items'] == 0) && ($Count["$C_id"]['merchants'] == 0) && (checkaccess('items', 'delete'))){
          echo '<a href="./index.php?do=deletecategory&amp;id='.$row['category_id'].'">Delete</a> -- ';
        }
        echo '<input type="submit" name="edit" value="Update" /></form></td></tr>'."\n";
      }
      if (checkaccess('items', 'create')){
        echo '<tr><td>N/A</td><td>&nbsp;</td><td>&nbsp;</td><td class="center"><form action="./index.php?do=createcategory" method="post"><input type="text" name="category" /><br/><input type="submit" name="submit" value="Create Category" /></form></td></tr>';
      }
      echo '</table>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function createcategory(){
  if (checkaccess('items', 'create')){
    $category = mysql_real_escape_string($_POST['category']);
    $query = "INSERT INTO item_categories SET name='$category'";
    $result = mysql_query2($query);
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
