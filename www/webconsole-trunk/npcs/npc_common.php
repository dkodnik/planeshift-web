<?PHP

function printChildTriggers($fatherName, $area, $priorResponse, $trigcount, $attcount, $type){ 

  // escape fields
  $escaped_area=mysql_escape_string($area);

  // FOR NOW WE ASSUME THAT DIALOGUES ARE TREES, GRAPHS ARE NOT YET SUPPORTED
  $prior = $priorResponse; 
  // given the above assumption, plus the assumption that all triggers have the same attitude range when they share the response
  // we can use this query
  $query = "select id,trigger_text from npc_triggers where prior_response_required=$prior and area='" . $escaped_area . "' ";
  // echo "query: $query<br>";
  $result = mysql_query2($query); 
  // every time the response changes we have a new trigger folder
  $prevResponse = -1;
  $prevTrigger = '';

  $atleastone = 0; 
  //echo "exploring prior: $prior\n<br>";
  // for every row result of the query
  while ($line = mysql_fetch_array($result, MYSQL_NUM)){ 
    $numrows= mysql_num_rows($result);
    //echo "numrows: $numrows\n<br>";

    // substitute any " with \" . Those are present in scripts
    $trigger_clean = str_replace("\"", "\\\"", $line[1]); 
    // escape fields
    $escaped_trigger=mysql_escape_string($line[1]);
    $triggerid = $line[0];

    // detects if the trigger is a script
    $is_script = parseTriggerScript($line[1]);
    if ($is_script[0] == '1'){
      $trigger_clean = $is_script[1];
    }


    // output trigger and phrase nodes
    $trigcount++;
    $currentTriggerName = "trig$trigcount";
    echo "$currentTriggerName = insFld($fatherName, gFld(\"trigger $trigger_clean\", \"index.php?page=npc_actions&operation=viewtrigger&triggerid=$triggerid&prior=$prior&type=$type&area=$area\"));\n";


    $atleastone = 1;
    $currResponse = $line[1];
//    if ($prevResponse != $currResponse){ 
      // if it's not the first pass output attitudes
//      if ($prevResponse != -1){

        $query = "select r.response1, r.id, t.id, t.prior_response_required from npc_responses r, npc_triggers t where t.id=r.trigger_id ";
        $query = $query . " and prior_response_required=$prior and area='$escaped_area' and r.trigger_id='$triggerid'";

//         echo "$query<br>";
        $result2 = mysql_query2($query);
        while ($line2 = mysql_fetch_array($result2, MYSQL_NUM)){
          $smaller = substr($line2[0],0,20);
          echo "att$attcount = insFld($currentTriggerName, gFld(\"response $smaller\", \"index.php?page=npc_actions&operation=viewresponse&prior=$prior&responseid=$line2[1]&type=$type&area=$area\"));\n";
          echo "att$attcount.iconSrc = ICONPATH + \"attitude.gif\"\n";
          echo "att$attcount.iconSrcClosed = ICONPATH + \"attitude.gif\"\n";
//          echo "nochilds = insDoc(att$attcount, gLnk(\"R\", \"responses $smaller\", \"index.php?page=npc_actions&operation=viewresponse&prior=$prior&responseid=$line2[1]&type=$type&area=$area\"));\n";
          // recursively go down in the tree with prior as current response
          //echo "attcount before: $attcount \n";
          $attfatherName = "att$attcount";
          $attcount++;

          $counts = printChildTriggers($attfatherName, $area, $line2[1], $trigcount, $attcount, $type); 

          // set values based on the recursive func result
          $trigcount = $counts[0];
          $attcount = $counts[1];

          //echo "attcount after: $attcount \n";


          // echo "attitude: $prevTrigger prior: $line2[3]\n";
        }
//      }
      // echo "output trigger with prior $prior\n";

//      echo "temp = insDoc($currentTriggerName, gLnk(\"R\", \"phrase $trigger_clean\", \"index.php?page=npc_actions&operation=viewphrase&triggerid=$triggerid&prior=$prior&type=$type&area=$area\"));\n";
//    }else{
//      echo "temp = insDoc($currentTriggerName, gLnk(\"R\", \"phrase $trigger_clean\", \"index.php?page=npc_actions&operation=viewphrase&triggerid=$triggerid&prior=$prior&type=$type&area=$area\"));\n";
//    }
//    $prevResponse = $currResponse;
//    $prevTrigger = mysql_escape_string($line[1]);
  }

  $counts[0] = $trigcount;
  $counts[1] = $attcount;
  return $counts;
}

