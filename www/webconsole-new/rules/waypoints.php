<script type="text/javascript" language="javascript">
function editdelform(i, a)
{
	document.getElementById('edid').value = i;
	document.getElementById('edaction').value = a;
	document.getElementById('editdelform').submit();
}
function showflags()
{
	var wr = document.getElementById('wayrows').value;
	document.getElementById('mainflagedit').style.display = 'none';
	document.getElementById('mainflagsave').style.display = 'inline';
	document.getElementById('editflagdiv').style.display = 'block';
	for(i=0; i<wr; i++)
	{
		document.getElementById(i +'_stndflagdiv').style.display = 'none';
		document.getElementById(i +'_editflagdiv').style.display = 'block';
	}
}
function toggleflag(flag)
{
	var setflag = false;
	if(document.getElementById(flag + "flag").checked)
	{
		setflag = true;	
	}
	var wr = document.getElementById('wayrows').value;
	for(i=0; i<wr; i++)
	{
		document.getElementById(i + "_" +  flag).checked = setflag;
	}
}
</script>

<?php
function listwaypoints(){
  if (checkaccess('natres', 'read')){
    if (isset($_POST['commit']) && checkaccess('natres', 'edit')){
      if ($_POST['commit'] == "Update Waypoint" && isset($_POST['id'])){
        $id = escapeSqlString($_POST['id']);
        $name = escapeSqlString($_POST['name']);
        $group = escapeSqlString($_POST['group']);
        $sector = escapeSqlString($_POST['loc_sector_id']);
        $x = escapeSqlString($_POST['x']);
        $y = escapeSqlString($_POST['y']);
        $z = escapeSqlString($_POST['z']);
        $radius = escapeSqlString($_POST['radius']);
        $flags = '';
        foreach ($_POST['flags'] AS $key => $value){
          $flags = $flags . $value . ', ';
        }
        if (strlen($flags) > 0){
          $flags = substr($flags, 0, -2);
        }
        $flag = escapeSqlString($flags);
        $query = "UPDATE sc_waypoints SET name='$name', wp_group='$group', loc_sector_id='$sector', x='$x', y='$y', z='$z', radius='$radius', flags='$flag' WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
      }else if($_POST['commit'] == "Confirm Delete" && checkaccess('natres', 'delete')){
        $id = escapeSqlString($_POST['id']);
        $query = "DELETE FROM sc_waypoints WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
      }else if($_POST['commit'] == "Create Waypoint" && checkaccess('natres', 'create')){
        $name = escapeSqlString($_POST['name']);
        $group = escapeSqlString($_POST['group']);
        $sector = escapeSqlString($_POST['loc_sector_id']);
        $x = escapeSqlString($_POST['x']);
        $y = escapeSqlString($_POST['y']);
        $z = escapeSqlString($_POST['z']);
        $radius = escapeSqlString($_POST['radius']);
        $flags = '';
        foreach ($_POST['flags'] AS $key => $value){
          $flags = $flags . $value . ', ';
        }
        if (strlen($flags) > 0){
          $flags = substr($flags, 0, -2);
        }
        $flag = escapeSqlString($flags);
        $query = "INSERT INTO sc_waypoints SET name='$name', wp_group='$group', loc_sector_id='$sector', x='$x', y='$y', z='$z', radius='$radius', flags='$flag'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
      }else{
        echo '<p class="error">Invalid Commit found - Returning to listing</p>';
      }
      unset($_POST);
      listwaypoints();
      return;
    }else if (checkaccess('natres', 'edit') && isset($_POST['action'])){		
		
      $id = escapeSqlString($_POST['id']);
      if ($_POST['action'] == 'Edit'){
        $query = "SELECT * FROM sc_waypoints WHERE id='$id'";
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        $navurl = (isset($_GET['sector']) ? '&amp;sector='.$_GET['sector'] : '' ).(isset($_GET['sort']) ? '&amp;sort='.$_GET['sort'] : '' ).(isset($_GET['limit']) ? '&amp;limit='.$_GET['limit'] : '' );
        echo '<form action="./index.php?do=waypoint'.$navurl.'" method="post"><input type="hidden" name="id" value="'.$row['id'].'" />';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Name</td><td><input type="text" name="name" value="'.$row['name'].'" /></td></tr>';
        echo '<tr><td>Group</td><td><input type="text" name="group" value="'.$row['wp_group'].'"/></td></tr>';
        $Sectors = PrepSelect('sectorid');
        echo '<tr><td>Sector</td><td>'.DrawSelectBox('sectorid', $Sectors, 'loc_sector_id', $row['loc_sector_id']).'</td></tr>';
        echo '<tr><td>X</td><td><input type="text" name="x" value="' . $row['x'] . '"/></td></tr>';
        echo '<tr><td>Y</td><td><input type="text" name="y" value="' . $row['y'] . '"/></td></tr>';
        echo '<tr><td>Z</td><td><input type="text" name="z" value="' . $row['z'] . '"/></td></tr>';
        echo '<tr><td>Radius</td><td><input type="text" name="radius" value="'.$row['radius'].'" /></td></tr>';
        echo '<tr><td>Flags</td><td>';
        $flags = ' '.$row['flags'];
        if (strpos($flags, 'ALLOW_RETURN')){
          echo 'ALLOW_RETURN: <input type="checkbox" name="flags[]" value="ALLOW_RETURN" checked="true" /><br/>';
        }else{
          echo 'ALLOW_RETURN: <input type="checkbox" name="flags[]" value="ALLOW_RETURN" /><br/>';
        }
        if (strpos($flags, 'UNDERGROUND')){
          echo 'UNDERGROUND: <input type="checkbox" name="flags[]" value="UNDERGROUND" checked="true" /><br/>';
        }else{
          echo 'UNDERGROUND: <input type="checkbox" name="flags[]" value="UNDERGROUND" /><br/>';
        }
        if (strpos($flags, 'UNDERWATER')){
          echo 'UNDERWATER: <input type="checkbox" name="flags[]" value="UNDERWATER" checked="true" /><br/>';
        }else{
          echo 'UNDERWATER: <input type="checkbox" name="flags[]" value="UNDERWATER" /><br/>';
        }
        if (strpos($flags, 'PRIVATE')){
          echo 'PRIVATE: <input type="checkbox" name="flags[]" value="PRIVATE" checked="true"/><br/>';
        }else{
          echo 'PRIVATE: <input type="checkbox" name="flags[]" value="PRIVATE" /><br/>';
        }
        if (strpos($flags, 'PUBLIC')){
          echo 'PUBLIC: <input type="checkbox" name="flags[]" value="PUBLIC" checked="true" /><br/>';
        }else{
          echo 'PUBLIC: <input type="checkbox" name="flags[]" value="PUBLIC" /><br/>';
        }
        if (strpos($flags, 'CITY')){
          echo 'CITY: <input type="checkbox" name="flags[]" value="CITY" checked="true"/><br/>';
        }else{
          echo 'CITY: <input type="checkbox" name="flags[]" value="CITY" /><br/>';
        }
        if (strpos($flags, 'INDOOR')){
          echo 'INDOOR: <input type="checkbox" name="flags[]" value="INDOOR" checked="true"/><br/>';
        }else{
          echo 'INDOOR: <input type="checkbox" name="flags[]" value="INDOOR" /><br/>';
        }
        if (strpos($flags, 'PATH')){
          echo 'PATH: <input type="checkbox" name="flags[]" value="PATH" checked="true"/><br/>';
        }else{
          echo 'PATH: <input type="checkbox" name="flags[]" value="PATH" /><br/>';
        }
        if (strpos($flags, 'ROAD')){
          echo 'ROAD: <input type="checkbox" name="flags[]" value="ROAD" checked="true"/><br/>';
        }else{
          echo 'ROAD: <input type="checkbox" name="flags[]" value="ROAD" /><br/>';
        }
        if (strpos($flags, 'GROUND')){
          echo 'GROUND: <input type="checkbox" name="flags[]" value="GROUND" checked="true"/><br/>';
        }else{
          echo 'GROUND: <input type="checkbox" name="flags[]" value="GROUND" /><br/>';
        }

        echo '</td></tr>';
        echo '</table>';
        echo '<input type="submit" name="commit" value="Update Waypoint" />';
        echo '</form>';
      }else if (($_POST['action'] == 'Delete') && checkaccess('natres', 'delete')){
        $id = escapeSqlString($_POST['id']);
        $query = "SELECT id FROM sc_waypoint_links WHERE wp1='$id' OR wp2='$id'";
        $result = mysql_query2($query);
        if (sqlNumRows($result) > 0)
        {
            echo '<p class="error">There are waypoint links still using this waypoint, it may not be deleted.</p>';
            echo '<p>Below are links to all such waypoints.<br /><br />';
            while ($row = fetchSqlAssoc($result)) 
            {
                echo '<a href="./index.php?do=editwaypointlink&id='.$row['id'].'">'.$row['id'].'</a><br />';
            }
            echo '</p>';
            return;
        }
        $query = "SELECT name FROM sc_waypoints WHERE id='$id'";
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        echo 'You are about to delete waypoint '.$row['name'].' - Please confirm you wish to do this<br/>';
        echo '<form action="./index.php?do=waypoint" method="post">';
        echo '<input type="hidden" name="id" value="'.$id.'"/><input type="submit" name="commit" value="Confirm Delete" /></form>';
      }else{
        unset($_POST['action']);
        echo '<p class="error">Error: Bad action submitted</p>';
        listwaypoints();
        return;
      }
    }else{
	if(checkaccess('natres', 'edit'))
	{
		if($_POST['flagedit'])
		{
			for($tmp = 0; $tmp < $_POST['wayrows']; $tmp++)
			{
				$tmp_flag = '';
				if(isset($_POST[$tmp.'_ar']))
				{
					$tmp_flag .= 'ALLOW_RETURN, ';
				}
				if(isset($_POST[$tmp.'_ug']))
				{
					$tmp_flag .= 'UNDERGROUND, ';
				}
				if(isset($_POST[$tmp.'_uw']))
				{
					$tmp_flag .= 'UNDERWATER, ';
				}
				if(isset($_POST[$tmp.'_pr']))
				{
					$tmp_flag .= 'PRIVATE, ';
				}
				if(isset($_POST[$tmp.'_pu']))
				{
					$tmp_flag .= 'PUBLIC, ';
				}
				if(isset($_POST[$tmp.'_ct']))
				{
					$tmp_flag .= 'CITY, ';
				}
				if(isset($_POST[$tmp.'_in']))
				{
					$tmp_flag .= 'INDOOR, ';
				}
				if(isset($_POST[$tmp.'_pa']))
				{
					$tmp_flag .= 'PATH, ';
				}
				if(isset($_POST[$tmp.'_rd']))
				{
					$tmp_flag .= 'ROAD, ';
				}
				if(isset($_POST[$tmp.'_gr']))
				{
					$tmp_flag .= 'GROUND, ';
				}
				$upd_flag = 'update sc_waypoints set flags="' . substr($tmp_flag, 0, -2) . '" where id=' . $_POST[$tmp.'_id'];
				mysql_query2($upd_flag);
			}
		}
	}
		
      $query = "SELECT w.id, w.name, w.wp_group, w.x, w.y, w.z, w.radius, w.flags, w.loc_sector_id, s.name AS sector FROM sc_waypoints AS w LEFT JOIN sectors AS s on s.id=w.loc_sector_id";
      if (isset($_GET['id']) && $_GET['id']!=''){
        $id = escapeSqlString($_GET['id']);
        $query .= " WHERE w.id='$id'";
      }
      elseif (isset($_GET['sector']) && $_GET['sector'] != '' && $_GET['sector'] != 0){
        $sec = escapeSqlString($_GET['sector']);
        $query .= " WHERE w.loc_sector_id='$sec'";
      }
      if (isset($_GET['sort'])){
        switch($_GET['sort']){
          case 'name':
            $query .= ' ORDER BY w.name';
            break;
          case 'group':
            $query .= ' ORDER BY w.wp_group';
            break;
          case 'sector':
            $query .= ' ORDER BY sector, name';
            break;
          default:
            $query .= ' ORDER BY sector, name';
        }
      }else{
        $query .= ' ORDER BY sector, name';
      }
      if (isset($_GET['limit']) && is_numeric($_GET['limit'])){
        $prev_lim = $_GET['limit'] - 30;
        $lim = $_GET['limit'];
        $query = $query . " LIMIT $prev_lim, 30"; // limit 1, 10 is offset 1, taking 10 records.
      }else{
        $query = $query . " LIMIT 30";
        $lim = 30;
        $prev_lim = 0;
      }
      $result = mysql_query2($query);
      if (sqlNumRows($result) == 0){
        echo '<p class="error">No Waypoints</p>';
      }//else{
        $sid = 0;
        if (isset($_GET['sector']))
        {
            $sid = $_GET['sector'];
        }
        if ($lim > 30){
          echo '<a href="./index.php?do=waypoint';
          if (isset($_GET['sort'])){
            echo '&amp;sort='.$_GET['sort'];
          }
          echo '&amp;limit='.$prev_lim.'&amp;sector='.$sid.'">Previous Page</a> ';
        }
        echo ' - Displaying records '.$prev_lim.' through '.$lim.' - ';
        $where = ($sid == 0 ? '' : " WHERE w.loc_sector_id=$sid");
        $result2 = mysql_query2('select count(w.id) AS mylimit FROM sc_waypoints AS w'.$where);
        $row2 = fetchSqlAssoc($result2);
        if ($row2['mylimit'] > $lim)
        {
          echo '<a href="./index.php?do=waypoint';
          if (isset($_GET['sort'])){
            echo '&amp;sort='.$_GET['sort'];
          }
          echo '&amp;limit='.($lim+30).'&amp;sector='.$sid.'">Next Page</a>';
        }
        $Sectors = PrepSelect('sectorid');
        if (!isset($_GET['sector'])){
          $_GET['sector']="NULL";
        }
        echo ' - <form action="./index.php" method="get"><input type="hidden" name="do" value="waypoint"/>';
        if (isset($_GET['sort'])){
          echo '<input type="hidden" name="sort" value="'.$_GET['sort'].'"/>';
        }
       /* if (isset($_GET['limit'])){
          echo '<input type="hidden" name="limit" value="'.$_GET['limit'].'"/>';
        }*/
        echo DrawSelectBox('sectorid', $Sectors, 'sector' ,$_GET['sector'], true).'<input type="submit" name="submit" value="Limit By Sector" /></form>';
        if ($_GET['sector'] == "NULL"){
          unset($_GET['sector']);
        }
		// Form for edit/delete buttons
		 $navurl = (isset($_GET['sector']) ? '&amp;sector='.$_GET['sector'] : '' ).(isset($_GET['sort']) ? '&amp;sort='.$_GET['sort'] : '' ).(isset($_GET['limit']) ? '&amp;limit='.$_GET['limit'] : '' );
            echo '<form id="editdelform" action="./index.php?do=waypoint'.$navurl.'" method="post">';
            echo '<input type="hidden" name="id" id="edid" value="0" />';
            echo '<input type="hidden" id="edaction" name="action" value="Edit" />';
            echo '</form>';
		echo '<form id="editflagform" action="./index.php?do=waypoint'.$navurl.'" method="post">
		<input type="hidden" name="flagedit" value="true">';		
        echo '<table border="1">';
        echo '<tr><th><a href="./index.php?do=waypoint&amp;sort=name';
        if (isset($_GET['limit'])){
          echo '&amp;limit='.$_GET['limit'];
        }
        if (isset($_GET['sector'])){
          echo '&amp;sector='.$_GET['sector'];
        }
        echo '">Name</a></th>';
        echo '<th><a href="./index.php?do=waypoint&amp;sort=group';
        if (isset($_GET['limit'])){
          echo '&amp;limit='.$_GET['limit'];
        }
        if (isset($_GET['sector'])){
          echo '&amp;sector='.$_GET['sector'];
        }
        echo '">Group</a></th>';
        echo '<th><a href="./index.php?do=waypoint&amp;sort=sector';
        if (isset($_GET['limit'])){
          echo '&amp;limit='.$_GET['limit'];
        }
        if (isset($_GET['sector'])){
          echo '&amp;sector='.$_GET['sector'];
        }
        echo '">Sector</a></th><th>X</th><th>Y</th><th>Z</th><th>Radius</th><th>Flags - <input type="button" id="mainflagedit" value="Edit Flags" onclick="showflags();"/><input type="submit" id="mainflagsave" value="Save Flags" style="display:none" /><div id="editflagdiv" style="display:none"><table>
		<tr>
		<td>AR<br /><input type="checkbox" id="arflag" onchange="toggleflag(\'ar\')" title="ALLOW_RETURN"></td>
		<td>UG<br /><input type="checkbox" id="ugflag" onchange="toggleflag(\'ug\')" title="UNDERGROUND"></td>
		<td>UW<br /><input type="checkbox" id="uwflag" onchange="toggleflag(\'uw\')" title="UNDERWATER"></td>
		<td>PR<br /><input type="checkbox" id="prflag" onchange="toggleflag(\'pr\')" title="PRIVATE"></td>
		<td>PU<br /><input type="checkbox" id="puflag" onchange="toggleflag(\'pu\')" title="PUBLIC"></td>
		<td>CT<br /><input type="checkbox" id="ctflag" onchange="toggleflag(\'ct\')" title="CITY"></td>
		<td>IN<br /><input type="checkbox" id="inflag" onchange="toggleflag(\'in\')" title="INSIDE"></td>
		<td>PA<br /><input type="checkbox" id="paflag" onchange="toggleflag(\'pa\')" title="PATH"></td>
		<td>RD<br /><input type="checkbox" id="rdflag" onchange="toggleflag(\'rd\')" title="ROAD"></td>
		<td>GR<br /><input type="checkbox" id="grflag" onchange="toggleflag(\'gr\')" title="GROUND"></td>
		</tr></table></div></th>';
        if (checkaccess('natres','edit')){
          echo '<th>Actions</th>';
        }
        echo '</tr>';
		$r = 0;
		
        while ($row = fetchSqlAssoc($result))
		{
		
          echo '<tr>';
          echo '<td>'.$row['name'].'</td>';
          echo '<td>'.$row['wp_group'].'</td>';
          echo '<td>'.$row['sector'].'</td>';
          echo '<td>'.$row['x'].'</td>';
          echo '<td>'.$row['y'].'</td>';
          echo '<td>'.$row['z'].'</td>';
          echo '<td>'.$row['radius'].'</td>';
          echo '<td><div id="' . $r . '_stndflagdiv" style="display:block">'.$row['flags'].'</div>';
		  
		  if (checkaccess('natres', 'edit'))
		  {
				$flags = ' '.$row['flags'];
			echo('<div id="' . $r . '_editflagdiv" style="display:none"><input type="hidden" name="' . $r . '_id" id="' . $r . '_id" value="'.$row['id'].'"/><table width="100%"><tr><td>');	
			if (strpos($flags, 'ALLOW_RETURN')){
			  echo '<input type="checkbox" name="' . $r . '_ar" id="' . $r . '_ar" value="ALLOW_RETURN" checked="true" title="ALLOW_RETURN" />';
			}else{
			  echo '<input type="checkbox" name="' . $r . '_ar" id="' . $r . '_ar" value="ALLOW_RETURN" title="ALLOW_RETURN" />';
			}
			echo '</td><td>';
			if (strpos($flags, 'UNDERGROUND')){
			  echo '<input type="checkbox" name="' . $r . '_ug" id="' . $r . '_ug" value="UNDERGROUND" title="UNDERGROUND" checked="true" />';
			}else{
			  echo '<input type="checkbox" name="' . $r . '_ug" id="' . $r . '_ug" value="UNDERGROUND" title="UNDERGROUND" />';
			}
			echo '</td><td>';
			if (strpos($flags, 'UNDERWATER')){
			  echo '<input type="checkbox" name="' . $r . '_uw" id="' . $r . '_uw" value="UNDERWATER" title="UNDERWATER" checked="true" />';
			}else{
			  echo '<input type="checkbox" name="' . $r . '_uw" id="' . $r . '_uw" value="UNDERWATER" title="UNDERWATER" />';
			}
			echo '</td><td>';
			if (strpos($flags, 'PRIVATE')){
			  echo '<input type="checkbox" name="' . $r . '_pr" id="' . $r . '_pr" value="PRIVATE" title="PRIVATE" checked="true"/>';
			}else{
			  echo '<input type="checkbox" name="' . $r . '_pr" id="' . $r . '_pr" value="PRIVATE" title="PRIVATE" />';
			}
			echo '</td><td>';
			if (strpos($flags, 'PUBLIC')){
			  echo '<input type="checkbox" name="' . $r . '_pu" id="' . $r . '_pu" value="PUBLIC" title="PUBLIC" checked="true" />';
			}else{
			  echo '<input type="checkbox" name="' . $r . '_pu" id="' . $r . '_pu" value="PUBLIC" title="PUBLIC" />';
			}
			echo '</td><td>';
			if (strpos($flags, 'CITY')){
			  echo '<input type="checkbox" name="' . $r . '_ct" id="' . $r . '_ct" value="CITY" title="CITY" checked="true"/>';
			}else{
			  echo '<input type="checkbox" name="' . $r . '_ct" id="' . $r . '_ct" value="CITY" title="CITY" />';
			}
			echo '</td><td>';
			if (strpos($flags, 'INDOOR')){
			  echo '<input type="checkbox" name="' . $r . '_in" id="' . $r . '_in" value="INDOOR" title="INDOOR" checked="true"/>';
			}else{
			  echo '<input type="checkbox" name="' . $r . '_in" id="' . $r . '_in" value="INDOOR" title="INDOOR" />';
			}
			echo '</td><td>';
			if (strpos($flags, 'PATH')){
			  echo '<input type="checkbox" name="' . $r . '_pa" id="' . $r . '_pa" value="PATH" title="PATH" checked="true"/>';
			}else{
			  echo '<input type="checkbox" name="' . $r . '_pa" id="' . $r . '_pa" value="PATH" title="PATH" />';
			}
			echo '</td><td>';
			if (strpos($flags, 'ROAD')){
			  echo '<input type="checkbox" name="' . $r . '_rd" id="' . $r . '_rd" value="ROAD" title="ROAD" checked="true"/>';
			}else{
			  echo '<input type="checkbox" name="' . $r . '_rd" id="' . $r . '_rd" value="ROAD" title="ROAD" />';
			}
			echo '</td><td>';
			if (strpos($flags, 'GROUND')){
			  echo '<input type="checkbox" name="' . $r . '_gr" id="' . $r . '_gr" value="GROUND" title="GROUND" checked="true"/>';
			}else{
			  echo '<input type="checkbox" name="' . $r . '_gr" id="' . $r . '_gr" value="GROUND" title="GROUND" />';
			}
			echo('</td></tr></table></div>');
		  }
		  
		  echo '</td>';
          if (checkaccess('natres', 'edit'))
		  {
            echo '<td><input type="button" name="action" value="Edit" onClick="editdelform(\''.$row['id'].'\', \'Edit\');"/>';
            if (checkaccess('natres', 'delete'))
			{
              echo '<br/><input type="button" name="action" value="Delete" onClick="editdelform(\''.$row['id'].'\', \'Delete\');" />';
            }
            echo '</td>';
          }
		  $r++;
          echo '</tr>';
        }
		
        echo '</table><input type="hidden" name="wayrows" id="wayrows" value="' . $r . '"/></form>';
        if (checkaccess('natres', 'create')){
        echo '<hr/><p>Create New Waypoint:</p><form action="./index.php?do=waypoint" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Name</td><td><input type="text" name="name" /></td></tr>';
        echo '<tr><td>Group</td><td><input type="text" name="group"/></td></tr>';
        $Sectors = PrepSelect('sectorid');
        echo '<tr><td>Sector</td><td>'.DrawSelectBox('sectorid', $Sectors, 'loc_sector_id', '' ).'</td></tr>';
        echo '<tr><td>X</td><td><input type="text" name="x"/></td></tr>';
        echo '<tr><td>Y</td><td><input type="text" name="y"/></td></tr>';
        echo '<tr><td>Z</td><td><input type="text" name="z"/></td></tr>';
        echo '<tr><td>Radius</td><td><input type="text" name="radius" /></td></tr>';
        echo '<tr><td>Flags</td><td>';
        $flags = ' '.$row['flags'];
          echo 'ALLOW_RETURN: <input type="checkbox" name="flags[]" value="ALLOW_RETURN" /><br/>';
          echo 'UNDERGROUND: <input type="checkbox" name="flags[]" value="UNDERGROUND" /><br/>';
          echo 'UNDERWATER: <input type="checkbox" name="flags[]" value="UNDERWATER" /><br/>';
          echo 'PRIVATE: <input type="checkbox" name="flags[]" value="PRIVATE" /><br/>';
          echo 'PUBLIC: <input type="checkbox" name="flags[]" value="PUBLIC" /><br/>';
          echo 'CITY: <input type="checkbox" name="flags[]" value="CITY" /><br/>';
          echo 'INDOOR: <input type="checkbox" name="flags[]" value="INDOOR" /><br/>';
          echo 'PATH: <input type="checkbox" name="flags[]" value="PATH" /><br/>';
          echo 'ROAD: <input type="checkbox" name="flags[]" value="ROAD" /><br/>';
          echo 'GROUND: <input type="checkbox" name="flags[]" value="GROUND" /><br/>';
        echo '</td></tr>';
        echo '</table>';
        echo '<input type="submit" name="commit" value="Create Waypoint" />';
        echo '</form>';

        }
      }
   // }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
