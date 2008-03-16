<?PHP

include('util.php');

//--------------------------------

//---for names mapping look at the end of the file 

//-------------------------------

function viewmain(){
    /**
     * view main info of an NPC
     */
    $id = $_GET['npcid']; 
    // get name of NPC
    $query = 'select name, lastname from characters where id=' . $id;
    $result = mysql_query2($query);
    $line = mysql_fetch_array($result, MYSQL_NUM);
    $area = $line[0];
    $lastname = $line[1];

    printf('<h2>%s %s [%d]</h2>', $area, $lastname, $id); 
    // displays base info
    $query = 'select * from characters where id=' . $id;
    $result = mysql_query2($query);
    $line = mysql_fetch_array($result, MYSQL_ASSOC);

    $masternpc = $line['npc_master_id'];
    $str = $line['base_strength'];
    $agi = $line['base_agility'];
    $end = $line['base_endurance'];
    $int = $line['base_intelligence'];
    $wil = $line['base_will'];
    $cha = $line['base_charisma'];
    $hp = $line['base_hitpoints_max'];
    $mana = $line['base_mana_max'];
    $spawn = $line['npc_spawn_rule'];
    $loot = $line['npc_addl_loot_category_id'];
    $descr = $line['description'];
    $raceid = $line['racegender_id'];
    $invulnerable = $line['npc_impervious_ind'];
    $kill_exp = $line['kill_exp'];

    // get behaviour
    $query = 'select char_id, name,npctype,region from sc_npc_definitions where char_id=' . $id;
    $result2 = mysql_query2($query);
    $line2 = mysql_fetch_array($result2, MYSQL_ASSOC);
    $behaviour = $line2['npctype'];
    $behaviour_region = $line2['region'];

    echo "<FORM action=index.php?page=npc_actions&npcid=$id&operation=editmain METHOD=POST>";

    echo "<TABLE><TR><TD valign=top>Description:</td><td> <textarea name=description cols=50 rows=3>$descr</textarea></td></tr></table><br>";

    if ($masternpc == 0 || $masternpc == $id){
        echo 'This NPC is not using any NPC template, skills and traits are its own.<br>';
        echo "You can set the master npc id to <INPUT type=text size='6' name=masternpc value=$masternpc>";
    }else{
        $query = 'select name from characters where id=' . $masternpc;
        $result = mysql_query2($query);
        $line = mysql_fetch_array($result, MYSQL_NUM);

        echo "This NPC is using <b>NPC template</b>: <A HREF='index.php?page=viewnpc&id=".$masternpc."' target=_top>$line[0] </a> with <b>id</b> <INPUT type=text name=masternpc value=$masternpc size='6'>, skills and traits are defined there.";
    }

    echo '<br><br>';
    // extract position and sector
    $query = 'select sec.name, c.loc_x, c.loc_y, c.loc_z, c.loc_yrot, c.loc_instance from characters as c, sectors as sec ';
    $query = $query . " where c.id=$id and c.loc_sector_id=sec.id";
    $result = mysql_query2($query);
    $line = mysql_fetch_array($result, MYSQL_NUM);

    echo '<TABLE>';
    echo "<TR><TD>Sector:</TD><TD>";
    DrawSelectBox("sector", "sector", $line[0], FALSE);
    echo '<TR><TD>Location:</TD><TD><TABLE><TR><TH> x </TH><TH> y </TH><TH> z </TH><TH> rotation </TH><TH> instance </TH></TR>';
    echo "<TR><TD><input size=4 type=text name=locx value=$line[1]><TD><input size=4 type=text name=locy value=$line[2]></TD><TD><input size=4 type=text name=locz value=$line[3]></TD><TD><input size=4 type=text name=locrot value=$line[4]></TD><TD><input size=4 type=text name=locinst value=$line[5]></TD></TD></TR>";
    echo '</TABLE></TD></TR>';
    echo '<TR><TD>&nbsp;</TD><TD></TD></TR>'; 
    // get race and gender list
    echo '<TR><TD>Race and gender:</TD><TD><SELECT name=raceid>';
    $query3 = 'select id, name, sex from race_info order by name, sex';
    $result3 = mysql_query2($query3);
    while ($line3 = mysql_fetch_array($result3, MYSQL_NUM)){
        if ($line3[0] == $raceid){
            echo "<OPTION value=$line3[0] SELECTED>$line3[1] - $line3[2]";
        }else{
            echo "<OPTION value=$line3[0]>$line3[1] - $line3[2]";
        }
    }
    echo '</SELECT></TD></TR>';

    echo "<TR><TD>Strength:</TD><TD> <INPUT size='6' type=text name=str value=$str></TD></TR>";
    echo "<TR><TD>Agility:</TD><TD> <INPUT size='6' type=text name=agi value=$agi></TD></TR>";
    echo "<TR><TD>Endurance:</TD><TD> <INPUT size='6' type=text name=end value=$end></TD></TR>";
    echo "<TR><TD>Intelligence:</TD><TD> <INPUT size='6' type=text name=int value=$int></TD></TR>";
    echo "<TR><TD>Will:</TD><TD> <INPUT type=text size='6' name=wil value=$wil></TD></TR>";
    echo "<TR><TD>Charisma:</TD><TD> <INPUT size='6' type=text name=cha value=$cha></TD></TR>";
    echo '<TR><TD>&nbsp;</TD><TD></TD></TR>';
    echo "<TR><TD>Base HP:</TD><TD> <INPUT size='6' type=text name=hp value=$hp> (Place 0 if you want it to be calculated from stats.)</TD></TR>";
    echo "<TR><TD>Base Mana:</TD><TD> <INPUT size='6' type=text name=mana value=$mana> (Place 0 if you want it to be calculated from stats.)</TD></TR>";
    echo '<TR><TD>&nbsp;</TD><TD></TD></TR>';
    echo "<TR><TD>Invulnerable:</TD><TD><SELECT name=invulnerable>";

    if ($invulnerable == 'N'){
            echo "<OPTION value=N SELECTED>No</OPTION>";
            echo "<OPTION value=Y >Yes</OPTION>";
    }else{
            echo "<OPTION value=N>No</OPTION>";
            echo "<OPTION value=Y SELECTED>Yes</OPTION>";
    }

    echo "</SELECT> (When set to Yes, the NPC cannot be attacked by any means)</TD></TR>";
    echo "<TR><TD>Experience:</TD><TD> <INPUT type=text size='6' name=kill_exp value=$kill_exp> (Experience given when killing this NPC)</TD></TR>";
    echo '<TR><TD>&nbsp;</TD><TD></TD></TR>';
    echo "<TR><TD>Spawn Rule:</TD><TD>";
    DrawSelectBox("spawn", "spawn", $spawn, TRUE);
    echo " (NONE prevents NPC from spawning) </TD></TR>";
    echo "<TR><TD>Loot Rule:</TD><TD> ";
    DrawSelectBox("loot", "loot", $loot, TRUE);
    echo "</TD></TR>";
    echo "<TR><TD>Behaviour/region:</TD><TD>";
    if ($behaviour=='New Behavior')
    {
      echo "<INPUT type=text name=behaviour value='$behaviour'>";
    } else {
      SelectBehavior($behaviour,'behaviour');
    }
    echo "/";
        SelectRegion($behaviour_region,'behaviour_region');
    echo "<INPUT type=hidden name=npcname value=$area></TD></TR>";
    echo '<TR><TD><INPUT type=submit name=save value=save></TD><TD></TD></TR>';
    echo '</TABLE></FORM>';
}

