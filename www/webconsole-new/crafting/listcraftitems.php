<?php
function listcraftitems(){
  if(checkaccess('items', 'read')){
    $categories = "7,12,20,30,53,54,55,10,11,56,8,6,9,32,36,38,48,40,37,44,45,39,35,46,49,33,34,47";
    $query = 'SELECT category_id, name FROM item_categories where category_id in ('.$categories.') order by name';
    $result = mysql_query2($query);

    echo '<b>NOTE</b>: Craftable items are the ones with (transform) or (combination) next to them.<br/><br/>';
    echo '<table border="1" class="top">';
    echo '<tr><th>Category</th><th>Items</th><th>Details</th></tr>';
    echo '<tr class="top"><td>';
    while ($row=mysql_fetch_array($result, MYSQL_ASSOC)){
      if (isset($_GET['category']) && ($_GET['category']==$row['category_id']))
        echo '<b>';
      echo '<a href="./index.php?do=listcraftitems&amp;category='.$row['category_id'];
      echo '">'.$row['name'].'</a>';
      if (isset($_GET['category']) && ($_GET['category']==$row['category_id']))
        echo '</b>';
      echo '<br/>';
    }
    echo '</td><td>';
    if (isset($_GET['category'])){
        $category = mysql_real_escape_string($_GET['category']);
        $query = 'SELECT DISTINCT (i.id), t.pattern_id as tid, c.pattern_id as cid, name FROM item_stats AS i LEFT JOIN trade_transformations AS t ON i.id=t.result_id LEFT JOIN trade_combinations as c ON i.id=c.result_id WHERE category_id ='.$category;
        $query = $query." AND stat_type = 'B'";
        $query .= ' ORDER BY name';
        $result = mysql_query2($query);
        while ($row = mysql_fetch_array($result)){
          echo '<a href="./index.php?do=listitems&amp;category='.$_GET['category'].'&amp;item='.$row['id'].'">'.$row['name'].'</a>';
          if ($row['tid'])
            echo '(<a href=index.php?do=editpattern&id='.$row['tid'].'>transform</a>)';
          if ($row['cid'])
            echo '(<a href=index.php?do=editpattern&id='.$row['cid'].'>combination</a>)';
          echo '<br/>';
        }
    }
    echo '</td><td>';
    echo '</td></tr>';
    echo '</table>';
  }else{
    echo '<p class="error">You are not authorized to use these functions!</p>';
  }
}

?>
