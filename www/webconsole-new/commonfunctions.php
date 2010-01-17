<?php
/*mysql_query2() calls mysql_query and dies with a failure message if there
are any mysql errors*/
function mysql_query2($a, $log=true){
  $t1 = microtime(true);
  $b = mysql_query($a) or die('<p class="error">Query: '.$a.' failed with Error:'.mysql_error().'</p>');
  $t2 = microtime(true);
  $t_fin = $t2 - $t1;
  $t_fin = sprintf("%01.4f", $t_fin);
  $_SESSION['totalq'] = $_SESSION['totalq'] . "<br/>\n" . htmlspecialchars($a) .' -- '.$t_fin.' Seconds.';
  if ($log === true){
    $foo = explode(' ', $a, 2);
    if (strcasecmp($foo[0], 'SELECT') != 0){
      $foo = mysql_real_escape_string($a);
      $user = mysql_real_escape_string($_SESSION['username']);
      date_default_timezone_set('UTC');
      $date = date("Y-m-d H:i:s");
      $query = "INSERT INTO wc_cmdlog (username, query, date) VALUES ('$user', '$foo', '$date')";
      mysql_query2($query, false);
    }
  }
  return $b;
}

/*StripInput() gets rid of any artifacts of magic_slashes, and prepares
everything for mysql_real_escape_string*/
function StripInput(){
  if (get_magic_quotes_gpc()){
  function stripslashes_deep($value){
    $value = is_array($value) ?
    array_map('stripslashes_deep', $value) :
    stripslashes($value);
    return $value;
  }
  $_POST = array_map('stripslashes_deep', $_POST);
  $_GET = array_map('stripslashes_deep', $_GET);
  $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
  $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
  }
}

/*CheckLogin() returns 1 if logged in, 0 if not logged in - Also checks 
  session information to validate login connection */
function CheckLogin(){
  if(isset($_SESSION['username'])){
    //Here they are logged in, Now we check their IP address
    if ($_SESSION['REMOTE_ADDR'] == $_SERVER['REMOTE_ADDR']){
      //Now we know that their IP address is the same as their last login
      return 1;
    }
  }
  return 0;
}

/*SetUpDB() creates the connections to the DataBase - no return value*/
function SetUpDB($a, $b, $c, $d){
  $link = mysql_connect("$a", "$b", "$c") or die('mysql_connect failed:' . mysql_error());
  mysql_select_db("$d", $link) or die('<p class="error">Could not select database: '. mysql_error().'</p>');
}

/*DoLogin() checks for the existance of a "login" flag to check for login 
information, and if present processes it. On a successful login, it sets
$_SESSION, and returns 1, otherwise, it displays a login screen, and returns 0*/
function DoLogin(){
  if(isset($_POST['login'])){
    //They have hit the login button, now we check their login info.
    $username = mysql_real_escape_string($_POST['username']);
    $password = mysql_real_escape_string($_POST['password']);
    $query = "SELECT security_level FROM accounts WHERE username='$username' AND password=MD5('$password') AND security_level > 0;";
    $result = mysql_query2($query);
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      $_SESSION['username'] = $username;
      $_SESSION['security_level'] = $row['security_level'];
      $_SESSION['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
      if (isset($_POST['remember'])){
        setcookie('autologin', '1', time()+60*60*24*30);
        setcookie('autoname', "$username", time()+60*60*24*30);
        setcookie('autopass', md5($password), time()+60*60*24*30);
      }
      return 1;
    }
    echo '<p class="error">Incorrect Username or Password.</p>'."\n";
  }else if (isset($_COOKIE['autologin'])){
    $username = mysql_real_escape_string($_COOKIE['autoname']);
    $password = mysql_real_escape_string($_COOKIE['autopass']);
    $query = "SELECT security_level FROM accounts WHERE username='$username' AND password='$password' AND security_level > 0";
    $result = mysql_query2($query);
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      $_SESSION['username'] = $username;
      $_SESSION['security_level'] = $row['security_level'];
      $_SESSION['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
      return 1;
    }
  }
  return 0;
}

