<?PHP

// defines the limit between normal and generated items in item_stats table for id column.
function getBaseItemMax () {
  return 10000;
}

function WayToID($way){
	$query = "select id from ways where name=\"" . $way . "\"";

	$result = mysql_query2($query);
	$wayID = mysql_fetch_array($result, MYSQL_NUM);

	return $wayID[0];
}

function SelectProgressionEvent($current_event, $select_name){
	printf("<SELECT name=%s>", $select_name);
	$query_events = "select name from progression_events";
	$result = mysql_query2($query_events);
	while ($event = mysql_fetch_array($result, MYSQL_NUM)){
		if ($event[0] == $current_event){
			printf("<OPTION selected>%s</OPTION>", $event[0]);
		}else{
			printf("<OPTION>%s</OPTION>", $event[0]);
		}
	}
	
	printf("<OPTION ");
	if($current_event == "")
	    printf("selected ");
	
	printf(" value=\"\">NULL</OPTION>");
	

	printf("</SELECT>");
}


function SelectCastProgressionEvent($current_event, $select_name){

	printf("<SELECT name=%s>", $select_name);
	$query_events = "select name from progression_events where name like 'cast %' order by name";
	$result = mysql_query2($query_events);
	$found = false;
	while ($event = mysql_fetch_array($result, MYSQL_NUM)){
		if ($event[0] == $current_event){
			printf("<OPTION value=\"%s\" selected>%s</OPTION>", $event[0], $event[0]);
			$found = true;
		}else{
			printf("<OPTION value=\"%s\" >%s</OPTION>", $event[0], $event[0]);
		}
	}
	
	printf("<OPTION ");
	if($current_event == "" || !$found)
	    printf("selected ");
	
	printf(" value=\"\">NULL</OPTION>");	

	printf("</SELECT>");
	if (!$found)
	  printf("<font color=red>Problem with selected value not present in DB! %s</font>",$current_event);
}

function SelectCommonString($current_string, $select_name){
	printf("<SELECT name=%s>", $select_name);
	$query_events = "select id, string from common_strings";
	$result = mysql_query2($query_events);
	while ($list = mysql_fetch_array($result, MYSQL_NUM)){
		if ($list[0] == $current_string){
			printf("<OPTION selected value=\"%s\">%s - %s</OPTION>",$list[0], $list[0], $list[1]);
		}else{
			printf("<OPTION value=\"%s\">%s - %s</OPTION>", $list[0], $list[0], $list[1]);
		}
	}
	printf("</SELECT>");
}

function SelectItemCateogory($current_category, $select_name){
	printf("<SELECT name=%s>", $select_name);
	$query_events = "select category_id, name from item_categories";
	$result = mysql_query2($query_events);
	while ($list = mysql_fetch_array($result, MYSQL_NUM)){
		if ($list[0] == $current_category){
			printf("<OPTION selected value=\"%s\">%s</OPTION>", $list[0], $list[1]);
		}else{
			printf("<OPTION value=\"%s\">%s</OPTION>", $list[0], $list[1]);
		}
	}
	printf("</SELECT>");
}

function SelectBaseItem($current_itemID,$select_name)
{
    $base_item_max = getBaseItemMax();
	printf("<SELECT name=%s>", $select_name);
	$query = "select id, item_type, name, flags from item_stats where id<$base_item_max order by item_type, name";
	$result = mysql_query2($query);
	while ($list = mysql_fetch_array($result, MYSQL_NUM)){
		if ($list[0] == $current_itemID){
			printf("<OPTION selected value=\"%s\">%s : %s</OPTION>", $list[0], $list[1], $list[2]);
		}else{
			printf("<OPTION value=\"%s\">%s : %s</OPTION>", $list[0], $list[1], $list[2]);
		}
	}
	
	printf("<OPTION ");
	if($current_itemID == "-1")
		printf("selected ");
	printf("value=\"%s\">%s</OPTION>", 0, "None");
	
	printf("</SELECT>");
}


