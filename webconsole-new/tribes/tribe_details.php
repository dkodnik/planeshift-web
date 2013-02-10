<?php

function tribedetails(){
  if (checkaccess('npcs', 'read')){
    $uri_string = './index.php?do=tribe_details';
    if (isset($_GET['tribe_id'])){
      if (is_numeric($_GET['tribe_id'])){
        $id = mysql_real_escape_string($_GET['tribe_id']);
        $query = "SELECT name FROM tribes WHERE id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<p class="bold" style="float: left; margin: 0pt 5px 0pt 0pt;">Tribe: '.$id.' - '.$row['name'].'</p>';
        if (checkaccess('npcs', 'delete'))
        {
            echo '<form action="index.php?do=edittribes" method="post" style="margin-bottom: 20px; margin-top: 20px;">';
	    echo '<p><input type="hidden" name="id" value="'.$id.'" /><input type="submit" name="commit" value="Delete" /></p>';
            echo '</form>';
        }
        echo "\n";
        $uri_string = $uri_string.'&amp;tribe_id='.$_GET['tribe_id'];
      }
    }
    echo '<div class="menu_npc">';
    echo '<a href="'.$uri_string.'&amp;sub=main">Main</a><br/>';
    echo '<a href="'.$uri_string.'&amp;sub=members">Members</a><br/>';
    echo '<a href="'.$uri_string.'&amp;sub=assets">Assets</a><br/>';
    echo '</div><div class="main_npc">';
    if (isset($_GET['sub'])){
      switch ($_GET['sub']){
        case 'main':
          tribe_main();
          break;
        case 'members':
          tribe_members();
          break;
        case 'assets':
          tribe_assets();
          break;
        default:
          echo '<p class="error">Please Select an Action</p>';
      }
    }else{
      echo '<p class="error">Please Select an Action</p>';
    }
    echo '</div>';
  }else{
    echo '<p class="error">You are not authorized to view Tribe details</p>';
  }
}