/*DisplayLogin displays the login dialog box*/
function DisplayLogin(){
  echo '<form action="./index.php" method="post">'."\n";
  echo '<p>Username:<input type="text" name="username" /><br/>'."\n";
  echo 'Password:<input type="password" name="password" /><br/>'."\n";
  echo 'Remember me<input type="checkbox" name="remember" /><br/>'."\n";
  echo '<input type="submit" name="login" value="Log In" /></p>'."\n";
  echo '</form>'."\n";
}

/*CacheAccess() reads the database, and caches the appropriate rights*/
function CacheAccess(){
  if (!isset($_SESSION['access']) && ($_SESSION['security_level'] != 50)){
    $query = 'SELECT objecttype, access FROM wc_accessrules WHERE security_level='.$_SESSION['security_level'];
    $result = mysql_query2($query);
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      $_SESSION['access'][$row['objecttype']]=$row['access'];
    }
  }
}

/*CheckAccess() uses the data stored in $_SESSION['access'] and the passed 
values to determine if the requested function is allowed
returns 1 if the access is allowed, 0 if the access is not allowed*/
function CheckAccess($type, $access){
 //First we check for security_level of 0 - players need to sod off
  if ($_SESSION['security_level'] == 0)
    return 0;
 //Next we check for 50 (admin can do anything they damn well please
  if ($_SESSION['security_level'] == 50)
    return 1;
 //Now we need to go to work, and check in $_SESSION['access'] to see if we're allowed
  if (strcmp('read', $access) == 0){
    $level = 1;
  }
  elseif (strcmp('edit', $access) == 0){
    $level = 2;
  }
  elseif (strcmp('create', $access) == 0){
    $level = 3;
  }
  elseif (strcmp('delete', $access) == 0){
    $level = 4;
  }
  else{
    $level = 5;
    //This should never happen, and should effectively LOCK the access as nothing in accessrules should be >4
  }
  if (isset($_SESSION['access'][$type])){
    if ( $_SESSION['access'][$type] >= $level){
      return 1;
    }
  }
  return 0;
}

/*GetNextID() returns the next valid id in the table that is specified*/
function GetNextID($a){
  $query = 'SELECT MAX(id) FROM '.$a;
  $result = mysql_query2($query);
  $row = mysql_fetch_row($result);
  $id = $row[0]+1;
  return $id;
}