function editmain(){
    /**
     * edit main info of an NPC
     */
    $id = $_GET['npcid'];

    $description = $_POST['description'];
    $masternpc = $_POST['masternpc'];
    $raceid = $_POST['raceid'];
    $str = $_POST['str'];
    $agi = $_POST['agi'];
    $end = $_POST['end'];
    $int = $_POST['int'];
    $wil = $_POST['wil'];
    $cha = $_POST['cha'];
    $hp = $_POST['hp'];
    $mana = $_POST['mana'];
    $spawn = $_POST['spawn'];
    $loot = $_POST['loot'];
    $kill_exp = $_POST['kill_exp'];
    $invulnerable = $_POST['invulnerable'];

    $sector = $_POST['sector'];
    $locx = $_POST['locx'];
    $locy = $_POST['locy'];
    $locz = $_POST['locz'];
    $locrot = $_POST['locrot']; 
    $locinst = $_POST['locinst'];
    // get sector id or create a new one
    $query = "select id from sectors where name='$sector'";
    $result = mysql_query2($query);
    $line = mysql_fetch_array($result, MYSQL_NUM);

    if ($line != ''){
        $sectorid = $line[0];
    }else{
        $sectorid = getNextId('sectors', 'id');
        $query = "insert into sectors values ($sectorid, '$sector',0,0,0,0,0,0,0,0)";
        $result = mysql_query2($query);
    }

    // saves data
    $query = "update characters set description='$description', base_strength=$str, base_agility=$agi, base_endurance=$end, base_intelligence=$int, ";
    $query = $query . "racegender_id=$raceid, ";
    $query = $query . "base_will=$wil, base_charisma=$cha, base_hitpoints_max=$hp, base_mana_max=$mana, npc_spawn_rule=$spawn, npc_addl_loot_category_id=$loot, ";
    $query = $query . "loc_sector_id=$sectorid, loc_x=$locx, loc_y=$locy, loc_z=$locz, loc_yrot=$locrot, loc_instance=$locinst , ";
    $query = $query . "npc_impervious_ind='$invulnerable', kill_exp=$kill_exp, "; 
    $query = $query . " npc_master_id=$masternpc where id=" . $id;
    //echo "$query";
    $result = mysql_query2($query); 

    $behaviour = $_POST['behaviour'];
    $region_id = $_POST['behaviour_region'];
    $name = $_POST['npcname'];

    //delete previous behaviour
    $query = "delete from sc_npc_definitions where char_id=$id";
    $result = mysql_query2($query);
    $line = mysql_fetch_array($result, MYSQL_NUM);
    
    // create new beahviour
    if ($behaviour!='') {
          $behaviour_region = GetRegionName($region_id);
        $query = "insert into sc_npc_definitions(char_id,name,npctype,region,console_debug) values ($id,'$name','$behaviour','$behaviour_region','N')";
        echo "$query";
        $result = mysql_query2($query);
        $line = mysql_fetch_array($result, MYSQL_NUM);
        }

    // redirect
    ?><SCRIPT language="javascript">
          document.location = "index.php?page=npc_actions&operation=viewmain&npcid=<?=$id?>";
       </script>
    <?PHP

}
function viewskills(){
    /**
     * view skills of an NPC
     */
    $id = $_GET['npcid'];

    $query = "select s.name, c.skill_rank, s.skill_id, c.skill_Y, c.skill_Z from skills s, character_skills c where c.skill_id=s.skill_id and character_id=" . $id;
    $result = mysql_query2($query);
    $found = 0;

    echo '<b>Skills present in this NPC: </b><br><br>';

    echo '<table border=1><th>Skill</th><th>Rank</th><th>Knowledge (Y) </th><th>Practice (Z)</th><th></th>';
    while ($line = mysql_fetch_array($result, MYSQL_NUM)){
        echo "<TR><TD><b>$line[0]</b>: </TD><TD>$line[1]</TD>";
        echo "<TD>$line[3]</TD><TD>$line[4]</TD>";
        echo "<TD><FORM action=index.php?page=npc_actions&operation=editskills&npcid=$id&subop=del&itemid=$line[2] METHOD=POST><INPUT type=submit name=submit value=Delete></FORM></TD></TR>";
        $found = 1;
    }
    echo '</TABLE><br><br>';

    if ($found == 0){
        echo 'No skills present in this NPC.<br><br>';
    }

    echo '<b>Add a Skill to this NPC: </b><br><br>';

    echo "<FORM action=index.php?page=npc_actions&operation=editskills&npcid=$id&subop=add METHOD=POST>";
    echo '<table><th>Skill</th><th>Rank</th><th>Knowledge (Y)</th><th>Practice (Z)</th><th></th>';
    echo '<tr><td>';
    DrawSelectBox('skill', 'itemid', '');
    echo '</SELECT></td>';
    echo '<TD><INPUT type=text name=skillrank size="4" value="0"></td>';
    echo '<TD><INPUT type=text name=skill_y size="4" value="0"></td>';
    echo '<TD><INPUT type=text name=skill_z size="4" value="0"></td>';
    echo '<td><INPUT type=submit name=submit value=Add></td></tr></table></FORM>';
}
function editskills(){
    /**
     * edit skills of an NPC
     */
    $id = $_GET['npcid'];

    $subop = $_GET['subop'];

    if ($subop == 'del'){
        $skillid = $_GET['itemid'];

        $query = "delete from character_skills where skill_id=$skillid and character_id=$id";
        $result = mysql_query2($query); 
        // redirect
        ?><SCRIPT language="javascript">
          document.location = "index.php?page=npc_actions&operation=viewskills&npcid=<?=$id?>";
       </script>
    <?PHP

    }else if ($subop == 'add'){
        $skillid = $_POST['itemid'];
        $skillrank = $_POST['skillrank'];
        $skill_y = $_POST['skill_y'];
        $skill_z = $_POST['skill_z'];

        $query = "insert into character_skills values($id, $skillid, $skill_z, $skill_y, $skillrank)";
        $result = mysql_query2($query); 
        // redirect
        ?><SCRIPT language="javascript">
          document.location = "index.php?page=npc_actions&operation=viewskills&npcid=<?=$id?>";
       </script>
    <?PHP

    }else{
        echo "Operation editskills supported, suboperation $subop not supported.";
    }
}
function viewtraits(){
    /**
     * view traits of an NPC
     */
    $id = $_GET['npcid'];

     $query = " select t.id, t.location, t.cstr_id_mesh, t.cstr_id_material, t.cstr_id_texture, t.name from traits t, character_traits c where c.trait_id=t.id ";
     $query = $query . " and c.character_id=" . $id;
    $result = mysql_query2($query);
    $found = 0;

    echo '<b>Traits present in this NPC: </b><br><br>';
    echo '<TABLE border=1><TH>Location</TH><TH>Name</TH><TH>Image</TH><TH>Mesh</TH><TH>Material</TH><TH>Delete</TH>';
    while ($line = mysql_fetch_array($result, MYSQL_NUM)){
          echo "<TR>
                       <TD VALIGN=top><b>$line[1]</b></TD>
                       <TD>$line[5]</TD>
                       <TD>$line[4]</TD>
                       <TD>$line[2]</TD>
                       <TD>$line[3]</TD>
                       <TD><FORM action=index.php?page=npc_actions&operation=edittraits&npcid=$id&subop=del&itemid=$line[0] METHOD=POST><INPUT type=submit name=submit value=Delete></FORM></TD>
                  </TR>";
        $found = 1;
    }
    echo '</TABLE><br><br>';

    if ($found == 0){
        echo 'No traits present in this NPC.<br><br>';
    }
    echo '<b>Add a Trait to this NPC: </b><br><br>';

    echo "<FORM action=index.php?page=npc_actions&operation=edittraits&npcid=$id&subop=add METHOD=POST>";
    $query = "select t.id,t.name,t.location from traits t, characters c where c.id=$id and c.racegender_id=t.race_id";
    $result = mysql_query2($query);

    echo '<SELECT name=itemid>';
    while ($line = mysql_fetch_array($result, MYSQL_NUM)){
        echo "<OPTION value=$line[0]>name=$line[1], location=$line[2]</OPTION>";
    }
    echo '</SELECT>';
    echo '<INPUT type=submit name=submit value=Add></FORM>';
}
function edittraits(){
    /**
     * edit traits of an NPC
     */
    $id = $_GET['npcid'];

    $subop = $_GET['subop'];

    if ($subop == 'del'){
        $traitid = $_GET['itemid'];

        $query = "delete from character_traits where trait_id=$traitid and character_id=$id";
        echo $query;
        $result = mysql_query2($query); 
        // redirect
        ?><SCRIPT language="javascript">
          document.location = "index.php?page=npc_actions&operation=viewtraits&npcid=<?=$id?>";
       </script>
    <?PHP

    }else if ($subop == 'add'){
        $traitid = $_POST['itemid'];

        $query = "insert into character_traits values($id, $traitid)";
        $result = mysql_query2($query); 
        // redirect
        ?><SCRIPT language="javascript">
          document.location = "index.php?page=npc_actions&operation=viewtraits&npcid=<?=$id?>";
       </script>
    <?PHP

    }else{
        echo "Operation edittraits supported, suboperation $subop not supported.";
    }
}
function viewkas(){
    /**
     * view kas of an NPC
     */
    $id = $_GET['npcid'];

  // get NPC name
  $query = "select name, lastname from characters where id=$id";
    $result = mysql_query2($query);
    $line = mysql_fetch_array($result, MYSQL_NUM);
    $name = $line[0] . " ". $line[1];

    $query = "select area, priority from npc_knowledge_areas where player_id=$id order by priority";
    $result = mysql_query2($query);
    $found = 0;

    echo '<b>Knowledge Areas present in this NPC: </b><br><br>';
    echo '<TABLE border=1><TH>Knowledge Area - </TH><TH>Priority</TH><TH></TH>';
    while ($line = mysql_fetch_array($result, MYSQL_NUM)){
        echo "<TR><TD VALIGN=top><b>$line[0]</b></TD><TD><FORM action=index.php?page=npc_actions&operation=editkas&npcid=$id&subop=mod METHOD=POST><select name=priority>";
        for($i=1;$i<=10;$i++){
          echo "<option value=$i";
          if($i == $line[1])
            echo " selected=selected";
          echo ">$i</option>";
        }
        echo "</select><input type=hidden name=area value=\"$line[0]\"></TD><TD><input type=submit name=mod value=\"Edit Priority\"></FORM><FORM action=index.php?page=npc_actions&operation=editkas&npcid=$id&subop=del METHOD=POST><INPUT type=hidden name=area value=\"$line[0]\"><INPUT type=submit name=submit value=Delete></FORM></TD></TR>";
        $found = 1;
    }
    echo '</TABLE><br><br>';

    if ($found == 0){
        echo 'No knowledge areas present in this NPC.<br><br>';
    }
    echo '<b>Add a Knowledge Areas to this NPC: </b><br><br>';

    echo "<FORM action=index.php?page=npc_actions&operation=editkas&npcid=$id&subop=add METHOD=POST>";
    $result = getKAs();

    echo '<TABLE><TH>Knowledge Area - </TH><TH>Priority</TH><TH></TH><TR><TD>';
    echo '<SELECT name=area>';
    echo "<OPTION value=\"$name\">[Add KA of this NPC]</OPTION>";
    for ($i = 0; $i < sizeof($result); $i++){
        echo "<OPTION value=$result[$i]>$result[$i]</OPTION>";
    }
    echo '</SELECT></TD><TD>';
    echo '<SELECT name=priority>';
    for ($i = 1;$i <= 10;$i++){
        echo "<OPTION value=$i>$i</OPTION>";
    }
    echo '</TD><TD><INPUT type=submit name=submit value=Add></TD></TR></TABLE></FORM>';
}
function editkas(){
    /**
     * edit kas of an NPC
     */
    $id = $_GET['npcid'];

    $subop = $_GET['subop'];

    if ($subop == 'del'){
        $area = $_POST['area'];

        $query = "delete from npc_knowledge_areas where player_id=$id and area='$area'";
        $result = mysql_query2($query);
        // redirect
        ?><SCRIPT language="javascript">
         document.location = "index.php?page=npc_actions&operation=viewkas&npcid=<?=$id?>";
       </script>
    <?PHP

    }else if ($subop == 'add'){
        $area = $_POST['area'];
        $priority = $_POST['priority'];

        $query = "insert into npc_knowledge_areas values($id, '$area', $priority)";
        echo "$query";
        $result = mysql_query2($query); 
        // redirect
        ?><SCRIPT language="javascript">
          document.location = "index.php?page=npc_actions&operation=viewkas&npcid=<?=$id?>";
       </script>
    <?PHP
    }else if ($subop == 'mod'){
      $id = $_GET['npcid'];
      $priority = $_POST['priority'];
      $area = $_POST['area'];
      $query = "UPDATE npc_knowledge_areas SET priority=$priority WHERE player_id='$id' AND area='$area'";
      $result = mysql_query2($query);
        ?><SCRIPT language="javascript">
          document.location = "index.php?page=npc_actions&operation=viewkas&npcid=<?=$id?>";
       </script>
    }else{
        echo "Operation editkas supported, suboperation $subop not supported.";
    }
}