function tribe_main(){
  if (checkaccess('npcs', 'read')){
    if (isset($_GET['tribe_id'])){

      // block unauthorized access
      if (isset($_POST['commit']) && !checkaccess('npcs', 'edit')) {
          echo '<p class="error">You are not authorized to edit Tribes</p>';
          return;
      }
      if (!isset($_POST['commit'])){
        $id = mysql_real_escape_string($_GET['tribe_id']);
        $query = 'SELECT * FROM tribes WHERE id='.$id;
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<form action="./index.php?do=tribe_details&amp;sub=main&amp;tribe_id='.$id.'" method="post" id="tribe_details_form"><table>';
        echo '<tr><td>Name:</td><td><input type="text" name="name" value="'.$row['name'].'" /></td></tr>';
        $Sectors = PrepSelect('sectorid');
        echo '<tr><td>Home:</td>';
        echo '<td>';
        echo '<table><tr><th>Sector</th><th>X</th><th>Y</th><th>Z</th><th>Radius</th></tr>';
        echo '<tr><td>'.DrawSelectBox('sectorid', $Sectors, 'home_sector_id', $row['home_sector_id']).'</td><td><input type="text" name="home_x" value="'.$row['home_x'].'" size="5"/></td>';
        echo '<td><input type="text" name="home_y" value="'.$row['home_y'].'" size="5"/></td>';
        echo '<td><input type="text" name="home_z" value="'.$row['home_z'].'" size="5"/></td>';
        echo '<td><input type="text" name="home_radius" value="'.$row['home_radius'].'" size="5"/></td></table></td></tr>';
        echo '<tr><td>Max Size</td><td><input type="text" name="max_size" value="'.$row['max_size'].'" /></td></tr>';
        echo '<tr><td>Wealth reource name:</td><td><input type="text" name="wealth_resource_name" value="'.$row['wealth_resource_name'].'" /></td></tr>';
        echo '<tr><td>Wealth Resource Nick</td><td><input type="text" name="wealth_resource_nick" value="'.$row['wealth_resource_nick'].'" /></td></tr>';
        echo '<tr><td>Wealth Resource Area</td><td><input type="text" name="wealth_resource_area" value="'.$row['wealth_resource_area'].'" /></td></tr>';
        echo '<tr><td>Wealth Gather Need</td><td><input type="text" name="wealth_gather_need" value="'.$row['wealth_gather_need'].'" /></td></tr>';
        echo '<tr><td>Wealth Resource Growth</td><td><input type="text" name="wealth_resource_growth" value="'.$row['wealth_resource_growth'].'" /></td></tr>';
        echo '<tr><td>Wealth Resource Growth Active</td><td><input type="text" name="wealth_resource_growth_active" value="'.$row['wealth_resource_growth_active'].'" /></td></tr>';
        echo '<tr><td>Wealth Resource Growth Active Limit</td><td><input type="text" name="wealth_resource_growth_active_limit" value="'.$row['wealth_resource_growth_active_limit'].'" /></td></tr>';
        echo '<tr><td>Reproduction Cost</td><td><input type="text" name="reproduction_cost" value="'.$row['reproduction_cost'].'" /></td></tr>';
        echo '<tr><td>NPC Idle Behavior</td><td><input type="text" name="npc_idle_behavior" value="'.$row['npc_idle_behavior'].'" /></td></tr>';
        $tribe_recipe = PrepSelect('tribe_recipe');
        echo '<tr><td>Tribal Recipe</td><td>'.DrawSelectBox('tribe_recipe', $tribe_recipe, 'tribal_recipe', $row['tribal_recipe']).'</td></tr>';
        echo '<tr><td colspan="2"><input type="hidden" name="id" value="'.$id.'" /><input type="submit" name="commit" value="Update" /></td></tr>';
        echo '</table></form>';
      }
      else
      {
        $id = mysql_real_escape_string($_POST['id']);
        $name = mysql_real_escape_string($_POST['name']);
        $home_sector_id = mysql_real_escape_string($_POST['home_sector_id']);
        $home_x = mysql_real_escape_string($_POST['home_x']);
        $home_y = mysql_real_escape_string($_POST['home_y']);
        $home_z = mysql_real_escape_string($_POST['home_z']);
        $home_radius = mysql_real_escape_string($_POST['home_radius']);
        $max_size = mysql_real_escape_string($_POST['max_size']);
        $wealth_resource_name = mysql_real_escape_string($_POST['wealth_resource_name']);
        $wealth_resource_nick = mysql_real_escape_string($_POST['wealth_resource_nick']);
        $wealth_resource_area = mysql_real_escape_string($_POST['wealth_resource_area']);
        $wealth_gather_need = mysql_real_escape_string($_POST['wealth_gather_need']);
        $wealth_resource_growth = mysql_real_escape_string($_POST['wealth_resource_growth']);
        $wealth_resource_growth_active = mysql_real_escape_string($_POST['wealth_resource_growth_active']);
        $wealth_resource_growth_active_limit = mysql_real_escape_string($_POST['wealth_resource_growth_active_limit']);
        $reproduction_cost = mysql_real_escape_string($_POST['reproduction_cost']);
        $npc_idle_behavior = mysql_real_escape_string($_POST['npc_idle_behavior']);
        $tribal_recipe = mysql_real_escape_string($_POST['tribal_recipe']);
        $query = "UPDATE tribes SET name='$name', home_sector_id='$home_sector_id', home_x='$home_x', home_y='$home_y', home_z='$home_z', home_radius='$home_radius', max_size='$max_size', wealth_resource_name='$wealth_resource_name', wealth_resource_nick='$wealth_resource_nick', wealth_resource_area='$wealth_resource_area', wealth_gather_need='$wealth_gather_need', wealth_resource_growth='$wealth_resource_growth', wealth_resource_growth_active='$wealth_resource_growth_active', wealth_resource_growth_active_limit='$wealth_resource_growth_active_limit', reproduction_cost='$reproduction_cost', npc_idle_behavior='$npc_idle_behavior', tribal_recipe='$tribal_recipe' WHERE id='$id'";
        mysql_query2($query);
        echo '<p class="error">Tribe Successfully Updated</p>';
        unset($_POST);
        tribe_main();
      }
    }else{
      echo '<p class="error">Error: No NPC Selected</p>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function tribe_members()
{
    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    
    $query = 'SELECT tm.tribe_id, t.name AS tribe_name, tm.member_id, tm.member_type, c.name, tm.flags FROM tribe_members AS tm LEFT JOIN characters AS c ON c.id=tm.member_id LEFT JOIN tribes AS t ON t.id=tm.tribe_id';

    if (isset($_GET['tribe_id']) && is_numeric($_GET['tribe_id'])) 
    {
        $tribe_id = mysql_real_escape_string($_GET['tribe_id']);
        $query .= " WHERE tm.tribe_id='$tribe_id'";
    }
    
    $query .= ' ORDER BY t.name, c.name';
    
    $result = mysql_query2($query);
    if (mysql_num_rows($result) > 0)
    {
        echo '<table border="1">';
        echo '<tr><th>Tribe</th><th>Member Name</th><th>Member Type</th><th>Flags</th></tr>';
        
        while ($row = mysql_fetch_array($result))
        {
            echo '<tr>';
            echo '<td>'.$row['tribe_name'].'</td>';
            echo '<td><a href="./index.php?do=npc_details&amp;sub=main&amp;npc_id='.$row['member_id'].'">'.$row['name'].'</a></td>';
            echo '<td>'.$row['member_type'].'</td>';
            echo '<td>'.$row['flags'].'</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    else
    {
        echo '<p class="error">No Tribe Members Found</p>';
    }
}

?>
