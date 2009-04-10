<?php
function listitems(){
  if(checkaccess('items', 'read')){
    $query = 'SELECT category_id, name FROM item_categories ORDER BY name';
    $result = mysql_query2($query);
    echo '<p><a href="./index.php?do=listitems';
    if (isset($_GET['category'])){
      echo '&amp;category='.$_GET['category'];
    }
    if (isset($_GET['item'])){
      echo '&amp;item='.$_GET['item'];
    }
    if (isset($_GET['override1'])){
      echo '&amp;override1';
    }
    if (!isset($_GET['override2'])){
      echo '&amp;override2';
    }
    echo '">Toggle Personalized Items ';
    if (isset($_GET['override2'])){
      echo 'OFF';
    }else{
      echo 'ON';
    }
    echo '</a></p>';
    echo '<table border="1" class="top">';
    echo '<tr><th>Category</th><th>Items</th><th>Details</th></tr>';
    echo '<tr class="top"><td>';
    while ($row=mysql_fetch_array($result, MYSQL_ASSOC)){
      if (isset($_GET['category']) && ($_GET['category']==$row['category_id']))
        echo '<b>';
      echo '<a href="./index.php?do=listitems&amp;category='.$row['category_id'];
      if (isset($_GET['override2'])){
        echo '&amp;override2';
      }
      echo '">'.$row['name'].'</a>';
      if (isset($_GET['category']) && ($_GET['category']==$row['category_id']))
        echo '</b>';
      echo '<br/>';
    }
    echo '</td><td>';
    if (isset($_GET['category'])){
      if (isset($_GET['item']) && !isset($_GET['override1'])){
        echo 'List suppressed<br/><a href="./index.php?do=listitems&amp;override1';
        if (isset($_GET['override2'])){
          echo '&amp;override2';
        }
        echo '&amp;category='.$_GET['category'].'&amp;item='.$_GET['item'].'">Override</a>';
      }else{
        $category = mysql_real_escape_string($_GET['category']);
        $query = 'SELECT id, name FROM item_stats WHERE category_id ='.$category;
        if (!isset($_GET['override2'])){
          $query = $query." AND stat_type = 'B'";
        }
        $result = mysql_query2($query);
        while ($row = mysql_fetch_array($result)){
          echo '<a href="./index.php?do=listitems&amp;category='.$_GET['category'].'&amp;item='.$row['id'];
          if (isset($_GET['override2'])){
            echo '&amp;override2';
          }
          echo '">'.$row['name'].'</a><br/>';
        }
      }
    }
    echo '</td><td>';
    if (isset($_GET['item'])){
      $id = mysql_real_escape_string($_GET['item']);
      $query = 'SELECT * FROM item_stats WHERE id='.$id;
      $result = mysql_query2($query);
      $row = mysql_fetch_array($result, MYSQL_ASSOC);
      echo '<table border="1" class="top">';
      if (checkaccess('items','edit')){
        echo '<tr><td>Item Actions:</td><td><a href="./index.php?do=edititem&amp;item='.$_GET['item'].'">Edit Item</a>';
      }
      if (checkaccess('items','delete')){
        echo ' -- <a href="./index.php?do=deleteitem&amp;item='.$_GET['item'].'">Delete Item</a>';
      }
      echo '</td></tr>';
      foreach ($row as $key=>$value){
        echo '<tr><td>'.$key.'</td><td>'.htmlspecialchars($value).'</td></tr>';
      }
      echo '</table>';
    }
    echo '</tr>';
    echo '</table>';
  }else{
    echo '<p class="error">You are not authorized to use these functions!</p>';
  }
}
?>