/******************************************************************************
 * view items of an NPC
******************************************************************************/
function viewitems()
{
    $id = $_GET['npcid'];

    $query = "select i.id, name, i.location_in_parent, i.stack_count from item_instances i, item_stats s where i.item_stats_id_standard=s.id and i.char_id_owner=$id";
    $result = mysql_query2($query);
    $found = 0; 

    // find possible locations
    echo '<b>Items carried by this NPC: </b><br><br>';
    echo '<TABLE border=1>';
    echo '<th>Name</th><th>Location</th><th>count</th><th colspan=2>Functions</th>';
    while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
    {
        $itemid = $line['id'];
        $itemname = $line['name'];
        $invloc = $line['location_in_parent'];
        $stack = $line['stack_count'];
        
      
        echo "<TR>";
        echo "<TD><B>$itemname</B></TD>";
        $strLoc = LocationToString($invloc);
        echo "<TD>$strLoc</TD>";

        echo "<FORM ACTION=index.php?page=npc_actions&operation=edititems&npcid=$id&subop=save&itemid=$itemid METHOD=POST>";
        echo "<TD><INPUT TYPE=text NAME=stack SIZE=5 VALUE=$stack>";
        echo "<INPUT TYPE=SUBMIT NAME=submit VALUE=Update></TD>";
        echo "</FORM>";
        
        echo "<FORM ACTION=index.php?page=npc_actions&operation=edititems&npcid=$id&subop=changeloc&itemid=$itemid METHOD=POST>";
        echo "<TD>"; PrintAvailableSlots($itemid, "location");
        echo "<INPUT TYPE=submit NAME=submit VALUE=ChangeLocation></TD>";        
        echo "</FORM>";
        
        echo "<FORM ACTION=index.php?page=npc_actions&operation=edititems&npcid=$id&subop=del&itemid=$itemid METHOD=POST>";        
        echo "<TD><INPUT type=submit name=submit value=Delete></TD></TR>";
        echo "</FORM>";
        $found = 1;        
/*

        echo "<SELECT name=location><OPTION value=equ_righthand>righthand</OPTION><OPTION value=equ_lefthand>lefthand</OPTION>";
        echo "<OPTION value=equ_head>head</OPTION><OPTION value=equ_torso>torso</OPTION><OPTION value=equ_legs>legs</OPTION>";
        echo "<OPTION value=equ_arms>arms</OPTION><OPTION value=equ_gloves>gloves</OPTION><OPTION value=equ_boots>boots</OPTION>";
        for ($i = 0;$i < 32;$i++)
        {
            echo "<OPTION value=inv_$i>Inv slot $i</OPTION>";
        }

        echo '</SELECT>';

        echo '<INPUT type=submit name=submit value=ChangeLocation></FORM></TD>';
        echo "<TD><FORM action=index.php?page=npc_actions&operation=edititems&npcid=$id&subop=del&itemid=$itemid METHOD=POST><INPUT type=submit name=submit value=Delete></FORM></TD></TR>";
        $found = 1;
        */
    }

    echo '</TABLE><br><br>';

    if ($found == 0)
    {
        echo 'No Items carried by this NPC.<br><br>';
    }

    echo '<b>Add an Item to this NPC (quest items are not in the list): </b><br><br>';
    echo "<FORM action=index.php?page=npc_actions&operation=edititems&npcid=$id&subop=add METHOD=POST>";

    $base_item_max_id = getBaseItemMax ();

    $query = 'select id, item_type, name, flags from item_stats where id<'.$base_item_max_id.' AND category_id!=24  order by item_type, name';
    $result = mysql_query2($query);

    echo '<TABLE><TH>Item</TH><TH>Count</TH><TH></TH><TR><TD>';
    echo '<SELECT name=itemid>';

    while ($line = mysql_fetch_array($result, MYSQL_NUM))
    {
    	echo "<OPTION value=$line[0]>$line[1] : $line[2]</OPTION>";
    }

    echo '</SELECT></TD>';

    echo '<TD><input type=text name=stack size=5 value=1></TD>';
    echo '<TD><INPUT type=submit name=submit value=Add></FORM></TD></TR></TABLE>';
}