function SelectGlyphs($current_glyphID,$select_name)
{
	printf("<SELECT name=%s>", $select_name);
	$query_events = "SELECT id,name FROM item_stats WHERE flags LIKE '%GLYPH%' order by name";
	$result = mysql_query2($query_events);
	while ($list = mysql_fetch_array($result, MYSQL_NUM)){
		if ($list[0] == $current_glyphID){
			printf("<OPTION selected value=\"%s\">%s</OPTION>", $list[0], $list[1]);
		}else{
			printf("<OPTION value=\"%s\">%s</OPTION>", $list[0], $list[1]);
		}
	}
	
	printf("<OPTION ");
	if($current_glyphID == "-1")
		printf("selected ");
	printf("value=\"%s\">%s</OPTION>", 0, "None");
	
	printf("</SELECT>");
}

function SelectNPCs($current_npc, $select_name, $invulnerable) {
	printf("<SELECT name=%s>", $select_name);

    if ($invulnerable=="vulnerable")
	    $query_events = "select c.id, c.name,s.name from characters c, sectors s where c.loc_sector_id=s.id and npc_master_id!=0 and npc_impervious_ind='N' order by s.name, c.name";
    else if ($invulnerable=="invulnerable")
	    $query_events = "select c.id, c.name,s.name from characters c, sectors s where c.loc_sector_id=s.id and npc_master_id!=0 and npc_impervious_ind='Y' order by s.name, c.name";
    else if ($invulnerable=="both")
	    $query_events = "select c.id, c.name,s.name from characters c, sectors s where c.loc_sector_id=s.id and npc_master_id!=0 order by s.name, c.name";

	$result = mysql_query2($query_events);
	while ($list = mysql_fetch_array($result, MYSQL_NUM)){
		if ($list[0] == $current_npc){
			printf("<OPTION selected value=\"%s\">%s (%s - %s)</OPTION>", $list[0], $list[1], $list[0], $list[2]);
		}else{
			printf("<OPTION value=\"%s\">%s (%s - %s)</OPTION>", $list[0], $list[1], $list[0], $list[2]);
		}
	}
	printf("</SELECT>");
}

function SelectSkills($current_skill,$select_name){
	printf("<SELECT name=%s>", $select_name);
	$query_events = "select skill_id, name from skills";
	$result = mysql_query2($query_events);
	while ($list = mysql_fetch_array($result, MYSQL_NUM)){
		if ($list[0] == $current_skill){
			printf("<OPTION selected value=\"%s\">%s</OPTION>", $list[0], $list[1]);
		}else{
			printf("<OPTION value=\"%s\">%s</OPTION>", $list[0], $list[1]);
		}
	}
	printf("</SELECT>");
}


function SelectSectors($current_sector,$select_name){
	printf("<SELECT name=%s>", $select_name);
	$query_events = "select id, name from sectors";
	$result = mysql_query2($query_events);
	while ($list = mysql_fetch_array($result, MYSQL_NUM)){
		if ($list[0] == $current_sector){
			printf("<OPTION selected value=\"%s\">%s</OPTION>", $list[0], $list[1]);
		}else{
			printf("<OPTION value=\"%s\">%s</OPTION>", $list[0], $list[1]);
		}
	}
	printf("</SELECT>");
}

function SelectRace($current_race,$select_name){
	printf("<SELECT name=%s>", $select_name);
	$query_events = "select race_id, name, sex from race_info order by name, sex";
	$result = mysql_query2($query_events);
	while ($list = mysql_fetch_array($result, MYSQL_NUM)){
		if ($list[0] == $current_race){
			printf("<OPTION selected value=\"%s\">%s - %s</OPTION>", $list[0], $list[1], $list[2] );
		}else{
			printf("<OPTION value=\"%s\">%s - %s</OPTION>", $list[0], $list[1], $list[2]);
		}
	}
	printf("</SELECT>");
}

