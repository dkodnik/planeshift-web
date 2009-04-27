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
  $typevals["items_resource"] = "SELECT i.id, CONCAT_WS(' - ', c.name, i.name) FROM item_stats AS i LEFT JOIN item_categories AS c ON i.category_id=c.category_id WHERE i.stat_type='B' ORDER BY c.name IN ('Raw Materials') DESC, c.name";
  $typevals["skill"] = "SELECT skill_id, name FROM skills ORDER BY name";
  $typevals["category"] = "SELECT category_id, name FROM item_categories ORDER BY name";
  $typevals["icon"] = "SELECT MIN(id),CONCAT_WS(' - ',id ,string) FROM common_strings WHERE common_strings.string LIKE '%_icon.dds' GROUP BY id";
  $typevals["mesh"] = "SELECT MIN(id),CONCAT_WS(' - ', id, string) FROM common_strings WHERE common_strings.string LIKE '%#%' GROUP BY id";
  $typevals["loot"] = "SELECT id, CONCAT_WS(' - ', id, name) FROM loot_rules ORDER by id";
  $typevals["spawn"] = "SELECT id, name FROM npc_spawn_rules ORDER by name";
  $typevals["sector"] = "SELECT name, name FROM sectors ORDER BY name";
  $typevals["sectorid"] = "SELECT id, name FROM sectors ORDER BY name";
  $typevals["scripts"] = "SELECT name, name from progression_events ORDER BY name";
  $typevals["cstring"] = "SELECT id, CONCAT_WS(' - ',id, string) FROM common_strings ORDER BY id";
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

  $query = $typevals[$type];

  $result = mysql_query2($query);
  return $result;
}

/*DrawSelectBox() creates and returns the string for the <select> box*/
function DrawSelectBox($type, $result, $name, $value, $includenull=false){
  $type = strtolower($type);
  $typevals["items"] = '""';
  $typevals["skill"] = '"-1"';
  $typevals["category"] = '"-1"';
  $typevals["icon"] = '"0"';
  $typevals["mesh"] = '"0"';
  $typevals["loot"] = '"0"';
  $typevals["spawn"] = '"0"';
  $typevals["sector"] = '""';
  $typevals["sectorid"] = '""';
  $typevals["scripts"] = '""';
  $typevals["cstring"] = '"0"';
  $typevals["races"] = '""';
  $typevals["behaviour"] = '""';
  $typevals["b_region"] = '""';
  $typevals["ways"] = '""';
  $typevals["cast_events"] = '""';
  $typevals["glyphs"] = '""';
  $typevals["locations"] = '"-1"';
  $typevals["process"] = '""';
  $typevals["patterns"] = '"0"';


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
        case 5: return "Head";
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

?>