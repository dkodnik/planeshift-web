<?php
function listmerchant(){
  if (checkaccess('npcs', 'read')){
    $query = 'SELECT category_id, name FROM item_categories';
    $result = mysql_query2($query);
    while ($row = fetchSqlAssoc($result)){
      $C_id = $row['category_id'];
      $Categories["$C_id"] = $row['name'];
    }
    unset($result);
    unset($row);
    $query = "SELECT DISTINCT m.player_id, m.category_id, CONCAT_WS(' ', c.name, c.lastname) AS name, s.name AS sector FROM merchant_item_categories AS m LEFT JOIN characters AS c ON m.player_id=c.id LEFT JOIN sectors AS s ON c.loc_sector_id=s.id ORDER BY sector, name, category_id";
    $result = mysql_query2($query);
    while (list($player_id, $category_id, $name, $sector) = fetchSqlAssoc($result)){
      $Merchant["$player_id"]['id'] = $player_id;
      $Merchant["$player_id"]['name'] = $name;
      $Merchant["$player_id"]['sector'] = $sector;
      $Merchant["$player_id"]['categories'][] = $category_id;
    }
    $query = "SELECT DISTINCT m.player_id, CONCAT_WS(' ', c.name, c.lastname) AS name, s.name AS sector FROM merchant_item_categories AS m LEFT JOIN characters AS c ON m.player_id=c.id LEFT JOIN sectors AS s ON c.loc_sector_id=s.id";
    if (isset($_GET['sort'])){
      if ($_GET['sort'] == 'sector'){
        $query = $query . ' ORDER BY sector, name';
      }else if ($_GET['sort'] == 'name'){
        $query = $query . ' ORDER BY name, sector';
      }
    }
    else{
      $query = $query . ' ORDER BY sector, name';
    }
    $result = mysql_query2($query);
    echo '<table border="1"><tr><th><a href="./index.php?do=listmerchant&amp;sort=name">NPC</a></th><th><a href="./index.php?do=listmerchant&amp;sort=sector">Sector</a></th><th>Categories</th></tr>';
    $cat_result = PrepSelect('category');
    while ($row = fetchSqlAssoc($result)){
      $pid = $row['player_id'];
      $P = $Merchant["$pid"];
      echo '<tr><td>';
      if (checkaccess('npcs', 'edit')){
        echo '<a href="./index.php?do=npc_details&amp;sub=main&amp;npc_id='.$pid.'">'.$P['name'].'</a></td><td>';
      }else{
        echo $P['name'].'</td><td>';
      }
      echo $P['sector'].'</td>';
      echo '<td>';
      foreach ($P['categories'] AS $cat){
        if (checkaccess('npcs', 'edit')){
            echo '<form action="./index.php?do=editmerchant" method="post">';
            echo '<input type="hidden" name="player_id" value="'.$pid.'" />';
            echo '<input type="hidden" name="category_id" value="'.$cat.'" />';
            echo '<input type="submit" name="commit" value="Remove" /> - ';
            echo $Categories["$cat"];
            echo '</form>';
          }else{
            echo $Categories["$cat"].'<br/>';
          }
      }
      if (checkaccess('npcs', 'edit')){
        echo '<form action="./index.php?do=editmerchant" method="post">';
        echo '<input type="hidden" name="player_id" value="'.$pid.'" />';
        echo '<input type="submit" name="commit" value="Add"/>';
        echo DrawSelectBox('category', $cat_result, 'category_id', '');
        echo '</form>';
      }
      echo '</td></tr>';
    }
    echo '</table>';
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function editmerchant(){
  if (checkaccess('npcs', 'edit')){
    if (isset($_POST['commit'])){
      $player_id = escapeSqlString($_POST['player_id']);
      $category_id = escapeSqlString($_POST['category_id']);
      if ($_POST['commit'] == 'Remove'){
        $query = "DELETE FROM merchant_item_categories WHERE player_id='$player_id' AND category_id='$category_id'";
        $result = mysql_query2($query);
      }else if ($_POST['commit'] == 'Add'){
        $query = "INSERT INTO merchant_item_categories (player_id, category_id) VALUES ('$player_id', '$category_id')";
        $result = mysql_query2($query);
      }
      echo '<p class="error">Update Successful</p>';
      listmerchant();
    }else{
      echo '<p class="error">Error: No Commit specified: Edit aborted</p>';
      listmerchant();
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