function SelectSpawnRule($current_spawnrule,$select_name){
	printf("<SELECT name=%s>", $select_name);
	printf("<OPTION value=0>Not Loaded in Game</OPTION>");
	$query_events = "select id, fixed_spawn_sector, fixed_spawn_x, fixed_spawn_y, fixed_spawn_z, loot_category_id from npc_spawn_rules order by fixed_spawn_sector, fixed_spawn_x";
	$result = mysql_query2($query_events);
	while ($list = mysql_fetch_array($result, MYSQL_NUM)){
		if ($list[0] == $current_spawnrule){
			printf("<OPTION selected value=\"%s\">%s (%s,%s,%s) loot:%s</OPTION>", $list[0], $list[1], $list[2], $list[3], $list[4], $list[5] );
		}else{
			printf("<OPTION value=\"%s\">%s (%s,%s,%s) loot:%s</OPTION>", $list[0], $list[1], $list[2], $list[3], $list[4], $list[5] );
		}
	}
	printf("</SELECT>");
}

function SelectWeapon($current_weapon,$select_name){
    $base_item_max = getBaseItemMax();
	printf("<SELECT name=%s>", $select_name);
	printf("<OPTION value=-1>None</OPTION>");
	$query_events = "select id, armorvsweapon_type, name from item_stats where category_id=1 and id<$base_item_max order by armorvsweapon_type, name";
	$result = mysql_query2($query_events);
	while ($list = mysql_fetch_array($result, MYSQL_NUM)){
		if ($list[0] == $current_weapon){
			printf("<OPTION selected value=\"%s\">[%s] %s </OPTION>", $list[0], $list[1], $list[2]);
		}else{
			printf("<OPTION value=\"%s\">[%s] %s </OPTION>", $list[0], $list[1], $list[2]);
		}
	}
	printf("</SELECT>");
}

function SelectActionLocation($current_action, $select_name){
	printf("<SELECT name=%s>", $select_name);

    if ($select_name=="triggertype") {
      $elem[0]="SELECT";
      $elem[1]="PROXIMITY";
    } else if ($select_name=="responsetype") {
      $elem[0]="EXAMINE";
      $elem[1]="SCRIPT";
    }
    
	for ($i=0; $i<sizeof($elem); $i++) {
		if ($elem[$i] == $current_action){
			printf("<OPTION selected>%s</OPTION>", $elem[$i]);
		}else{
			printf("<OPTION>%s</OPTION>", $elem[$i]);
		}
	}

	printf("</SELECT>");
}

function SelectQuestScriptByName($current_name, $select_name){
	printf("<SELECT name=%s>", $select_name);

    printf("<OPTION value=-1>None</OPTION>");

	$query_events = "select name from quests order by name";
	$result = mysql_query2($query_events);
	$found = false;
	while ($list = mysql_fetch_array($result, MYSQL_NUM)){
		if ($list[0] == $current_name){
			printf("<OPTION selected value=\"%s\">%s</OPTION>", $list[0], $list[0]);
			$found = true;
		}else{
			printf("<OPTION value=\"%s\">%s</OPTION>", $list[0], $list[0]);
		}
	}
	printf("</SELECT>");
	return $found;
}

function SelectAreas($current_sector,$select_name){

    $areas = array("hydlaa_plaza","hydlaa_jayose","hydlaa_winch","sewers","laanxdungeon","arena","ojaroad1","ojaroad2","akkaio","bdroad1","bdroad2","bdoorsout","bdoorsin","npcroom1","npcroom2");

	printf("<SELECT name=%s>", $select_name);

	for ($i=0; $i<sizeof($areas);$i++) {
		if ($areas[$i] == $current_sector){
			printf("<OPTION selected value=\"%s\">%s</OPTION>", $areas[$i], $areas[$i]);
		} else {
			printf("<OPTION value=\"%s\">%s</OPTION>", $areas[$i], $areas[$i]);
		}
	}
	printf("</SELECT>");
}