/******************************************************************************
* edit items of an NPC
******************************************************************************/
function edititems()
{
    $id = $_GET['npcid'];
    $stack = $_POST['stack'];
    $subop = $_GET['subop'];
    $location = $_POST['location'];
    
    if ($subop == 'changeloc')
    {
        $itemid = $_GET['itemid'];
        $query = "update item_instances set location_in_parent=$location where char_id_owner=$id and id=$itemid";
        $result = mysql_query2($query);
        
        // redirect
        ?><SCRIPT language="javascript">
            document.location = "index.php?page=npc_actions&operation=viewitems&npcid=<?=$id?>";
         </script>
      <?PHP

    }
    else if ($subop == 'save')
    {
        $itemid = $_GET['itemid'];
        $query = "update item_instances set stack_count=$stack where char_id_owner=$id and id=$itemid";
        $result = mysql_query2($query); 
        // redirect
        ?><SCRIPT language="javascript">
            document.location = "index.php?page=npc_actions&operation=viewitems&npcid=<?=$id?>";
         </script>
      <?PHP

    }
    else if ($subop == 'add')
    {
        $itemid = $_POST['itemid'];
        $slot = GetNextEmptyBulkLocation($id);
       
        $query = "INSERT INTO item_instances (char_id_owner, location_in_parent, stack_count, item_stats_id_standard ) VALUES ($id, $slot, $stack, $itemid )";
        $result = mysql_query2($query); 
        // redirect
        ?>
        <SCRIPT language="javascript">
            document.location = "index.php?page=npc_actions&operation=viewitems&npcid=<?=$id?>";
         </script>
     <?PHP
     }
    else if ($subop == 'del')
    {
        $itemid = $_GET['itemid'];
        $query = "delete from item_instances where char_id_owner=$id and id=$itemid";
        echo '$query';
        $result = mysql_query2($query); 
        // redirect
        ?><SCRIPT language="javascript">
            document.location = "index.php?page=npc_actions&operation=viewitems&npcid=<?=$id?>";
         </script>
      <?PHP

    }
    else
    {
        echo "Operation edititems supported, suboperation $subop not supported.";
    }
}
function viewresponse(){
    /**
     * view response node of an NPC
     */
    $respid = $_GET['responseid'];
    $type = $_GET['type'];
    $area = $_GET['area']; 
    // get the npc id just for the reload tree at the end
    $id = getIdForReload($type, $area);

    $query = "select response1, response2, response3, response4, response5, script from npc_responses where id=$respid";
    $result = mysql_query2($query);

  // replace all spaces with %20 so parameters in URLs are correct
  $area = str_replace(" ", "%20", $area);

    echo '<b>Response Edit</b><br><br>';


    echo '<br>Available responses. One will be chosen randomly each time the NPC answers.<br><br>';
    echo "You can use these variables inside the response: \$playername, \$playerrace, \$sir .<br> Those will be substituted in the response with player name, player race (enkidukai, ...), and with Madam or Sir depending on gender.<br> Be sure to avoid any punctuation just after the variable or it will not be substituted.<br><br>";


    while ($line = mysql_fetch_array($result, MYSQL_NUM)){

    // edit responses
      echo "<FORM action=index.php?page=npc_actions&operation=editresponse&responseid=$respid&area=$area&type=$type METHOD=POST><TABLE>";
        echo "<TR><TD><b>Response 1:</b></TD><TD><textarea name=response1 rows=2 cols=50>$line[0]</textarea></TD></TR>";
        echo "<TR><TD><b>Response 2:</b></TD><TD><textarea name=response2 rows=2 cols=50>$line[1]</textarea></TD></TR>";
        echo "<TR><TD><b>Response 3:</b></TD><TD><textarea name=response3 rows=2 cols=50>$line[2]</textarea></TD></TR>";
        echo "<TR><TD><b>Response 4:</b></TD><TD><textarea name=response4 rows=2 cols=50>$line[3]</textarea></TD></TR>";
        echo "<TR><TD><b>Response 5:</b></TD><TD><textarea name=response5 rows=2 cols=50>$line[4]</textarea></TD></TR>";
        echo '<TR><TD><input TYPE=SUBMIT NAME=submit VALUE="save responses"></TD><TD></TD></TR></TABLE></FORM>';

    // edit script
      echo "<TABLE><TR><TD><BR><BR><FORM action=index.php?page=npc_actions&operation=editresponsescript&responseid=$respid&area=$area&type=$type METHOD=POST></TD></TR>";
      echo '<TH>action</TH><TH>use only for train</TH><TH>quest name</TH><TH>items id given</TH><TH>exp given</TH><TH>money (C,O,H,T)</TH><TR><TD>';

    echo '<SELECT NAME=scriptelem>';
    echo '<OPTION value="respond">Say one response</OPTION>';
    echo '<OPTION value="animgreet">Play greet animation</OPTION>';
    echo '<OPTION value="train">Train player</OPTION>';
    echo '<OPTION value="assignquest">Assign quest</OPTION>';
    echo '<OPTION value="completequest">Complete quest</OPTION>';
    echo '<OPTION value="offeritem">Give Item</OPTION>';
    echo '<OPTION value="giveexp">Give Exp</OPTION>';
    echo '<OPTION value="givemoney">Give Money</OPTION>';

      echo '</SELECT></TD>';

    // skill field
      $query2 = 'select skill_id,name from skills ';
      $result2 = mysql_query2($query2);
      echo '<TD><SELECT name=skillname>';
      echo '<OPTION value="empty"></OPTION>';
      while ($line2 = mysql_fetch_array($result2, MYSQL_NUM)){
          echo "<OPTION value=\"$line2[1]\">$line2[1]</OPTION>";
      }
      echo '</SELECT></td>';

      // quest, items, exp fields
      echo '<td><input TYPE=text NAME=questname></td><td><input TYPE=text NAME=offeritem></td><td><input TYPE=text NAME=giveexp size=6></td><td><input TYPE=text NAME=givemoney size=8></td></TR>';

        echo '<TR><TD><input TYPE=SUBMIT NAME=submit VALUE="add action to script"></TD><TD></TD></TR></FORM>';

    // delete script
      echo "<FORM action=index.php?page=npc_actions&operation=editresponsedelscript&responseid=$respid&area=$area&type=$type METHOD=POST><TABLE>";

        echo '<TR><TD><BR><BR><input TYPE=SUBMIT NAME=submit VALUE="delete script"></TD><TD></TD></TR>';

        echo "<TR><TD><b>Current Script <br>(just for reference) :</b></TD><TD><textarea edit=false name=script rows=3 cols=50>$line[5]</textarea></TD></TR>";

    }
    echo '</TABLE></FORM>';

    echo "<FORM action='index.php?page=npc_actions&operation=addtrigger&prior=$respid&area=$area' METHOD=POST>";
    echo '<TABLE>';
    echo '<TR><TD><b>Add Trigger</b></td></TD></TR>';
    echo '<TR><TD>Phrase1:</td><td><input TYPE=text NAME=phrase1></TD></TR>';
    echo '<TR><TD>Phrase2:</td><td><input TYPE=text NAME=phrase2></TD></TR>';
    echo '<TR><TD>Phrase3:</td><td><input TYPE=text NAME=phrase3> (an empty field means it will not be added)</TD></TR>';
    echo "<input TYPE=hidden NAME=type value=$type>";
    echo "<TR><TD><input TYPE=SUBMIT NAME=submit VALUE=\"Add trigger\"></TD><td></td></TR>";
    echo '</TABLE>';
    echo '</FORM>';

}
function editresponse(){
    /**
     * edit response node of an NPC
     */
    $responseid = $_GET['responseid'];
    $type = $_GET['type'];
    $area = $_GET['area'];

    $response1 = $_POST['response1'];
    $response2 = $_POST['response2'];
    $response3 = $_POST['response3'];
    $response4 = $_POST['response4'];
    $response5 = $_POST['response5'];

    $query = "update npc_responses set response1='$response1', response2='$response2', response3='$response3', response4='$response4', response5='$response5' where id=$responseid";
    $result = mysql_query2($query);
    echo "$query";
    // redirect
    redirectOnType($type, $area);
}

function editresponsescript(){
    /**
     * edit script response of an NPC
     */
    $responseid = $_GET['responseid'];
    $type = $_GET['type'];
    $area = $_GET['area'];

    $scriptelem = $_POST['scriptelem'];
    $skillname = $_POST['skillname'];
    $questname = $_POST['questname'];
    $offeritem = $_POST['offeritem'];
    $giveitem = $_POST['giveitem'];
    $giveexp = $_POST['giveexp'];
    $givemoney = $_POST['givemoney'];

  // get current script
    $query = "select script from npc_responses where id=$responseid";
    $result = mysql_query2($query);
  $line = mysql_fetch_array($result, MYSQL_NUM);
    $script = $line[0];

  // remove last part
  if ($script=="") {
    $script = '<response>';
  } else {
    $pos = strpos($script, "</response>");
    $script = substr($script,0,$pos);
  }

  if ($scriptelem=='respond') {
    $script = $script . "<respond/>";
  } else if ($scriptelem=='animgreet') {
    $script = $script . "<action anim=\"greet\"/>";
  } else if ($scriptelem=='train') {
    $script = $script . "<train skill=\"$skillname\"/>";
  } else if ($scriptelem=='assignquest') {
    $script = $script . "<assign q1=\"$questname\"/>";
  } else if ($scriptelem=='completequest') {
    $script = $script . "<complete quest_id=\"$questname\"/>";
  } else if ($scriptelem=='offeritem') {
    $script = $script . "<offer>";
    if (strpos($offeritem, ",")) {
      $tok = strtok($offeritem, ",");
      $script = $script . "<item id=".$tok."/>";  
      while ($tok = strtok(","))
        $script = $script . "<item id=".$tok."/>";
    } else
      $script = $script . "<item id=".$offeritem."/>";
    $script = $script . "</offer>";

  } else if ($scriptelem=='giveexp') {
    $script = $script . "<run scr=\"give_exp\" param0=\"$giveexp\" />";
  } else if ($scriptelem=='givemoney') {
    $script = $script . "<money value=\"$givemoney\" />";
  }

  $script = $script . "</response>";

    $query = "update npc_responses set script='".$script."' where id=$responseid";
    //echo "$query"; 
    $result = mysql_query2($query);

    // redirect on same page
    echo "<SCRIPT language=\"javascript\">";
    echo "  this.location = \"index.php?page=npc_actions&operation=viewresponse&prior=0&responseid=$responseid&type=$type&area=$area\";";
    echo "  </script>";
}

function editresponsedelscript(){
    /**
     * delete script response of an NPC
     */
    $responseid = $_GET['responseid'];
    $type = $_GET['type'];
    $area = $_GET['area'];

    $query = "update npc_responses set script='' where id=$responseid";
    //echo "$query"; 
    $result = mysql_query2($query);

    // redirect on same page
    echo "<SCRIPT language=\"javascript\">";
    echo "  this.location = \"index.php?page=npc_actions&operation=viewresponse&prior=0&responseid=$responseid&type=$type&area=$area\";";
    echo "  </script>";

}

function viewka(){
    /**
     * view specificka main node of an NPC
     */
    $area = $_GET['area'];
    $type = $_GET['type']; 
    // get the npc id just for the reload tree at the end
    $id = getIdForReload($type, $area); 
    if ($type == 'npc'){
        echo "<h2>Specific knowledge for NPC: $area</h2>";
    }else if ($type == 'ka'){
        echo "<h2>Knowledge area : $area</h2>";
    }else{
        echo "$type not supported.";
        exit;
    }

    echo "<FORM action='index.php?page=npc_actions&operation=addtrigger&prior=0&area=$area' METHOD=POST>";
    echo '<TABLE>';
    echo '<TR><TD><b>Add Trigger</b></td></TD></TR>';
    echo '<TR><TD>Phrase1:</td><td><input TYPE=text NAME=phrase1></TD></TR>';
    echo '<TR><TD>Phrase2:</td><td><input TYPE=text NAME=phrase2></TD></TR>';
    echo '<TR><TD>Phrase3:</td><td><input TYPE=text NAME=phrase3> (an empty field means it will not be added)</TD></TR>';
    echo "<input TYPE=hidden NAME=type value=$type>";
    echo "<TR><TD><input TYPE=SUBMIT NAME=submit VALUE=\"Add trigger\"></TD><td></td></TR>";
    echo '</TABLE>';
    echo '</FORM>';
}