function getKAs(){ 
  // search all KAs
  $query = "select distinct(area) from npc_triggers";
  $result = mysql_query2($query);
  $i = 0;
  while ($line = mysql_fetch_array($result, MYSQL_NUM)){
    $areas[$i] = strtolower($line[0]);
    $i++;
  } 
  // search names of npcs
  $query2 = "select distinct(name) from characters where npc_master_id!=0";
  $result2 = mysql_query2($query2);
  $i = 0;
  while ($line = mysql_fetch_array($result2, MYSQL_NUM)){
    $names[$i] = strtolower($line[0]);
    $i++;
  } 
  // search KAs excluding npc specific kas
  $result = array_values(array_diff ($areas, $names)); 
  // DEBUG
  if (0){
    for ($i = 0; $i < sizeof($areas); $i++){
      echo "$i - $areas[$i]<br>";
    }
    echo "------------------<br>";
    for ($i = 0; $i < sizeof($names); $i++){
      echo "$i - $names[$i]<br>";
    }
    echo "------------------<br>";
    for ($i = 0; $i < sizeof($result); $i++){
      echo "$i - $result[$i]<br>";
    }
  }

  return $result;
}

function redirectOnType($type, $area){
  if ($type == 'ka'){

    ?><SCRIPT language="javascript">
            top.location = "index.php?page=viewka&area=<? echo $area;
    ?>";
         </script>
      <?PHP
  }
  if ($type == 'npc'){
      // area is now name and lastname combined
      $name = strtok($area, " ");
      $lastname = strtok(" ");
    $query_string = "select id from characters where name='$name' and lastname='$lastname'";
    echo "$area <br>";
    $result = mysql_query2($query_string);
    $line = mysql_fetch_array($result, MYSQL_NUM);
    $id = $line[0];

    ?><SCRIPT language="javascript">
            top.location = "index.php?page=viewnpc&id=<? echo $id;
    ?>";
         </script>
      <?PHP
  }else{

    ?><SCRIPT language="javascript">
            top.location = "index.php?page=viewka&area=<? echo $area;
    ?>";
         </script>
      <?PHP
  }
}

function getIdForReload($type, $area){
  $query = "select id from characters where name='$area'";
  $result = mysql_query2($query);
  $line = mysql_fetch_array($result, MYSQL_NUM);
  $id = $line[0];
  return $id;
}

function triggerFromID($triggerid){
  $query = "select trigger_text from npc_triggers where id=$triggerid";
  $result = mysql_query2($query);
  $line = mysql_fetch_array($result, MYSQL_NUM);
  $trigger_name = $line[0];

  return $trigger_name;
}

function parseTriggerScript($trigger){
  $pos = stristr($trigger, "<l");
  $istrigger = 0;
  $result[0] = 0;
  if ($pos != false) {
    $istrigger = 1;
    $result[0] = 1;
  }

  // parse trigger
  if ($istrigger==1)
  {
    $pos = strpos($trigger, "money");
    if ( $pos != 0){
      $pos = strpos($trigger, "\"");
      $endmoney = substr($trigger,$pos+1);
      $pos = strpos($endmoney, "\"");
      $endmoney = substr($endmoney,0,$pos);
      $result[1] = "!EXCHANGE SCRIPT!";
      $result[2] = $endmoney;
      $pos = strpos($trigger, "n=\"");
      if ($pos) {
        $item = substr($trigger,$pos+3);
        //echo "TEST TEST $item TEST $pos<br>";
        $pos = strpos($item, "\"");
        $item = substr($item,0,$pos);
        //echo "TEST TEST $item TEST $pos<br>";
        $result[3] = $item;

        $pos = strpos($trigger, "c=\"");
        $itemcount = substr($trigger,$pos+3);
        $pos = strpos($itemcount, "\"");
        $itemcount = substr($itemcount,0,$pos);
        $result[4] = $itemcount;
      }

    }else{
      $result[1] = "!UNKNOWN SCRIPT!";
    }
  }
  
  return $result;
  
}