/*PrepSelect() Returns the result link appropriate for the table requested*/
function PrepSelect($a){
  $type = strtolower($a);
  $typevals["items"] = "SELECT i.id, CONCAT_WS(' - ', c.name, i.name) FROM item_stats AS i LEFT JOIN item_categories AS c ON i.category_id=c.category_id WHERE i.stat_type='B' ORDER BY c.name, i.name"; 
  $typevals["items_resource"] = "SELECT i.id, CONCAT_WS(' - ', c.name, i.name) FROM item_stats AS i LEFT JOIN item_categories AS c ON i.category_id=c.category_id WHERE i.stat_type='B' AND (c.category_id='12' OR c.category_id='16' OR c.category_id='26' OR c.category_id='27') ORDER BY c.name, i.name";
  $typevals["skill"] = "SELECT skill_id, name FROM skills ORDER BY name";
  $typevals["category"] = "SELECT category_id, name FROM item_categories ORDER BY name";
  $typevals["loot"] = "SELECT id, CONCAT_WS(' - ', id, name) FROM loot_rules ORDER by id";
  $typevals["spawn"] = "SELECT id, name FROM npc_spawn_rules ORDER by name";
  $typevals["sector"] = "SELECT name, name FROM sectors ORDER BY name";
  $typevals["sectorid"] = "SELECT id, name FROM sectors ORDER BY name";
  $typevals["scripts"] = "SELECT name, name from progression_events ORDER BY name";
  $typevals["races"] = "SELECT id, CONCAT_WS(' - ', sex, name) AS info FROM race_info ORDER BY name";
  $typevals["behaviour"] = "SELECT DISTINCT npctype, npctype FROM sc_npc_definitions ORDER BY npctype";
  $typevals["b_region"] = "SELECT DISTINCT region, region FROM sc_npc_definitions ORDER BY region";
  $typevals["ways"] = "SELECT id, name FROM ways ORDER BY name";
  $typevals["cast_events"] = "SELECT name, name FROM progression_events WHERE name LIKE 'cast %'";
  $typevals["glyphs"] = "SELECT id, name FROM item_stats WHERE category_id='5' ORDER BY name";
  $typevals["locations"] = "SELECT id, name FROM sc_locations ORDER BY name";
  $typevals["process"] = "SELECT DISTINCT process_id, CONCAT(process_id, ' - ', name) FROM trade_processes ORDER BY name";
  $typevals["patterns"] = "SELECT id, pattern_name FROM trade_patterns ORDER BY pattern_name";
  $typevals["mind_slot_items"] = "SELECT id, name FROM item_stats WHERE stat_type='B' AND valid_slots LIKE '%MIND%' ORDER BY name";
  $typevals["waypoints"] = "SELECT w.id, CONCAT(s.name, ' -- ', w.name, ' -- ', ' X: ', w.x, ' Y: ', w.y, ' Z: ', w.z) FROM sc_waypoints AS w LEFT JOIN sectors AS s ON w.loc_sector_id=s.id ORDER BY s.name, w.name";
  
  $query = $typevals[$type];

  $result = mysql_query2($query);
  return $result;
}

/*DrawSelectBox() creates and returns the string for the <select> box*/
function DrawSelectBox($type, $result, $name, $value, $includenull=false){
  $type = strtolower($type);
  $typevals["items"] = '"0"';
  $typevals["skill"] = '"-1"';
  $typevals["category"] = '"-1"';
  $typevals["loot"] = '"0"';
  $typevals["spawn"] = '"0"';
  $typevals["sector"] = '""';
  $typevals["sectorid"] = '""';
  $typevals["scripts"] = '""';
  $typevals["races"] = '""';
  $typevals["behaviour"] = '""';
  $typevals["b_region"] = '""';
  $typevals["ways"] = '""';
  $typevals["cast_events"] = '""';
  $typevals["glyphs"] = '""';
  $typevals["locations"] = '"-1"';
  $typevals["process"] = '""';
  $typevals["patterns"] = '"0"';
  $typevals["mind_slot_items"] = '"0"';
  $typevals["waypoints"] = '""';

  $nullval = $typevals[$type];

  mysql_data_seek($result, 0);
  $string = '<select name="'.$name.'">';
  if ($includenull){
    $string = $string.'<option value='.$nullval.'>NONE</option>';
  }
  while ($internal_row = mysql_fetch_row($result)){
    $string = $string . '<option value="'.$internal_row[0].'"';
    if ($value == $internal_row[0]){
      $string = $string . ' selected="selected" ';
    }
    if ($internal_row[1] == ""){
      $string = $string . '>'.htmlspecialchars('NULL').'</option>';
    }else{
      $string = $string . '>'.htmlspecialchars($internal_row[1]).'</option>';
    }
  }
  $string = $string . '</select>';
  return $string;
}

function LocationToString($id)
{
    switch ($id)
    {
        case 0: return "Right Hand";
        case 1: return "Left Hand";
        case 2: return "Both Hands";
        case 3: return "Right Finger";
        case 4: return "Left Finger";
        case 5: return "Helm";
        case 6: return "Neck";
        case 7: return "Back";
        case 8: return "Arms";
        case 9: return "Gloves";

        case 10: return "Boots";
        case 11: return "Legs";
        case 12: return "Belt";
        case 13: return "Bracers";
        case 14: return "Torso";
        case 15: return "Mind";
    }

    if ( $id >= 16 && $id <= 47 )
    {
        $bulk = $id-15;
        $slot = "Bulk $bulk";
        return $slot;
    }

    return $id;
}