/**
 * add a trigger node and attitude nodes
 */
function addtrigger(){

    $area = $_GET['area'];
    $prior = $_GET['prior'];
    $type = $_POST['type'];
    // get the npc id just for the reload tree at the end
    $id = getIdForReload($type, $area); 
    // count phrases needed
    $numphrases = 1;
    $phrases[0] = $_POST['phrase1'];
    if ($_POST['phrase2'] != ''){
        $numphrases = 2;
        $phrases[1] = $_POST['phrase2'];
    }
    if ($_POST['phrase3'] != ''){
        $numphrases = 3;
        $phrases[2] = $_POST['phrase3'];
    }
    echo "phrases: $numphrases<br>"; 

    // cycle for each phrase
    for ($j = 0; $j < $numphrases; $j++){ 
        // search next trigger id
        echo "type: $type<br>";

        $query = "insert into npc_triggers values( '', \"$phrases[$j]\", $prior,  \"$area\")";
        echo "$query";
        $result = mysql_query2($query);
    } 
    // redirect
    $type = $_POST['type'];
    redirectOnType($type, $area);
}

/**
 * view attitude
 */
function viewattitude(){

    $area = $_GET['area'];
    $triggerid = $_GET['triggerid'];
    $prior = $_GET['prior'];
    $type = $_GET['type']; 
    // get the npc id just for the reload tree at the end
    $id = getIdForReload($type, $area);

    $query = "select min_attitude_required, max_attitude_required, response_id from npc_triggers where id=$triggerid";
    $result = mysql_query2($query);
    $line = mysql_fetch_array($result, MYSQL_NUM);

  // replace all spaces with %20 so parameters in URLs are correct
  $area = str_replace(" ", "%20", $area);

    echo '<b>Attitude Edit</b><br>';

    echo "<FORM action=index.php?page=npc_actions&operation=editattitude&prior=$prior&responseid=$line[2]&area=$area&type=$type METHOD=POST>";
    echo '<table><br>';
    echo '<th>Min</th><th>Max</th>';
    echo "<tr><td><input TYPE=text name=min value=$line[0]></td><td><input TYPE=text name=max value=$line[1]></td></tr>";
    echo "<TR><TD><input TYPE=SUBMIT NAME=submit VALUE=\"Save\"></TD><td></td></TR>";
    echo '</table>';
    echo '</form><br><br>';

    echo "<FORM action=index.php?page=npc_actions&operation=addtrigger&prior=$line[2]&area=$area METHOD=POST>";
    echo "<input TYPE=hidden NAME=type value=$type>";
    echo '<TABLE>';
    echo '<TR><TD colspan=2><b>Add Trigger as child of this attitude</b></td></TD></TR>';
    echo '<TR><TD>Phrase1:</td><td><input TYPE=text NAME=phrase1></TD></TR>';
    echo '<TR><TD>Phrase2:</td><td><input TYPE=text NAME=phrase2></TD></TR>';
    echo "<TR><TD>Phrase3:</td><td><input TYPE=text NAME=phrase3> (an empty field means it will not be added)</TD></TR>";
    echo '<TR><TD>&nbsp;</td><td>&nbsp;</TD></TR>';
    echo "<TR><TD>Attitude 1:</td><td>min:<input TYPE=text NAME=att1min value=-100> max:<input TYPE=text NAME=att1max value=-1></TD></TR>";
    echo "<TR><TD>Attitude 2:</td><td>min:<input TYPE=text NAME=att2min value=0> max:<input TYPE=text NAME=att2max value=100></TD></TR>";
    echo "<TR><TD>Attitude 3:</td><td>min:<input TYPE=text NAME=att3min> max:<input TYPE=text NAME=att3max> (an empty field means it will not be added)</TD></TR>";
    echo '<TR><TD>&nbsp;</td><td>&nbsp;</TD></TR>';
    echo "<TR><TD><input TYPE=SUBMIT NAME=submit VALUE=\"Add trigger\"></TD><td></td></TR>";
    echo '</TABLE>';
    echo '</FORM>';
}

/**
 * edit attitude
 */
function editattitude(){

    $area = $_GET['area'];
    $responseid = $_GET['responseid'];
    $prior = $_GET['prior'];
    $type = $_GET['type'];
    $min = $_POST['min'];
    $max = $_POST['max'];

    $query = "update npc_triggers set min_attitude_required=$min , max_attitude_required=$max where response_id=$responseid and area='$area' and prior_response_required=$prior";
    $result = mysql_query2($query); 
    // redirect

    redirectOnType($type, $area);
}

/**
 * view trigger
 */
function viewtrigger(){

    $area = $_GET['area'];
    $trigger = $_GET['trigger'];
    $triggerid = $_GET['triggerid'];
    $prior = $_GET['prior'];
    $type = $_GET['type']; 

    // get the npc id just for the reload tree at the end
    $id = getIdForReload($type, $area); 

    $query = "select t.trigger_text, t.id from npc_triggers t where t.id=$triggerid";
    $result = mysql_query2($query);

    // replace all spaces with %20 so parameters in URLs are correct
    $area = str_replace(" ", "%20", $area);

    echo '<b>Trigger Edit</b><br><br>';

    echo 'Current Phrases (To edit one phrases please click on the phrase node on the left tree)<br><br>';

    echo '<TABLE>';
    while ($line = mysql_fetch_array($result, MYSQL_NUM)){
        echo "<FORM action=index.php?page=npc_actions&operation=edittrigger&subop=savephrase&area=$area&prior=$prior&type=$type METHOD=POST>";
        echo "<TR><TD><b>Phrase:</b></TD><TD><textarea name=phrase cols=30 rows=1>$line[0]</textarea></TD><TD><input TYPE=SUBMIT NAME=submit VALUE=save></TD></TR>";
        echo "<input TYPE=hidden NAME=triggerid VALUE=$line[1]></FORM>";
        $is_script = parseTriggerScript($line[0]);
        if ($is_script[0] == '1'){
          echo "<TR><TD valign=top><b>Phrase:</b></TD>";
            if ($is_script[1] == "!EXCHANGE SCRIPT!"){
                echo "<td>EXCHANGE TRIGGER, fired when player gives:<br>";
                if ($is_script[2]!="0,0,0,0")
                  echo "Money: $is_script[2]<br>";
                if ($is_script[3])
                  echo "Item: $is_script[3]<br>";
              echo "</TD></tr>";
            }else{
                echo "<td>!UNKNOWN SCRIPT!</TD></tr>";
            }
        }
        $oldphrase = $line[0];
    }
    echo '</TABLE>';

    echo '<br><TABLE>';
    echo "<FORM action=index.php?page=npc_actions&operation=edittrigger&subop=addphrase&area=$area&prior=$prior&type=$type METHOD=POST>";
    echo "<TR><TD><b>Add Phrase:</b></TD><TD><textarea name=phrase cols=30 rows=1></textarea></TD><TD><input TYPE=SUBMIT NAME=submit VALUE=Add></TD></TR>";
    echo "<input TYPE=hidden NAME=triggerid VALUE=$triggerid></FORM>";
    echo '</TABLE>';

    echo '<br><br>';
    echo "<FORM action=index.php?page=npc_actions&operation=deltrigger&area=$area&prior=$prior&type=$type METHOD=POST>";
    echo "<input TYPE=SUBMIT NAME=submit VALUE=\"Delete this trigger\">";
    echo "<input TYPE=hidden NAME=triggerid VALUE=$triggerid></FORM>";
}

/**
 * edit trigger
 */
function edittrigger(){

    $area = $_GET['area'];
    $prior = $_GET['prior'];
    $subop = $_GET['subop'];
    $phrase = $_POST['phrase'];
    $triggerid = $_POST['triggerid'];
    $type = $_GET['type'];
    $area = $_GET['area'];
    $trigtype = $_POST['trigtype'];

    if ($subop == 'savephrase'){
        $oldphrase = triggerFromID($triggerid);
        
        // check exchange
        if ($trigtype=="newexchange")
          $phrase="<l money=\"$triggerid,0,0,0\"></l>";

           // if exchange
        if ($trigtype=="exchange") {
          $itemid = $_POST['itemid'];
          $itemcount = $_POST['count'];
          $trias = $_POST['trias'];
          $hexas = $_POST['hexas'];
          $octas = $_POST['octas'];
          $circles = $_POST['circles'];

                // use quest if other is empty
                if ($itemid=="none") {
                  $itemid = $_POST['itemidq'];
                }

          $phrase="<l money=\"$circles,$octas,$hexas,$trias\">";
          if ($itemid!="none") {
            $itemid = strtolower($itemid);
            $phrase=$phrase."<item n=\"$itemid\" c=\"$itemcount\" />";
          }
          $phrase=$phrase."</l>";
        }

        $query = "update npc_triggers set trigger_text='$phrase' where area='$area' and trigger_text='$oldphrase' and prior_response_required=$prior";
        $result = mysql_query2($query);

    }else if ($subop == 'addphrase'){
        echo "triggerid: $triggerid<br>"; 

        $query2 = "insert into npc_responses values('', '$triggerid', '$phrase','','','','','','','','','','','0')"; 
        // echo "$query2";
        $result2 = mysql_query2($query2);
    }else{
        echo "Operation edittrigger supported, suboperation $subop not supported.";
    } 
    // redirect
    redirectOnType($type, $area);
}

