<?
function checkbooks(){
  include('util.php');

	?>
			
<?PHP

    checkAccess('main', '', 'read');

    	$query = "select * from action_locations where name like '%books%' or meshname like '%book%'";
    	$result = mysql_query2($query);
    	
    	while ($line = mysql_fetch_array($result, MYSQL_ASSOC)){
    	  $trigger = $line['response'];
    	  $pos = strpos($trigger, 'Container ID');
    	  if ($pos!=false) {
    	      $temptrig = substr($trigger,$pos+14);
    	      //echo "temptrig : $temptrig<br>";
    	      $containerid = substr($temptrig,0,strpos($temptrig,'\''));
    	      //echo "Container ID: " . $containerid ;

              // seach container
    	      $query = "select s.name as sectorname,i.* from item_instances i left join sectors s on i.loc_sector_id=s.id where i.id=$containerid";
    	      $result2 = mysql_query2($query);
    	      if (mysql_num_rows($result2)==0) {
    	        echo "Action location ". $line['id'] . " " .$line['name'] . " specifies an invalid container id ".$containerid."<br>";
    	        continue;
    	      }
    	      $line2 = mysql_fetch_array($result2, MYSQL_ASSOC);
    	      
    	      if ($line2['char_id_owner']!=null && $line2['char_id_owner']!=0) {
    	        echo "Action location ". $line['id'] . " " .$line['name'] . " specifies a valid container id ".$containerid." but the container is carried by player ".$line2['char_id_owner']."<br>";
    	        continue;
    	      }

    	      if ($line2['parent_item_id']!=null && $line2['parent_item_id']!=0) {
    	        echo "Action location ". $line['id'] . " " .$line['name'] . " specifies a valid container id ".$containerid." but the container is inside another container with id ".$line2['parent_item_id']."<br>";
    	        continue;
    	      }

    	      if ($line2['sectorname']!=$line['sectorname']) {
    	        echo "Action location ". $line['id'] . " " .$line['name'] . " in sector ".$line['sectorname']." specifies a valid container id ".$containerid." but the container is inside another sector : ".$line2['sectorname']."<br>";
    	        continue;
    	      }

    	      //if ($line2['loc_x']!=$line['pos_x'] || $line2['loc_z']!=$line['pos_z']) {
    	      // echo "Action location ". $line['id'] . " " .$line['name'] . " in sector ".$line['sectorname']." (".$line['pos_x'].",".$line['pos_y'].",".$line['pos_z'].") specifies a valid container id ".$containerid." in sector ".$line2['sectorname']." but the container is at wrong position : (".$line2['loc_x'].",".$line2['loc_y'].",".$line2['loc_z'].")";
    	      // continue;
    	      //}

    	      if (strstr($line2['flags'],"NOPICKUP")=='') {
    	        echo "Action location ". $line['id'] . " " .$line['name'] . " in sector ".$line['sectorname']." specifies a valid container id ".$containerid." but the container is missing the NOPICKUP flag<br>";
    	        echo "    Tip: update item_instances set flags='NOPICKUP NPCOWNED' where id=$containerid; <br>";
    	        continue;
    	      }

    	      if (strstr($line2['flags'],"NPCOWNED")=='') {
    	        echo "Action location ". $line['id'] . " " .$line['name'] . " in sector ".$line['sectorname']." specifies a valid container id ".$containerid." but the container is missing the NPCOWNED flag<br>";
    	        echo "    Tip: update item_instances set flags='NOPICKUP NPCOWNED' where id=$containerid; <br>";
    	        continue;
    	      }


              // check contained items
    	      $query = "select * from item_instances where parent_item_id=$containerid order by item_stats_id_standard";
    	      $result3 = mysql_query2($query);
    	      if (mysql_num_rows($result3)==0) {
    	        echo "Action location ". $line['id'] . " " .$line['name'] . " in sector ".$line['sectorname']." specifies a valid container id ".$containerid." but the container seems empty<br>";
    	        continue;
    	      }

      	      while ($line3 = mysql_fetch_array($result3, MYSQL_ASSOC)){
              
        	      if (strstr($line3['flags'],"NOPICKUP")=='') {
        	        echo "Action location ". $line['id'] . " " .$line['name'] . " in sector ".$line['sectorname']." specifies a valid container id ".$containerid." but the item ".$line3['id']." contained is missing the NOPICKUP flag<br>";
        	        continue;
        	      }
        	      echo "     Book ".$line3['item_stats_id_standard']." in container ".$containerid." is ok.<br>";
        	  }
        	  
        	  echo "Action location ". $line['id'] . " " .$line['name'] . " in sector ".$line['sectorname']." specifies a valid container id ".$containerid." and all checks are passed.<br>";
  	      }
    	}
    	
	echo "<br><br><A HREF=\"index.php?page=descworld\" target=_top>Go back to sectors list</A>";

}

?>