function SelectLocation($current_location,$select_name){
	printf("<SELECT name=%s>", $select_name);
	if ("-1" == $current_location){
		printf("<OPTION selected value=\"-1\"></OPTION>");
	}else{
		printf("<OPTION value=\"-1\"></OPTION>");
	}
	$query_events = "select id, name from sc_locations";
	$result = mysql_query2($query_events);
	while ($list = mysql_fetch_array($result, MYSQL_NUM)){
		if ($list[0] == $current_location){
			printf("<OPTION selected value=\"%s\">%s(%s)</OPTION>", $list[0], $list[1], $list[0]);
		}else{
			printf("<OPTION value=\"%s\">%s(%s)</OPTION>", $list[0], $list[1], $list[0]);
		}
	}
	printf("</SELECT>");
}

function SelectLocationType($current_locationtype,$select_name){
	printf("<SELECT name=%s>", $select_name);
	$query_events = "select id, name from sc_location_type";
	$result = mysql_query2($query_events);
	while ($list = mysql_fetch_array($result, MYSQL_NUM)){
		if ($list[0] == $current_locationtype){
			printf("<OPTION selected value=\"%s\">%s(%s)</OPTION>", $list[0], $list[1], $list[0]);
		}else{
			printf("<OPTION value=\"%s\">%s(%s)</OPTION>", $list[0], $list[1], $list[0]);
		}
	}
	printf("</SELECT>");
}

function SelectRegion($current_region,$select_name){
	printf("<SELECT name=%s>", $select_name);
	$query_events = "select distinct type_id, t.name from sc_locations, sc_location_type t where id_prev_loc_in_region != -1 and t.id=type_id";
	$result = mysql_query2($query_events);
	printf("<OPTION selected value=\"-1\">None(-1)</OPTION>");
	while ($list = mysql_fetch_array($result, MYSQL_NUM)){
		if ($list[0] == $current_region || $list[1] == $current_region){
			printf("<OPTION selected value=\"%s\">%s(%s)</OPTION>", $list[0], $list[1], $list[0]);
		}else{
			printf("<OPTION value=\"%s\">%s(%s)</OPTION>", $list[0], $list[1], $list[0]);
		}
	}
	printf("</SELECT>");
}

function SelectBehavior($current_behavior,$select_name){
	printf("<SELECT name=%s>", $select_name);
	$query_events = "select distinct npctype from sc_npc_definitions";
	$result = mysql_query2($query_events);
	if ('New Behavior' == $current_behavior){
	  printf("<OPTION selected value=\"New Behavior\">New Behavior</OPTION>");
  	}else{
	  printf("<OPTION value=\"New Behavior\">New Behavior</OPTION>");
	}
	if ('None' == $current_behavior){
	  printf("<OPTION selected value=\"None\">None</OPTION>");
	}else{
	  printf("<OPTION value=\"None\">None</OPTION>");	    
	}
	while ($list = mysql_fetch_array($result, MYSQL_NUM)){
		if ($list[0] == $current_behavior){
			printf("<OPTION selected value=\"%s\">%s</OPTION>", $list[0], $list[0]);
		}else{
			printf("<OPTION value=\"%s\">%s</OPTION>", $list[0], $list[0]);
		}
	}
	printf("</SELECT>");
}

function GetRegionName($region_id)
{
     $query = "select name from sc_location_type where id='$region_id'";
     $result = mysql_query2($query);
     $list = mysql_fetch_array($result, MYSQL_NUM);
     return $list[0];
}