/**
 * delete trigger
 */
function deltrigger(){

    $area = $_GET['area'];
    $prior = $_GET['prior'];
    $type = $_GET['type'];
    $triggerid = $_POST['triggerid'];
    
    $trigger = triggerFromID($triggerid);

    // search all responseid of this trigger
    $query = "select r.id from npc_triggers t, npc_responses r where t.trigger_text='$trigger' and t.area='$area' and t.prior_response_required=$prior and r.trigger_id=t.id";

    //echo "$query<br>";
    $result = mysql_query2($query);

    $found = 0;

    while ($line = mysql_fetch_array($result, MYSQL_NUM)){
        $responseid = $line[0]; 
        // search if some trigger has this as previous response
        $query2 = "select count(*) from npc_triggers where prior_response_required=$responseid and area='$area'";
        //echo "$query2<br>";
        $result2 = mysql_query2($query2);
        $line2 = mysql_fetch_array($result2, MYSQL_NUM);
        if ($line2[0] != 0){
            echo "<b>This trigger can't be deleted because it has some subtriggers, please delete those first.</b><br>";
            break;
        }else{ 
            // delete response
            $query2 = "delete from npc_responses where trigger_id=$triggerid";
            echo "$query2<br>";
            $result2 = mysql_query2($query2); 
            // delete trigger
            $query2 = "delete from npc_triggers where id=$triggerid";
            echo "$query2<br>";
            $result2 = mysql_query2($query2); 
            // redirect
            redirectOnType($type, $area);
        }
    }
}

/**
 * view phrase
 */
function viewphrase(){

    $area = $_GET['area'];
    $trigger = $_GET['trigger'];
    $triggerid = $_GET['triggerid'];
    $prior = $_GET['prior'];
    $type = $_GET['type']; 
    // get the npc id just for the reload tree at the end
    $id = getIdForReload($type, $area); 
    // retrieve the trigger from trigger id
    $trigger = triggerFromID($triggerid);

  // replace all spaces with %20 so parameters in URLs are correct
  $area = str_replace(" ", "%20", $area);
  
    echo '<b>Phrase Edit</b><br><br>';

    echo '<TABLE>';
    echo "<FORM action=index.php?page=npc_actions&operation=edittrigger&subop=savephrase&area=$area&prior=$prior&type=$type METHOD=POST>"; 

  $trigtype = "Phrase";
    $is_script = parseTriggerScript($trigger);

     $base_item_max_id = getBaseItemMax ();

    if ($is_script[0] == '1'){

    $trigtype = "XML";

      echo "<TR><TD valign=top><b>Phrase:</b></TD>";
        if ($is_script[1] == "!EXCHANGE SCRIPT!"){
            echo "<td>EXCHANGE TRIGGER, fired when player gives:<br>";
            $tok1 = strtok($is_script[2],",");
            $tok2 = strtok(",");
            $tok3 = strtok(",");
            $tok4 = strtok(",");

      echo "<br>Money: trias <input type=text name=trias size=5 value=$tok4>, hexas <input type=text name=hexas size=5 value=$tok3>";
      echo "octas <input type=text name=octas size=5 value=$tok2>, circles <input type=text name=circles size=5 value=$tok1><br>";

        $query = 'select id, item_type, name, flags from item_stats where id<'.$base_item_max_id.' order by item_type, name';
        $result = mysql_query2($query);

        echo '<TABLE><TR><TD><br>Item: ';
        echo '<SELECT name=itemidq>';
        echo '<OPTION value=none>None';
        while ($line = mysql_fetch_array($result, MYSQL_NUM)){
          if (strstr($line[3], "QUESTITEM"))
            continue;
          if (strcasecmp($line[2],$is_script[3])==0)
              echo "<OPTION value=\"$line[2]\" SELECTED>$line[1] : $line[2]</OPTION>";
            else
              echo "<OPTION value=\"$line[2]\">$line[1] : $line[2]</OPTION>";
        }
        echo "</SELECT></TD><td></td></TR>";
        echo "<TR><TD><b>OR</b></TD></TR>";

        $query = 'select id, item_type, name, flags from item_stats where id<'.$base_item_max_id.' order by item_type, name';
        $result = mysql_query2($query);

        echo "<TR><TD><br>Quest Item: ";
        echo '<SELECT name=itemid>';
        echo '<OPTION value=none>None';
        while ($line = mysql_fetch_array($result, MYSQL_NUM)){
          if (!strstr($line[3], "QUESTITEM"))
            continue;
          if (strcasecmp($line[2],$is_script[3])==0)
              echo "<OPTION value=\"$line[2]\" SELECTED>$line[1] : $line[2]</OPTION>";
            else
              echo "<OPTION value=\"$line[2]\">$line[1] : $line[2]</OPTION>";
        }
        echo "</SELECT></TD><td><br>Count: <input type=text name=count size=5 value=$is_script[4]></td></TR></TABLE>";

            echo "</TD></tr><tr><td><br><br></td></tr>";
        }else{
            echo "<td>!UNKNOWN SCRIPT!</TD></tr>";
        }
    }

  if ($trigtype=="XML") {
    echo "<input type=hidden name=trigtype value=exchange>";
    echo "<TR><TD><input TYPE=SUBMIT NAME=submit VALUE=save><BR><BR></TD></TR>";
      echo "<TR><TD valign=top><b>Current XML <br> (just for reference):</b></TD><TD><textarea name=xml cols=30 rows=3>$trigger</textarea></TD><TD></TD></TR>";
      echo "<input type=hidden name=triggerid value=\"$triggerid\"></FORM>";
      echo '</TABLE>';

  } else {
    echo "<input type=hidden name=trigtype value=phrase>";
      echo "<TR><TD valign=top><b>$trigtype:</b></TD><TD><textarea name=phrase cols=30 rows=1>$trigger</textarea></TD><TD><input TYPE=SUBMIT NAME=submit VALUE=save></TD></TR>";
      echo "<input type=hidden name=triggerid value=\"$triggerid\"></FORM>";
      echo '</TABLE>';

  }

    echo "<FORM action=index.php?page=npc_actions&operation=edittrigger&subop=savephrase&area=$area&prior=$prior&type=$type METHOD=POST>";
    echo "<input type=hidden name=trigtype value=newexchange>";
  echo "<TR><TD valign=top><br><input TYPE=SUBMIT NAME=submit VALUE=\"Make this an exchange trigger\"></TD></TR>";
    echo "<input type=hidden name=triggerid value=\"$triggerid\"></FORM>";
 
    echo "<FORM action=index.php?page=npc_actions&operation=delphrase&area=$area&prior=$prior&type=$type METHOD=POST>";
    echo "<input TYPE=SUBMIT NAME=submit VALUE=\"Delete this phrase\">";
    echo "<input type=hidden name=triggerid value=\"$triggerid\"></FORM>";
}

/**
 * del phrase
 */
function delphrase(){

    $prior = $_GET['prior'];
    $area = $_GET['area'];
    $triggerid = $_POST['triggerid'];
    
    // retrieve the trigger from trigger id
    $trigger = triggerFromID($triggerid); 
    // search responseid of this trigger
    $query = "select response_id from npc_triggers where trigger_text='$trigger' and area='$area' and prior_response_required=$prior"; 
    // echo "$query<br>";
    $result = mysql_query2($query);
    $line = mysql_fetch_array($result, MYSQL_NUM);
    $responseid = $line[0]; 
    // search all triggers with same responseid
    $query = "select count(*) from npc_triggers where response_id=$responseid and area='$area' and prior_response_required=$prior"; 
    // echo "$query<br>";
    $result = mysql_query2($query);
    $line = mysql_fetch_array($result, MYSQL_NUM);
    $count = $line[0]; 
    // allow deletion ONLY if there are more triggers
    if ($count == 1){
        echo 'Only one phrase found. To delete this phrase you should delete the whole trigger';
    }else{
        $query = "delete from npc_triggers where area='$area' and trigger_text='$trigger' and prior_response_required=$prior";
        echo "$query";
        $result = mysql_query2($query); 
        // redirect
        $type = $_GET['type'];
        redirectOnType($type, $area);
    }
}