/*
    This method contains hardcoded coords and part of SQL statements. They are the sizes of each map, as well as which sectors they 
    span in the database. This data "should" be put in a database someday(TM), since right now you will need to edit this code
    every time a map gets added to the world. (This gets used for drawing maps.)
*/
function getDataFromArea($area) {

  // sectors
  $data;
  if ($area=='hydlaa_plaza') {
      $data[0] = 'loc_sector_id=15 or loc_sector_id=52';
      $data[1] = 535;
      $data[2] = 180;
      $data[3] = 2.2;
      $data[4] = 2.2;
      $data[5] = array('hydlaa_plaza', 'tavern_de_kadel');
  } else if ($area=='hydlaa_jayose') {
      $data[0] = 'loc_sector_id=49 or loc_sector_id=40';
      $data[1] = -250;
      $data[2] = -150;
      $data[3] = 3.2;
      $data[4] = 3.2;
      $data[5] = array('hydlaa_jayose', 'jayose_inside');
  } else if ($area=='hydlaa_winch') {
      $data[0] = 'loc_sector_id=72';
      $data[1] = 200;
      $data[2] = -430;
      $data[3] = 2.2;
      $data[4] = 2.2;
      $data[5] = array('hydlaa_winch');
  } else if ($area=='sewers') {
      $data[0] = 'loc_sector_id>26 and loc_sector_id<38';
      $data[1] = 645;
      $data[2] = 175;
      $data[3] = 2.5;
      $data[4] = 2.5;
      $data[5] = array('swr-pink', 'swr-lightgreen', 'swr-yellow', 'swr-purple', 'swr-red', 'swr-blue', 'swr-orange', 'swr-darkgreen01', 'swr-darkgreen02', 'swr-lightblue01', 'swr-lightblue02');
  } else if ($area=='laanxdungeon') {
      $data[0] = 'loc_sector_id>43 and loc_sector_id<49';
      $data[1] = 1452;
      $data[2] = 815;
      $data[3] = 6.73;
      $data[4] = 6.73;
      $data[5] = array('laanxdungeon', 'wtowerdung', 'wtower', 'wtowerexit', 'wtowertop');
  } else if ($area=='arena') {
      $data[0] = 'loc_sector_id>3 and loc_sector_id<15';
      $data[1] = 470;
      $data[2] = 456;
      $data[3] = 4.43;
      $data[4] = 4.43;
      $data[5] = array('hall', 'trans1', 'dngn', 'cntr', 'trans2', 'merc', 'upper', 'entr', 'outer', 'hycorr1', 'hycorr2');
  } else if ($area=='ojaroad1') {
      $data[0] = 'loc_sector_id=22';
      $data[1] = 603;
      $data[2] = 600;
      $data[3] = 0.9;
      $data[4] = 0.9;
      $data[5] = array('ojaroad1');
  } else if ($area=="ojaroad2") {
      $data[0] = 'loc_sector_id=59';
      $data[1] = 632;
      $data[2] = 625;
      $data[3] = 1;
      $data[4] = 1;
      $data[5] = array('ojaroad2');
  } else if ($area=='akkaio') {
      $data[0] = 'loc_sector_id>16 and loc_sector_id<20';
      $data[1] = 445;
      $data[2] = 465;
      $data[3] = 5.3;
      $data[4] = 5.3;
      $data[5] = array('ojapath', 'Akk-Central', 'Akk-East');
  } else if ($area=='bdroad1') {
      $data[0] = 'loc_sector_id=60';
      $data[1] = 491;
      $data[2] = 493;
      $data[3] = 0.76;
      $data[4] = 0.76;
      $data[5] = array('bdroad1');
  } else if ($area=='bdroad2') {
      $data[0] = 'loc_sector_id=61';
      $data[1] = 669;
      $data[2] = 667;
      $data[3] = 1.07;
      $data[4] = 1.07;
      $data[5] = array('bdroad2');
  } else if ($area=='bdoorsout') {
      $data[0] = 'loc_sector_id=67';
      $data[1] = 832;
      $data[2] = -749;
      $data[3] = 0.55;
      $data[4] = 0.55;
      $data[5] = array('bdoorsout');
  } else if ($area=='bdoorsin') {
      $data[0] = 'loc_sector_id=66';
      $data[1] = 656;
      $data[2] = -977;
      $data[3] = 2.2;
      $data[4] = 2.2;
      $data[5] = array('bdoorsin');
  } else if ($area=='gugrontid') {
      $data[0] = 'loc_sector_id=77';
      $data[1] = 240;
      $data[2] = 673;
      $data[3] = 1.3;
      $data[4] = 1.3;
  } else if ($area=='NPCroom') {
      $data[0] = 'loc_sector_id=3';
      $data[1] =  321.94;
      $data[2] = -371.05;
      $data[3] =    3.22;
      $data[4] =    3.26;
      $data[5] = array('NPCroom');
  } else if ($area=='npcroom2') {
      $data[0] = 'loc_sector_id=6 or loc_sector_id=7';
      $data[1] = 460;
      $data[2] = 318;
      $data[3] = 4;
      $data[4] = 4;
      $data[5] = array('dngn', 'cntr');
  }

  return $data;
}