function getDataFromArea($area) {

  // sectors
  if ($area=="hydlaa_plaza") {
      $data[0] = "(loc_sector_id=15 or loc_sector_id=52)";
      $data[1] = 535;
      $data[2] = 180;
      $data[3] = 2.2;
  } else if ($area=="hydlaa_jayose") {
      $data[0] = "(loc_sector_id=49 or loc_sector_id=40)";
      $data[1] = -250;
      $data[2] = -150;
      $data[3] = 3.2;
  } else if ($area=="hydlaa_winch") {
      $data[0] = "(loc_sector_id=72)";
      $data[1] = 200;
      $data[2] = -430;
      $data[3] = 2.2;
  } else if ($area=="sewers") {
      $data[0] = "(loc_sector_id>26 and loc_sector_id<38)";
      $data[1] = 645;
      $data[2] = 175;
      $data[3] = 2.5;
  } else if ($area=="laanxdungeon") {
      $data[0] = "(loc_sector_id>43 and loc_sector_id<49)";
      $data[1] = 1452;
      $data[2] = 815;
      $data[3] = 6.73;
  } else if ($area=="arena") {
      $data[0] = "(loc_sector_id>3 and loc_sector_id<15)";
      $data[1] = 470;
      $data[2] = 456;
      $data[3] = 4.43;
  } else if ($area=="ojaroad1") {
      $data[0] = "(loc_sector_id=22)";
      $data[1] = 603;
      $data[2] = 600;
      $data[3] = 0.9;
  } else if ($area=="ojaroad2") {
      $data[0] = "(loc_sector_id=59)";
      $data[1] = 632;
      $data[2] = 625;
      $data[3] = 1;
  } else if ($area=="akkaio") {
      $data[0] = "(loc_sector_id>16 and loc_sector_id<20)";
      $data[1] = 445;
      $data[2] = 465;
      $data[3] = 5.3;
  } else if ($area=="bdroad1") {
      $data[0] = "(loc_sector_id=60)";
      $data[1] = 491;
      $data[2] = 493;
      $data[3] = 0.76;
  } else if ($area=="bdroad2") {
      $data[0] = "(loc_sector_id=61)";
      $data[1] = 669;
      $data[2] = 667;
      $data[3] = 1.07;
  } else if ($area=="bdoorsout") {
      $data[0] = "(loc_sector_id=67)";
      $data[1] = 832;
      $data[2] = -749;
      $data[3] = 0.55;
  } else if ($area=="bdoorsin") {
      $data[0] = "(loc_sector_id=66)";
      $data[1] = 656;
      $data[2] = -977;
      $data[3] = 2.2;
  } else if ($area=="npcroom1") {
      $data[0] = "(loc_sector_id=3)";
      $data[1] = 387;
      $data[2] = -453;
      $data[3] = 3.8;
  } else if ($area=="npcroom2") {
      $data[0] = "(loc_sector_id=6 or loc_sector_id=7)";
      $data[1] = 460;
      $data[2] = 318;
      $data[3] = 4;
  }

  return $data;
}


function myquery ($query){
	$result = mysql_query2($query);
	if (mysql_errno())
		echo "<P>MySQL error " . mysql_errno() . ": " . mysql_error() . "\n<br>When executing:<br>\n$query\n<br></P>";
	return $result;
}


function draw_natural_resources($im,$sectors,$centerx,$centery,$scalefactor,$fg_color,$bg_color){
    $query = " select id,loc_x,loc_y,loc_z,radius,visible_radius from natural_resources where " . $sectors;
    $res = mysql_query2($query);

    // exit if there is no data
    $num = mysql_num_rows($res);
    if ($num==0)
      return;

    $i=0;
    while ($line = mysql_fetch_array($res, MYSQL_NUM)){
        $id          = $line[0];
        $x           = $line[1];
        $y           = $line[2];
        $z           = $line[3];
        $radius      = $line[4];
        $vis_radius  = $line[5];

	$ix = $centerx+($x*$scalefactor);
	$iy = $centery-($z*$scalefactor);
        $ir = $radius*$scalefactor;
        imagearc($im,$ix,$iy,$ir,$ir,0,360,$fg_color);

        $ivr = $vis_radius*$scalefactor;
        imagearc($im,$ix,$iy,$ivr,$ivr,0,360,$bg_color);

    }
}