/******************************************************************************
 Gets the next bulk location for the given character.  This will always return
 one more than the max slot so make sure that npcs have a packed inventory.
******************************************************************************/
function GetNextEmptyBulkLocation($id)
{
    $query = "SELECT MAX(location_in_parent) FROM item_instances where char_id_owner=$id";
    $result = mysql_query2($query);
    $line = mysql_fetch_array($result, MYSQL_NUM);
    $count = $line[0];
    $count++;
    // 16 is the start of the bulk locations 
    if ( $count < 16 )
    {
        $count = 16;
    }

        // 48 is the end of the bulk locations 
    if ( $count >= 48 )
    {
        $count = 47;
    }

    return $count;
}

function AddSelected($valid_slots, $slotStr, $slotInt, $name)
{
    if ( strstr( $valid_slots, $slotStr ) )
    {   
        echo "<OPTION VALUE=$slotInt>$name</OPTION>";
    }

}

    
/******************************************************************************
Prints a select box for all the available slots for item
******************************************************************************/
function PrintAvailableSlots($itemid, $name)
{
    $query = "SELECT item_stats_id_standard FROM item_instances WHERE id=$itemid";
    $result = mysql_query2($query);
    
    $line = mysql_fetch_array($result, MYSQL_NUM);
    $item_stats = $line[0];   
    
    $query = "SELECT  valid_slots FROM item_stats WHERE id=$item_stats";
    $result = mysql_query2($query);
    
    $line = mysql_fetch_array($result, MYSQL_NUM);
    $valid_slots = $line[0];   
    
    echo "<SELECT NAME=$name>";
    
    AddSelected($valid_slots, "RIGHTHAND", 0, "Right Hand");
    AddSelected($valid_slots, "LEFTHAND", 1, "Left Hand");
    AddSelected($valid_slots, "BOTHANDS", 2, "Both Hands");
    AddSelected($valid_slots, "BOTHANDS", 2, "Both Hands");
    AddSelected($valid_slots, "RIGHTFINGER", 3, "Right Finger");
    AddSelected($valid_slots, "LEFTFINGER", 4, "Left Finger");
    AddSelected($valid_slots, "HEAD", 5, "Head");
    AddSelected($valid_slots, "NECK", 6, "Neck");
    AddSelected($valid_slots, "BACK", 7, "Back");
    AddSelected($valid_slots, "ARMS", 8, "Arms");
    AddSelected($valid_slots, "GLOVES", 9, "Gloves");
    AddSelected($valid_slots, "BOOTS", 10, "Boots");
    AddSelected($valid_slots, "LEGS", 11, "Legs");
    AddSelected($valid_slots, "BELT", 12, "BELT");
    AddSelected($valid_slots, "BRACERS", 13, "Bracers");
    AddSelected($valid_slots, "TORSO", 14, "Torso");
    AddSelected($valid_slots, "MIND", 15, "Mind");
    
    if ( strstr($valid_slots, "BULK") )
    {
        for ( $i = 1; $i < 31; $i++ )
        {            
            $name=$i+15;
            echo "<OPTION VALUE=$name $selectedStr>BULK $i</OPTION>"; 
        }
    }    
    echo "</SELECT>";
}


/******************************************************************************
Returns the string for the decimal value of a location
******************************************************************************/
function StringToLocation($string)
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
    
    return "Unknown";  
}


/******************************************************************************
Returns the string for the decimal value of a location
******************************************************************************/
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
    
    return "Unknown";  
}



function getPreferredLocation($itemid)
{
    $query = 'select name,item_type from item_stats where id='.$itemid;
    $result = mysql_query2($query);
    $line = mysql_fetch_array($result, MYSQL_NUM);
    $name = $line[0];

    $location = "inv_1";

    if (strstr($line[0], "Arms Armor"))
      $location = "equ_arms";
    else if (strstr($line[0], " Boots"))
      $location = "equ_boots";
    else if (strstr($line[0], " Gloves"))
      $location = "equ_gloves";
    else if (strstr($line[0], " Helm"))
      $location = "equ_head";
    else if (strstr($line[0], " Pants"))
      $location = "equ_legs";
    else if (strstr($line[0], " Torso Armor"))
      $location = "equ_torso";
    else if (strstr($line[0], " Torso Armor"))
      $location = "equ_torso";
    else if (strstr($line[1], "SHIELD"))
      $location = "equ_lefthand";
    else if (strstr($line[1], "SWORD") || strstr($line[1], "AXE") || strstr($line[1], "DAGGER"))
      $location = "equ_righthand";

    return $location;
}

?>