function deletenpc(){
    /**
     * delete an NPC
     */
    $npc_id = $_GET['npcid'];
    printf("You want to delete NPC %d <br>", $npc_id); 
    // get name of the NPC you want to delete
    $query_string = "select name from characters where id=" . $npc_id;

    $result = mysql_query2($query_string);
    $line = mysql_fetch_array($result, MYSQL_NUM);
    $npc_name = $line[0];

    printf("name of NPC: %s <br>", $npc_name); 

    // delete bad text
    $query_string = "delete from npc_bad_text where npc='" . $npc_name . "'";
    $result = mysql_query2($query_string);
    printf("deletion result: %s ,  from npc_bad_text <br>", $result); 
    // delete knowledge areas
    $query_string = "delete from npc_knowledge_areas where player_id ='" . $npc_id."'";
    $result = mysql_query2($query_string);
    printf("deletion result: %s ,  from npc_knowledge_areas <br>", $result); 
    // delete responses
    $query_string = "delete from npc_responses where id IN (select response_id from npc_triggers where area='" . $npc_name . "')";
    $result = mysql_query2($query_string);
    printf("deletion result: %s ,  from npc_responses <br>", $result); 
    // delete triggers
    $query_string = "delete from npc_triggers where area='" . $npc_name . "'";
    $result = mysql_query2($query_string);
    printf("deletion result: %s ,  from npc_triggers <br>", $result); 
    // delete items
    $query_string = "delete from item_instances where char_id_owner ='" . $npc_id."'";
    $result = mysql_query2($query_string);
    printf("deletion result: %s ,  from item_instances <br>", $result); 
    // delete traits
    $query_string = "delete from character_traits where character_id ='" . $npc_id."'";
    $result = mysql_query2($query_string);
    printf("deletion result: %s ,  from character_traits <br>", $result); 
    // delete npc
    $query_string = "delete from characters where id ='" . $npc_id."'";
    $result = mysql_query2($query_string);
    printf("deletion result: %s ,  from characters <br>", $result);
    
    echo "<script>document.location='index.php?page=listnpcs'</script>";
}

/******************************************************************************
 * Create a new NPC
******************************************************************************/
function createnpc()
{
    checkAccess('npc', '', 'create');

    $npcname = $_POST['npcname'];
    $npclastname = $_POST['npclastname'];

    $query = "select id,name,lastname from characters where name='$npcname'";
    $result = mysql_query2($query);
    $num_rows = mysql_num_rows($result);
    if ($num_rows>0) 
    {
        $line = mysql_fetch_array($result, MYSQL_NUM);
        echo "This name already exists (". $line[0] .") ". $line[1] ." ". $line[2];
        return;
    }

    $newnpcid = getNextId('characters', 'id');

    // the 27th field is loc_sector_id=1
    // account set to 9 to have npcclient manage it

    $query = "INSERT INTO characters (id, 
                                    name, lastname, 
                                    character_type, 
                                    loc_x, loc_y, loc_z, loc_sector_id, 
                                    account_id, 
                                    npc_master_id, 
                                    npc_impervious_ind) 
                        VALUES ($newnpcid, 
                               '$npcname', '$npclastname', 
                               1,
                               0, 0, 0, 3,
                               9,
                               $newnpcid,
                               'Y')";
  //$query = "insert into characters values($newnpcid, '$npcname', '$npclastname', '','',1,'','','','','','','','','','','','','','','','','','','','','','',1,'','','','','','','','','','','','',$newnpcid,'Y',9,'','','','','','',0,'',0)";
    $result = mysql_query2($query);

    $fullname = $npcname . " " . $npclastname;
    $query = "insert into npc_knowledge_areas values($newnpcid, '$fullname', 1)";
    
    $result = mysql_query2($query); 
    // redirect
    ?>
    <SCRIPT language="javascript">
          document.location = "index.php?page=listnpcsinv";
    </script>
    <?PHP
}


function createsimplenpc(){
    /**
     * create a simple NPC
     */
    checkAccess('npc', '', 'create');

    $npcname = $_POST['npcname'];
    $description = $_POST['description'];
    $race = $_POST['race'];
    $stats = $_POST['stats'];
    $hp = $_POST['hp'];
    $sector = $_POST['sector'];
    $position = $_POST['position'];
    $spawnrule = $_POST['spawnrule'];
    $weapon = $_POST['weapon'];
        $region = $_POST['region'];
        $behavior = $_POST['behavior'];
    $skill_value = $_POST['skill_value'];
    $exp = $_POST['exp'];

  // transforms stats to single elements
  $stat_str = strtok($stats, ",");
  $stat_agi = strtok(",");
  $stat_end = strtok(",");
  $stat_int = strtok(",");
  $stat_wil = strtok(",");
  $stat_cha = strtok(",");

  // transforms position to single elements
  $locx = strtok($position, ",");
  $locy = strtok(",");
  $locz = strtok(",");
  $locrot = strtok(",");

  // get skill to use
    $query_events = "select item_skill_id_1 from item_stats where id=$weapon";
    $result = mysql_query2($query_events);
    $line = mysql_fetch_array($result, MYSQL_NUM);
    $skill = $line[0];

    $newnpcid = getNextId('characters', 'id');

  // the 25th field is loc_sector_id=1
  $query = "insert into characters(id,name,racegender_id,character_type,base_strength,base_agility,base_endurance,base_intelligence,base_will,base_charisma,base_hitpoints_max,mod_hitpoints,stamina_physical,stamina_mental,loc_sector_id,loc_x,loc_y,loc_z,loc_yrot,npc_spawn_rule,npc_master_id,npc_impervious_ind,account_id,description,kill_exp) values($newnpcid, '$npcname', '$race','1','$stat_str','$stat_agi','$stat_end','$stat_int','$stat_wil','$stat_cha','$hp','$hp',100,100,$sector,$locx,$locy,$locz,$locrot,$spawnrule,$newnpcid,'N',9,'$description',$exp)";
  echo "$query";
    $result = mysql_query2($query);

    if ($skill != '')
        {
      $query = "insert into character_skills values($newnpcid, $skill, 0,0,$skill_value)";
      echo "$query\n";
      $result = mysql_query2($query);
        }
    if ($region != -1 || $behavior != 'None')
        {
           $behavior_region=GetRegionName($region);
       $query = "insert into sc_npc_definitions (char_id, name, npctype, region,console_debug) values ($newnpcid,'$npcname','$behavior','$behavior_region','N')";
        echo "$query\n";
           $result = mysql_query2($query);
        }
    // redirect
    ?><SCRIPT language="javascript">
          document.location = "index.php?page=listnpcs";
    </script>
    <?PHP

}


function viewtrainer(){
    /**
     * view trainer skills of an NPC
     */

    $id = $_GET['npcid'];

    $query = "select t.skill_id, s.name, t.min_rank, t.max_rank, t.min_faction from skills s, trainer_skills t where t.skill_id=s.skill_id and player_id=" . $id;
    $result = mysql_query2($query);
    $found = 0;

    echo '<b>Training Skills present in this NPC: </b>';
    echo '<p>A NPC will only train a player in the given skills, ';
    echo 'within the given rank range and if the faction between ';
    echo 'trainer and player is better than Min faction.</p>';

    echo '<table border=1><th>Skill</th><th>Min Rank</th><th>Max Rank</th><th>Min Faction</th><th></th>';
    while ($line = mysql_fetch_array($result, MYSQL_NUM)){
        echo "<TR><TD><b>$line[1]</b>: </TD><TD>$line[2]</TD>";
        echo "<TD>$line[3]</TD><TD>$line[4]</TD>";
        echo "<TD><FORM action=index.php?page=npc_actions&operation=edittrainer&npcid=$id&subop=del&itemid=$line[0] METHOD=POST><INPUT type=submit name=submit value=Delete></FORM></TD></TR>";
        $found = 1;
    }
    echo '</TABLE><br><br>';

    if ($found == 0){
        echo 'No training skills present in this NPC.<br><br>';
    }

    echo '<b>Add/Replace a Training Skill to this NPC: </b><br><br>';

    echo "<FORM action=index.php?page=npc_actions&operation=edittrainer&npcid=$id&subop=add METHOD=POST>";
    echo "<table border='1'><th>Skill</th><th>Min Rank</th><th>Max Rank</th><th>Min Faction</th><th></th>";
    echo '<tr><td>';
    DrawSelectBox('skill','itemid', '');
    echo '</SELECT></td>';
    echo "<TD><INPUT type=text name=min_rank value='0' size='4'></td>";
    echo "<TD><INPUT type=text name=max_rank  value='0' size='4'></td>";
    echo "<TD><INPUT type=text name=min_faction  value='0' size='4'></td>";
    echo "<td><INPUT type=submit name=submit value=Add></td></tr></table></FORM>";
}
function edittrainer(){
    /**
     * edit trainer skills of an NPC
     */

    $id = $_GET['npcid'];
    $subop = $_GET['subop'];
    $min_rank = $_POST['min_rank'];
    $max_rank = $_POST['max_rank'];
    $min_faction = $_POST['min_faction']; 
        
    if ($subop == 'del'){

      $skillid = $_GET['itemid'];

        $query = "delete from trainer_skills where skill_id=$skillid and player_id=$id";
        $result = mysql_query2($query); 
        // redirect
        ?><SCRIPT language="javascript">
          document.location = "index.php?page=npc_actions&operation=viewtrainer&npcid=<?=$id?>";
       </script>
    <?PHP

    }else if ($subop == 'add'){

      $skillid = $_POST['itemid'];
    
        // First delete trainer skill
        $query = "delete from trainer_skills where skill_id=$skillid and player_id=$id";
        echo "$query\n";
        $result = mysql_query2($query); 
        // Update with new
        $query = "insert into trainer_skills values($id, $skillid, $min_rank, $max_rank, $min_faction)";
        $result = mysql_query2($query); 
        // redirect
        ?><SCRIPT language="javascript">
          document.location = "index.php?page=npc_actions&operation=viewtrainer&npcid=<?=$id?>";
       </script>
    <?PHP

    }else{
        echo "Operation edittrainer supported, suboperation $subop not supported.";
    }
}

