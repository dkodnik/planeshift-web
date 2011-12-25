<?php
function listtribes(){
  if (checkaccess('npcs', 'read')){
    echo '<table border="1">';
    echo '<tr><th><a href="./index.php?do='.$_GET['do'].'&amp;sort=id">ID</a></th><th><a href="./index.php?do='.$_GET['do'].'&amp;sort=name">Name</a></th><th>Home Position</th><th>Home Radius</th><th><a href="./index.php?do='.$_GET['do'].'&amp;sort=sector">Home Sector</a></th><th>Max size</th><th>Wealth Resource Name</th><th>Wealth Resource Nick</th><th>Wealth Resource Area</th><th>Wealth Gather Need</th><th>Wealth Resource Growth</th><th>Wealth Resource Growth Active</th><th>Wealth Resource Growth Active Limit</th><th>Reproduction Cost</th><th>NPC idle behavior</th><th>Tribal Recipe</th></tr>';
    $query = 'SELECT t.*, s.name AS sector FROM tribes AS t LEFT JOIN sectors AS s ON t.home_sector_id=s.id';

    if (isset($_GET['sort'])){
      if ($_GET['sort'] == 'id'){
        $query = $query . ' ORDER BY id';
      }else if ($_GET['sort'] == 'name'){
        $query = $query . ' ORDER BY name';
      }
    }
    $result = mysql_query2($query);
    if (mysql_num_rows($result) > 0){
      while ($row = mysql_fetch_array($result)){
        echo '<tr>';
        echo '<td>'.$row['id'].'</td>';
        $T_id = $row['id'];
          echo '<td><a href="./index.php?do=listtribemembers&amp;id='.$row['id'].'">'.$row['name'].'</a></td>';

        echo '<td>'.$row['home_x'].' / '.$row['home_y'].' / '.$row['home_z'].' / ? </td>';
        echo '<td>'.$row['home_radius'].'</td>';
        echo '<td>'.$row['sector'].'</td>';
        echo '<td>'.$row['max_size'].'</td>';
        echo '<td>'.$row['wealth_resource_name'].'</td>';
        echo '<td>'.$row['wealth_resource_nick'].'</td>';
        echo '<td>'.$row['wealth_resource_area'].'</td>';
        echo '<td>'.$row['wealth_gather_need'].'</td>';
        echo '<td>'.$row['wealth_resource_growth'].'</td>';
        echo '<td>'.$row['wealth_resource_growth_active'].'</td>';
        echo '<td>'.$row['wealth_resource_growth_active_limit'].'</td>';
        echo '<td>'.$row['reproduction_cost'].'</td>';
        echo '<td>'.$row['npc_idle_behavior'].'</td>';
        echo '<td>'.$row['tribal_recipe'].'</td>';
        echo '</tr>';
      }
      echo '</table>';
    }else{
      echo '</table>';
      echo '<p class="error">No NPCs Found</p>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
