<?php
function listcraftitems(){
  if(checkaccess('items', 'read')){
    $query = 'SELECT category_id, name FROM item_categories order by name';
    $result = mysql_query2($query);

    echo '<b>NOTE</b>: Craftable items are the ones with (transform) or (combination) next to them.<br/><br/>';
    echo '<table border="1" class="top">';
    echo '<tr><th>Category</th><th>Items</th><th>Details</th></tr>';
    echo '<tr class="top"><td>';
    while ($row=fetchSqlAssoc($result)){
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
        $category = escapeSqlString($_GET['category']);
        $query = 'SELECT DISTINCT (i.id), t.pattern_id as tid, c.pattern_id as cid, name FROM item_stats AS i LEFT JOIN trade_transformations AS t ON i.id=t.result_id LEFT JOIN trade_combinations as c ON i.id=c.result_id WHERE category_id ='.$category;
        $query = $query." AND stat_type = 'B'";
        $query .= ' ORDER BY name';
        $result = mysql_query2($query);
        while ($row = fetchSqlAssoc($result)){
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
