<?
function listspawnrules($selectedrule){
        include('util.php');


	?>


<SCRIPT language=javascript>

function confirmDelete()
{
    return confirm("Are you sure you want to delete this spawn rule?");
}

</SCRIPT>

<?PHP

    checkAccess('npc', '', 'read');

    echo '  <TABLE BORDER=1>';
    echo '  <TR><TH> Rule </TH> ';
    echo '  <TH> Ranges </TH> <TH>NPCs</TH></TR>';

    // check if one loot has been selected
    if ($selectedrule)
	  $query = "select * from npc_spawn_rules where id=".$selectedrule." order by id";
	else
	  $query = "select * from npc_spawn_rules order by id";

	$rule_result = mysql_query2($query);
	while ($rule = mysql_fetch_array($rule_result, MYSQL_NUM)){
	   echo "<TR><TD>(<A HREF=index.php?page=listspawnrules&selectedrule=$rule[0]>$rule[0]</A>) <br>$rule[12]</TD>";
	   echo '<TD>';

		// print main spawn fields
		echo '<FORM ACTION=index.php?page=spawnrules_actions&operation=updaterule METHOD=POST>';
		echo "<INPUT TYPE=hidden NAME=rule_id VALUE=\"$rule[0]\">";		
		echo "<table width=\"100%\">\n";
		echo "<tr><th>Min Spawn Time</th><td><input name=min_spawn_time value=\"{$rule[1]}\" size=5></td>\n";
		echo "<th>Max Spawn Time</th><td><input name=max_spawn_time value=\"{$rule[2]}\" size=5></td></tr>\n";
		echo "<tr><th>Substitute Spawn Odds</th><td><input name=substitute_spawn_odds value=\"{$rule[3]}\" size=5></td>\n";
		echo "<th>Substitute NPC</th><td><input name=substitute_player value=\"{$rule[4]}\" size=5></td></tr>\n";
		echo "<tr><th>Fixed X</th><td><input name=fixed_spawn_x value=\"{$rule[5]}\" size=5></td>\n";
		echo "<th>Fixed Y</th><td><input name=fixed_spawn_y value=\"{$rule[6]}\" size=5></td></tr>\n";
		echo "<tr><th>Fixed Z</th><td><input name=fixed_spawn_z value=\"{$rule[7]}\" size=5></td>\n";
		echo "<th>Fixed Rot</th><td><input name=fixed_spawn_rot value=\"{$rule[8]}\" size=5></td></tr>\n";
		echo "<tr><th>Fixed Sector</th><td><input name=fixed_spawn_sector value=\"{$rule[9]}\" size=15></td>\n";
		echo "<th>Loot Category ID</th><td><input name=loot_category_id value=\"{$rule[10]}\" size=5></td></tr>\n";
		echo "<tr><th>Dead Time</th><td><input name=dead_remain_time value=\"{$rule[11]}\" size=5></td>\n";
		echo "<td></td><td></td></tr>\n";
		echo "</table>\n";
		echo '<INPUT TYPE=SUBMIT NAME=submit VALUE=Update>';
		echo "</form>\n";

	   $query = "select r.id,s.name, x1, y1, z1, x2, y2, z2, range_type_code, s.name from npc_spawn_ranges as r ,sectors as s where r.npc_spawn_rule_id='$rule[0]' and s.id=cstr_id_spawn_sector";
	   $result = mysql_query2($query);

       // prints items table
       echo "<h3>Additional Spawn Ranges</h3>\n";
	   echo '  <TABLE BORDER=4>';
       echo '  <TH> Id </TH> ';
       echo '  <TH> Sector </TH> ';
       echo '  <TH> X1 </TH>';
       echo '  <TH> Y1 </TH>';
       echo '  <TH> Z1 </TH>';
       echo '  <TH> X2 </TH>';
       echo '  <TH> Y2 </TH>';
       echo '  <TH> Z2 </TH>';
       echo '  <TH> Type </TH>';

	   while ($line = mysql_fetch_array($result, MYSQL_NUM)){
			echo '<TR>';
			echo '<FORM ACTION=index.php?page=spawnrules_actions&operation=update METHOD=POST>';
			echo "<TD><INPUT TYPE=hidden NAME=rule_id VALUE=\"$rule[0]\"><INPUT TYPE=hidden NAME=range_id VALUE=\"$line[0]\">$line[0]</TD>";
			echo "<TD>$line[1]</TD>";
			echo "<TD><INPUT TYPE=text size=5 name=x1 VAlUE=\"$line[2]\"></TD>";
			echo "<TD><INPUT TYPE=text size=5 name=y1 VAlUE=\"$line[3]\"></TD>";
			echo "<TD><INPUT TYPE=text size=5 name=z1 VAlUE=\"$line[4]\"></TD>";
			echo "<TD><INPUT TYPE=text size=5 name=x2 VAlUE=\"$line[5]\"></TD>";
			echo "<TD><INPUT TYPE=text size=5 name=y2 VAlUE=\"$line[6]\"></TD>";
			echo "<TD><INPUT TYPE=text size=5 name=z2 VAlUE=\"$line[7]\"></TD>";
			echo "<TD><INPUT TYPE=text size=5 name=range_type_code VAlUE=\"$line[8]\"></TD>";
			echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Update></FORM>';
			echo '</TD><TD>';
			echo "<FORM ACTION=index.php?page=spawnrules_actions&operation=remove METHOD=POST onsubmit=\"return confirmDelete()\">";
			echo "<INPUT TYPE=hidden NAME=rule_id VALUE=\"$rule[0]\"><INPUT TYPE=hidden NAME=range_id VALUE=\"$line[0]\">";
			echo '<INPUT TYPE=SUBMIT NAME=submit VALUE=Remove>';
			echo '</FORM></TD></TR>';
	   }
	   // add new item row
	   echo '<TR>';
	   echo '<FORM ACTION=index.php?page=spawnrules_actions&operation=add METHOD=POST>';
	   echo "<TD><INPUT TYPE=hidden NAME=rule_id VALUE=\"$rule[0]\"></TD>";
	   echo '<TD>';
      SelectSectors(-1, 'cstr_id_spawn_sector');
	   echo '</TD>';
		echo "<TD><INPUT TYPE=text size=5 name=x1 ></TD>";
		echo "<TD><INPUT TYPE=text size=5 name=y1 ></TD>";
		echo "<TD><INPUT TYPE=text size=5 name=z1 ></TD>";
		echo "<TD><INPUT TYPE=text size=5 name=x2 ></TD>";
		echo "<TD><INPUT TYPE=text size=5 name=y2 ></TD>";
		echo "<TD><INPUT TYPE=text size=5 name=z2 ></TD>";
		echo "<TD><select name=range_type_code><option value=A>Area</option><option value=L>Line</option></select></TD>";
	   echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Add>';
	   echo '</FORM></TD>';
	   echo '</TABLE>';

       // npc table
	   echo '<TD><TABLE>';
	   $npc_query = "select c.id,c.name,s.name from characters c, sectors s where c.loc_sector_id=s.id and npc_spawn_rule=\"$rule[0]\"";
	   $npc_result = mysql_query2($npc_query);
	   while ($npc = mysql_fetch_array($npc_result, MYSQL_NUM)) {
          echo "<TR><TD>";
          echo '<FORM ACTION=index.php?page=spawnrules_actions&operation=remove_npc METHOD=POST>';
          echo "<INPUT TYPE=hidden NAME=rule_id VALUE=\"$rule[0]\">";
          echo "<INPUT TYPE=hidden NAME=npc_id VALUE=\"$npc[0]\">";
          echo "$npc[1] ($npc[0] - $npc[2])";
          echo '</TD><TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Remove>';
          echo "</FORM>";
          echo "</TD></TR>";
       }
      echo "<TR><TD>";
      echo '<FORM ACTION=index.php?page=spawnrules_actions&operation=add_npc METHOD=POST>';
      echo "<INPUT TYPE=hidden NAME=rule_id VALUE=\"$rule[0]\">";
      SelectNPCs(-1,npc_id,"vulnerable");
      echo '</TD><TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Add>';
      echo "</FORM>";
      echo "</TD></TR>";
      echo '</TABLE></TD>';
      echo '</TR>';
      echo '<TR><TD colspan=3 bgcolor=darkblue></TD><TR>';
       }
       
      // add new full loot rule
      echo '<TR><FORM ACTION=index.php?page=spawnrules_actions&operation=create METHOD=POST>';
      echo '<TD><INPUT TYPE=text name=rule_name size=15></TD><TD>';
      echo '  <TABLE BORDER=1>';
       echo '  <TR><TH> Sector </TH> ';
       echo '  <TH> X1 </TH>';
       echo '  <TH> Y1 </TH>';
       echo '  <TH> Z1 </TH>';
       echo '  <TH> X2 </TH>';
       echo '  <TH> Y2 </TH>';
       echo '  <TH> Z2 </TH>';
       echo '  <TH> Type </TH>';
      echo '<TR>';
      echo '<TD>';
      SelectSectors(-1,'cstr_id_spawn_sector');
      echo '</TD>';
		echo "<TD><INPUT TYPE=text size=5 name=x1 ></TD>";
		echo "<TD><INPUT TYPE=text size=5 name=y1 ></TD>";
		echo "<TD><INPUT TYPE=text size=5 name=z1 ></TD>";
		echo "<TD><INPUT TYPE=text size=5 name=x2 ></TD>";
		echo "<TD><INPUT TYPE=text size=5 name=y2 ></TD>";
		echo "<TD><INPUT TYPE=text size=5 name=z2 ></TD>";
		echo "<TD><select name=range_type_code><option value=A>Area</option><option value=L>Line</option></select></TD>";
      echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=create>';
      echo '</FORM>';
      echo '</TABLE></TD>';

	echo '<TD></TD>';

	echo '</TR></TABLE>';

	echo '<br><br>';
}

?>
  