function draw_waypoints($im,$sectors,$centerx,$centery,$scalefactor,$fg_color,$fg_color_no_wander){
    $query = $query . " select id,x,y,z,radius from sc_waypoints where " . $sectors;
    //echo $query;
    $res = mysql_query2($query);

    // exit if there is no data
    $num = mysql_num_rows($res);
    if ($num==0)
      return;

    $i=0;
    while ($line = mysql_fetch_array($res, MYSQL_NUM)){
        $id      = $line[0];
        $x       = $line[1];
        $y       = $line[2];
        $z       = $line[3];
        $radius  = $line[4];

	$ix = $centerx+($x*$scalefactor);
	$iy = $centery-($z*$scalefactor);
        $ir = $radius*$scalefactor;
        imagearc($im,$ix,$iy,$ir,$ir,0,360,$fg_color);

        $query2 = "select wp1.x,wp1.y,wp1.z,wp2.x,wp2.y,wp2.z,l.flags,wp1.id,wp2.id from sc_waypoint_links l, sc_waypoints wp1, sc_waypoints wp2 where l.wp1 = wp1.id and l.wp2 = wp2.id and wp1.id = ".$id;
        $res2=mysql_query2($query2); 
        while ($line2 = mysql_fetch_array($res2, MYSQL_NUM)){
            $x1 = $line2[0];
            $y1 = $line2[1];
            $z1 = $line2[2];
            $x2 = $line2[3];
            $y2 = $line2[4];
            $z2 = $line2[5];
            $flags = $line2[6];
            $id1 = $line2[7];
            $id2 = $line2[8];

            $ix1 = $centerx+($x1*$scalefactor);
            $iy1 = $centery-($z1*$scalefactor);
            $ix2 = $centerx+($x2*$scalefactor);
            $iy2 = $centery-($z2*$scalefactor);

            if (stristr($flags, 'NO_WANDER'))
            {
                $line_color = $fg_color_no_wander;
            } else
            { 
                $line_color = $fg_color;
            }
            if ($id1 == $id2)
            {
                imagearc($im,$ix1,$iy1+8,16,16,0,360,$fg_color);
                // Move arrow if oneway
                $iy1 += 16; 
                $iy2 += 16; 
            } else
            {
                imageline($im,$ix1,$iy1,$ix2,$iy2 , $line_color);
            }
            if (stristr($flags, 'ONEWAY') != FALSE)
            {
                $cx = ($ix1+$ix2)/2;
                $cy = ($iy1+$iy2)/2;
                $a = atan2($iy1-$iy2,$ix1-$ix2);
                $dx = 10*cos($a+0.8);
                $dy = 10*sin($a+0.8);
                imageline($im,$cx,$cy,$cx+$dx,$cy+$dy,$line_color);
                $dx = 10*cos($a-0.8);
                $dy = 10*sin($a-0.8);
                imageline($im,$cx,$cy,$cx+$dx,$cy+$dy,$line_color);
            }
        }
    }
}