function getAccountsToExclude() {
	$sql = "select id from accounts a where security_level>0 ";

	//echo $sql;
	$query = mysql_query2($sql);
	$to_exclude = "(";
	while($result = mysql_fetch_array($query, MYSQL_ASSOC))
	{
		$to_exclude .= $result['id'].",";
	}
	$to_exclude = substr($to_exclude,0,-1);
	$to_exclude .= ")";
	//echo "$to_exclude";
	return $to_exclude;
}

function getNextQuarterPeriod($groupid) {
    $sql = "SELECT MAX(periodname) AS max FROM wc_statistics WHERE groupid = '$groupid' ORDER BY periodname";

    $result = mysql_fetch_array(mysql_query2($sql), MYSQL_ASSOC);
    $max = $result['max'];
    
    $year = substr($max, 0, 4);
    $quarter = substr($max, 5, 6);
    
    if($quarter == 'Q4')
    {
      $year = $year+1;
      $quarter = 'Q1';
    }
    else
    {
      $quarter = 'Q'. (substr($quarter, 1, 2) + 1);
    }

    return $year.' '.$quarter;
}

function validatePeriod($period) {
    
    $year = substr($period, 0, 4);
    $quarter = substr($period, 5, 6);
	
	if ($year=='' || $quarter=='')
		return 0;
	
	if ($quarter!="Q1" && $quarter!="Q2" && $quarter!="Q3" && $quarter!="Q4")
		return 0;
	
	return 1;
}

function getDatesFromPeriod($period) {
    
    $year = substr($period, 0, 4);
    $quarter = substr($period, 5, 6);
    
    if($quarter == 'Q1')
    {
      $start = $year."-01-01";
      $end = $year."-03-31";
    }
    else if($quarter == 'Q2')
    {
      $start = $year."-04-01";
      $end = $year."-06-30";
    }
    else if($quarter == 'Q3')
    {
      $start = $year."-07-01";
      $end = $year."-09-30";
    }
    else if($quarter == 'Q4')
    {
      $start = $year."-10-01";
      $end = $year."-12-31";
    }

	$dates[1] = $start;
	$dates[2] = $end;
    return $dates;
}

function getAssetsDir() {

	return "D:\\Luca\\PS_distro\\distroCB\\repo\\planeshift\\art";
}

?>