function viewmerchant(){
    /**
     * view merchant capabilities of an NPC
     */

    $id = $_GET['npcid'];

    $query = "select c.category_id, c.name from item_categories c, merchant_item_categories m where c.category_id=m.category_id and m.player_id=" . $id;
    $result = mysql_query2($query);
    $found = 0;

  echo '<b>Categories of items this NPC buys/sells</b><br><br>';

    echo '<table border=1><th>Category</th><th></th>';
    while ($line = mysql_fetch_array($result, MYSQL_NUM)){
        echo "<TR><TD><b>$line[1]</b></TD>";
        echo "<TD><FORM action=index.php?page=npc_actions&operation=editmerchant&npcid=$id&subop=del&itemid=$line[0] METHOD=POST><INPUT type=submit name=submit value=Delete></FORM></TD></TR>";
        $found = 1;
    }
    echo '</TABLE><br>';

    if ($found == 0){
        echo 'No merchant capabilities present in this NPC.<br><br>';
    }

    echo '<b>Add a Merchant Capability to this NPC: </b><br><br>';

    echo "<FORM action=index.php?page=npc_actions&operation=editmerchant&npcid=$id&subop=add METHOD=POST>";
    $query = 'select category_id,name from item_categories';
    $result = mysql_query2($query);

    echo "<table border='1'><th>Category</th><th></th>";
    echo '<tr><td><SELECT name=itemid>';
    while ($line = mysql_fetch_array($result, MYSQL_NUM)){
        echo "<OPTION value=$line[0]>$line[1]</OPTION>";
        $found = 1;
    }
    echo '</SELECT></td>';
    echo "<td><INPUT type=submit name=submit value=Add></td></tr></table></FORM>";
}

function editmerchant(){
    /**
     * edit merchant capabilities of an NPC
     */

    $id = $_GET['npcid'];
    $subop = $_GET['subop'];

        
    if ($subop == 'del'){

      $categoryid = $_GET['itemid'];
    
        $query = "delete from merchant_item_categories where category_id=$categoryid and player_id=$id";
        echo "$query <br>";
        $result = mysql_query2($query); 
        // redirect
        ?><SCRIPT language="javascript">
          document.location = "index.php?page=npc_actions&operation=viewmerchant&npcid=<?=$id?>";
       </script>
    <?PHP

    }else if ($subop == 'add'){

      $categoryid = $_POST['itemid'];

        // Add new
        $query = "insert into merchant_item_categories values($id, $categoryid)";
        $result = mysql_query2($query); 
        // redirect
        ?><SCRIPT language="javascript">
          document.location = "index.php?page=npc_actions&operation=viewmerchant&npcid=<?=$id?>";
       </script>
    <?PHP

    }else{
        echo "Operation editmerchant supported, suboperation $subop not supported.";
    }
}

function findtrigger(){
    /**
     * find a trigger word in all dialogues
     */

  outputHtmlHeader();
  
    $word = $_POST['word'];

    $query = "select id,trigger_text,area from npc_triggers where trigger_text='$word' or trigger_text like '% $word %' or trigger_text like '$word %' or trigger_text like '% $word'";
    $result = mysql_query2($query);
    

  echo '<b>Triggers found</b><br><br>';

    echo '<table border=1><th>ID</th><th>Trigger</th><th>Area</th>';
    while ($line = mysql_fetch_array($result, MYSQL_NUM)){
        echo "<TR><TD><b>$line[0]</b></TD><TD>$line[1]</TD><TD>$line[2]</TD></TR>";
    }
    echo '</TABLE><br>';
    outputHtmlFooter();
}


function savecombatnpc(){

    $id = $_GET['npcid'];
    $subop = $_GET['subop'];
    $righthand = $_POST['righthand'];
    $lefthand = $_POST['lefthand'];
    $exp = $_POST['exp'];

  // search max 3 skills
  $skill1id =  $_POST['skill1id'];
  $skill1value =  $_POST['skill1'];
  $skill2id =  $_POST['skill2id'];
  $skill2value =  $_POST['skill2'];
  $skill3id =  $_POST['skill3id'];
  $skill3value =  $_POST['skill3'];

  // update skills
  if ($skill1id!="") {
      $query = "update character_skills set skill_rank=$skill1value where skill_id=$skill1id and character_id=$id";
      //echo "$query";
      $result = mysql_query2($query);
    }
  if ($skill2id!="") {
      $query = "update character_skills set skill_rank=$skill2value where skill_id=$skill2id and character_id=$id";
      //echo "$query";
      $result = mysql_query2($query);
  }
  if ($skill3id!="") {
      $query = "update character_skills set skill_rank=$skill3value where skill_id=$skill3id and character_id=$id";
      //echo "$query";
      $result = mysql_query2($query);
  }

  // update weapons
    $query = "delete from item_instances where char_id_owner=$id and (equipped_slot='lefthand' or equipped_slot='righthand')";
  $result = mysql_query2($query);
  if ($righthand!="-1") {
      $query = "insert into item_instances values ('',$id,0,0,1,0,0,0,0,0,0,0,1,1,$righthand,0,'E','righthand','',0,-1,'')";
      //echo "$query";
      $result = mysql_query2($query);
  }
  if ($lefthand!="-1") {
      $query = "insert into item_instances values ('',$id,0,0,1,0,0,0,0,0,0,0,1,1,$lefthand,0,'E','lefthand','',0,-1,'')";
      //echo "$query";
      $result = mysql_query2($query);
  }

  // update exp
  $query = "update characters set kill_exp=$exp where id=$id";
  $result = mysql_query2($query);

  echo "<HTML><BODY BGCOLOR=#052F2E text=57B9CB link=#FFFFFF vlink=#FFFFFF alink=#FFFFFF><h3>Operation completed.</h3><BR>";
  //echo "To see the changes you have to refresh the first page manually.";

   //<SCRIPT language="javascript">
   //       document.location = "index.php?page=listnpcscombat";
   //    </script>

}

/*
*--------------------------------------------*

*--------------------------------------------*

*--------------------------------------------*
*/

function npc_actions(){
    include('npc_common.php');
    echo'<body  >'; 
    // gets operation to perform
    $operation = $_GET['operation'];

    if ($operation == 'viewmain'){
        viewmain();
    }else if ($operation == 'editmain'){
        editmain();
    }else if ($operation == 'viewskills'){
        viewskills();
    }else if ($operation == 'editskills'){
        editskills();
    }else if ($operation == 'viewtraits'){
        viewtraits();
    }else if ($operation == 'edittraits'){
        edittraits();
    }else if ($operation == 'viewkas'){
        viewkas();
    }else if ($operation == 'editkas'){
        editkas();
    }else if ($operation == 'viewitems'){
        viewitems();
    }else if ($operation == 'edititems'){
        edititems();
    }else if ($operation == 'viewresponse'){
        viewresponse();
    }else if ($operation == 'editresponse'){
        editresponse();
    }else if ($operation == 'editresponsescript'){
        editresponsescript();
    }else if ($operation == 'editresponsedelscript'){
        editresponsedelscript();
    }else if ($operation == 'viewka'){
        viewka();
    }else if ($operation == 'addtrigger'){
        addtrigger();
    }else if ($operation == 'viewattitude'){
        viewattitude();
    }else if ($operation == 'editattitude'){
        editattitude();
    }else if ($operation == 'viewtrigger'){
        viewtrigger();
    }else if ($operation == 'edittrigger'){
        edittrigger();
    }else if ($operation == 'deltrigger'){
        deltrigger();
    }else if ($operation == 'viewphrase'){
        viewphrase();
    }else if ($operation == 'delphrase'){
        delphrase();
    }else if ($operation == 'deletenpc'){
        deletenpc();
    }else if ($operation == 'createnpc'){
        createnpc();
    }else if ($operation == 'createsimplenpc'){
        createsimplenpc();
    }else if ($operation == 'viewtrainer'){
        viewtrainer();
    }else if ($operation == 'edittrainer'){
        edittrainer();
    }else if ($operation == 'viewmerchant'){
        viewmerchant();
    }else if ($operation == 'editmerchant'){
        editmerchant();
    }else if ($operation == 'findtrigger'){
        findtrigger();
    }else if ($operation == 'savecombatnpc'){
        savecombatnpc();        
    }else{ 
        // manage another operation here
        echo "Operation $operation not supported.";
    }

    if ($type == 'npc'){
        echo "<br><br><A HREF=\"index.php?page=viewnpc&id=$id\" target=_top>Reload the tree</A>";
        echo "<br><br><A HREF=\"index.php?page=listnpcs\" target=_top>Go back to list of available NPCs</A>";
    }else if ($type == 'ka'){
        echo "<br><br><A HREF=\"index.php?page=viewka&area=$area\" target=_top>Reload the tree</A>";
        echo "<br><br><A HREF=\"index.php?page=listkas\" target=_top>Go back to list of available KAs</A>";
    }
    echo'</body>';
}

?>