function draw_paths($im,$sectors,$centerx,$centery,$scalefactor,$fg_color,$fg_color_no_wander){
    $query = $query . " select id,x,y,z,prev_point from sc_path_points where " . $sectors;
    //echo $query;
    $res = mysql_query2($query);

    // exit if there is no data
    $num = mysql_num_rows($res);
    if ($num==0)
      return;

    $i=0;
    while ($line = mysql_fetch_array($res, MYSQL_NUM)){
        $id      = $line[0];
        $x       = $line[1];
        $y       = $line[2];
        $z       = $line[3];
        $radius  = 5;

	$ix = $centerx+($x*$scalefactor);
	$iy = $centery-($z*$scalefactor);
        $ir = $radius;
        imagearc($im,$ix,$iy,$ir,$ir,0,360,$fg_color);

	if ($line[4] != 0)
        {
            $query2 = "select p1.x,p1.y,p1.z,p2.x,p2.y,p2.z from sc_path_points p1, sc_path_points p2 where p1.id = p2.prev_point and p2.id = ".$id;
            $res2=mysql_query2($query2); 
            while ($line2 = mysql_fetch_array($res2, MYSQL_NUM)){
                $x1 = $line2[0];
                $y1 = $line2[1];
                $z1 = $line2[2];
                $x2 = $line2[3];
                $y2 = $line2[4];
                $z2 = $line2[5];

                $ix1 = $centerx+($x1*$scalefactor);
                $iy1 = $centery-($z1*$scalefactor);
                $ix2 = $centerx+($x2*$scalefactor);
                $iy2 = $centery-($z2*$scalefactor);

                $line_color = $fg_color;
 
                imageline($im,$ix1,$iy1,$ix2,$iy2 , $line_color);
            }           
        }

    }
}

function draw_locations($im,$sectors,$centerx,$centery,$scalefactor,$fg_color){
    $query = "select id,x,y,z,radius,id_prev_loc_in_region from sc_locations where " . $sectors;
    $res = mysql_query2($query);

    // exit if there is no data
    $num = mysql_num_rows($res);
    if ($num==0)
      return;

    $i=0;
    while ($line = mysql_fetch_array($res, MYSQL_NUM)){
        $id      = $line[0];
        $x       = $line[1];
        $y       = $line[2];
        $z       = $line[3];
        $radius  = $line[4];
        $id_prev = $line[5];

	$ix1 = $centerx+($x*$scalefactor);
	$iy1 = $centery-($z*$scalefactor);
        $ir = $radius*$scalefactor;
        imagearc($im,$ix1,$iy1,$ir,$ir,0,360,$fg_color);

	if ( $id_prev > 0 )
        {
	    $query2 = "select x,y,z from sc_locations where " . $sectors . " and id = " . $id_prev;
	    $res2 = mysql_query2($query2);
            while ($line2 = mysql_fetch_array($res2, MYSQL_NUM)){
               $x2       = $line2[0];
               $y2       = $line2[1];
               $z2       = $line2[2];
 
	       $ix2 = $centerx+($x2*$scalefactor);
               $iy2 = $centery-($z2*$scalefactor);
               imageline($im,$ix1,$iy1,$ix2,$iy2,$fg_color);
	    }
        }

    }
}



function draw_map($sector){

    $image_name = $sector.".gif";

    $data = getDataFromArea($sector);
    $sectors = $data[0];
    $centerx = $data[1];
    $centery = $data[2];
    $scalefactor = $data[3];

    #Open gif and copy into truecolor png.
    $gifim     = imagecreatefromgif($image_name);
    $w         = imagesx($gifim);
    $h         = imagesy($gifim);
    $im        = imagecreatetruecolor($w,$h);
    imagecopy($im,$gifim,0,0,0,0,$w,$h);

    $red        = imagecolorallocate($im, 255,   0,   0);
    $green      = imagecolorallocate($im, 128, 255,   0);
    $dark_green = imagecolorallocate($im,   0, 128,   0);
    $orange     = imagecolorallocate($im, 255, 128,   0);
    $gray       = imagecolorallocate($im, 228, 228, 228);
    $blue       = imagecolorallocate($im,   0,   0, 128);

    draw_locations($im,$sectors,$centerx,$centery,$scalefactor,$red);
    draw_waypoints($im,$sectors,$centerx,$centery,$scalefactor,$orange,$blue);
    draw_paths($im,$sectors,$centerx,$centery,$scalefactor,$gray,$blue);
    draw_natural_resources($im,$sectors,$centerx,$centery,$scalefactor,$green,$dark_green);

    imagepng($im);
    imagedestroy($im);
}

?>
