<?PHP

function append_sql($field,$sql)
{
	return $sql . ", $field=\"" . $_POST[$field] . "\"";
}

function spell_actions(){


    checkAccess('main', '', 'read');

	include 'util.php';
	if ($_POST['operation'] == ''){
		$operation = 'Edit';
	}else $operation = $_POST['operation'];

	if ($_POST['id'] == ''){
		$id = $_GET['id'];
	}else $id = $_POST['id'];

	if ($operation == ''){
		echo 'You cant view this page';
	}else{
		echo '<A HREF="index.php?page=listspells">Back</A>';
		if ($operation == 'Edit'){
			$query = "select s.id, s.name, w.name, s.realm, ";
			$query .= "s.caster_effect, s.target_effect, s.offensive, ";
			$query .= "s.progression_event, s.saved_progression_event, s.saving_throw, ";
			$query .= "s.saving_throw_value, s.max_power, s.target_type, s.image_name, s.spell_description, ";
                        $query .= "s.cstr_npc_spell_category ";
			$query .= "FROM spells as s, ways as w";
			$query .= " where s.way_id=w.id and s.id=$id";

			$result = mysql_query2($query);
			$line = mysql_fetch_array($result, MYSQL_NUM);

			echo "<p class=\"yellowtitlebig\">Edit spell " . $line[1] . "</p>";

			echo '<TABLE BORDER="1">';
			printf("<FORM ACTION=index.php?page=spell_actions METHOD=POST>");
			printf("<INPUT TYPE=HIDDEN NAME=id VALUE=%d>", $line[0]); 
			// Name
			printf('<TR><TD>Name:</TD>');
			printf("<TD><INPUT TYPE=TEXT NAME=name VALUE=\"%s\"></TD></TR>", $line[1]); 
			// Way
			echo'<TR><TD>Way: </TD>';
			echo"<TD><SELECT name=way>";
			$query_way = 'select id, name from ways';
			$result = mysql_query2($query_way);
			while ($way = mysql_fetch_array($result, MYSQL_NUM)){
				if ($way[1] == $line[2]){
					printf("<OPTION selected>%s</OPTION>", $way[1]);
				}else{
					printf("<OPTION>%s</OPTION>", $way[1]);
				}
			}
			echo'</SELECT></TD></TR>'; 
			// Realm
			echo'<TR><TD>Realm: </TD>';
			echo'<TD><SELECT name=realm>';
			for ($realm = 1; $realm < 12; $realm++){
				if ($realm == $line[3]){
					printf("<OPTION selected>%s</OPTION>", $realm);
				}else{
					printf("<OPTION>%s</OPTION>", $realm);
				}
			}
			echo'</SELECT></TD></TR>'; 

			// Caster effect
			echo'<TR><TD>Caster effect: </TD>';
			printf("<TD><INPUT TYPE=TEXT NAME=caster_effect VALUE=\"%s\"> (played during weaving)</TD></TR>", $line[4]); 
			// Target effect
			echo'<TR><TD>Target effect: </TD>';
			printf("<TD><INPUT TYPE=TEXT NAME=target_effect VALUE=\"%s\"> (played at end of weaving)</TD></TR>", $line[5]); 
			// Offensive
			echo'<TR><TD>Offensive: </TD>';
			printf("<TD><INPUT TYPE=CHECKBOX NAME=offensive");
			if($line[6] == "1")
				print(" CHECKED");
			
			print("></TD></TR>");
										
			// Progression event
			echo'<TR><TD>Progression event: </TD><TD>';
			SelectCastProgressionEvent($line[7], "progression_event");
			echo'</TD></TR>'; 
			// Saved progression event
			echo'<TR><TD>Saved progression event: </TD><TD>';
			SelectCastProgressionEvent($line[8], "saved_progression_event");
			echo'</TD></TR>'; 
			// Saving throw
			echo'<TR><TD>Saving throw: <br>STR,AGI,END,INT,WIL,CHA <br>or a skill with proper case, e.g. Red Way</TD><TD>';
			printf("<INPUT TYPE=TEXT NAME=saving_throw VALUE=\"%s\"> (0 if no ST allowed)", $line[9]); 
			echo'</TD></TR>';
			// Saving throw value
			echo'<TR><TD>Saving throw value: </TD><TD>';
			printf("<INPUT TYPE=TEXT NAME=saving_throw_value VALUE=\"%s\"> (-1 if no ST allowed)", $line[10]); 
			echo'</TD></TR>'; 			
			// Max power
			echo'<TR><TD>Max power: </TD><TD>';
			printf("<INPUT TYPE=TEXT NAME=max_power VALUE=\"%s\">", $line[11]); 
			echo'</TD></TR>'; 
			
			// Target type
			echo'<TR><TD>Target type: ';
                        echo' 	TARGET_NONE = 0x01, /* Also Area */ <br>
 	TARGET_NPC      = 2, <br>
	TARGET_ITEM     = 4, <br>
	TARGET_SELF     = 8, <br>
	TARGET_FRIEND   = 16, <br>
	TARGET_FOE      = 32, <br>
	TARGET_DEAD   = 64, <br>
	TARGET_GM   = 128, <br>
	TARGET_PVP = 256 </TD>';
			printf("<TD><INPUT TYPE=TEXT NAME=target_type VALUE=\"%s\"></TD></TR>", $line[12]); 
			// Image name
			echo'<TR><TD>Image: </TD>';
			printf("<TD><INPUT TYPE=TEXT NAME=image_name VALUE=\"%s\"></TD></TR>", $line[13]); 
			// Description
			echo'<TR><TD>Description: </TD>';
			printf("<TD><TEXTAREA NAME=spell_description rows=\"10\" cols=\"40\">%s</TEXTAREA></TD></TR>", $line[14]);
			echo'<TR><TD>NPC Spell category: </TD><TD>';
                        SelectCommonString($line[15],"cstr_npc_spell_category");
                        echo "<TD></TR>";

			printf("<TR><TD></TD><TD><INPUT TYPE=SUBMIT NAME=operation VALUE=\"Save\"><INPUT TYPE=SUBMIT NAME=operation VALUE=\"Delete\"></TD>");
			echo'</FORM>';
			
			// Glyphs
			printf("<FORM ACTION=index.php?page=spell_actions METHOD=POST>");
			printf("<INPUT TYPE=HIDDEN NAME=id VALUE=%d>", $line[0]); 
			printf("<INPUT TYPE=HIDDEN NAME=glyphs VALUE=1>");
			echo "<TR><TD>";
			
			// Loop the glyphs
			$query = "SELECT * FROM spell_glyphs WHERE spell_id = '".$line[0]."' LIMIT 4";
			$result = mysql_query2($query);
			
			for($i = 0; $i < 4; $i++)			
			{
				if(!$line = mysql_fetch_array($result, MYSQL_ASSOC))
					$item = "-1";
				else
					$item = $line['item_id'];
					
			    echo "Slot ".($i+1).":";
			    echo "</TD><TD>";
				SelectGlyphs($item,"glyph".$i);
				echo "</TD></TR>";
				echo "<TR><TD>";
			}
					
			echo "</TD></TR>";
			printf("<TR><TD></TD><TD><INPUT TYPE=SUBMIT NAME=operation VALUE=\"Save glyphs\"></TD>");
			echo '</TABLE>';
		}else if ($operation == 'Save'){
			$query_save = 'update spells set ';
			$query_save = $query_save . " name=\"" . $_POST['name'] . "\"";
			$query_save = $query_save . ", realm=\"" . $_POST['realm'] . "\"";
			$query_save = $query_save . ", way_id=\"" . WayToID($_POST['way']) . "\"";
			$query_save = append_sql("caster_effect",$query_save);
			$query_save = append_sql("target_effect",$query_save);
			if ($_POST['offensive']=="on")
			  $query_save = $query_save . ", offensive=1 ";
			else
			  $query_save = $query_save . ", offensive=0 ";
			$query_save = append_sql("progression_event",$query_save);
			$query_save = append_sql("saved_progression_event",$query_save);
			$query_save = append_sql("saving_throw",$query_save);
			$query_save = append_sql("saving_throw_value",$query_save);
			$query_save = append_sql("max_power",$query_save);
			$query_save = append_sql("target_type",$query_save);
			$query_save = append_sql("image_name",$query_save);
			$query_save = append_sql("spell_description",$query_save);
			$query_save = append_sql("cstr_npc_spell_category",$query_save);
			$query_save = $query_save . " where id=" . $id;
			if ($debug) echo $query_save;

			$result = myquery($query_save);

			echo '<P>Spell saved</P>';
		}else if ($operation == 'Save glyphs')
		{
		    // Check for duplicates
		    $dup_found = false;
			for($i = 0; $i < 4; $i++)
		    {
		     	   for($z = 0; $z < 4; $z++)
		     	   {
		     	        if($_POST['glyph'.$i] == $_POST['glyph'.$z]
						 	&& $_POST['glyph'.$i] != "0"
							&& $z != $i)
							$dup_found = true;		     	   
		     	   }
		    }
		    
		    if($dup_found)
		    {
		    	echo "<p><font color='red'>Duplicates found in glyphs</font></p>";
		    }
		    else
			{
    		    
    		 	// Remove old glyphs
    		    $result = myquery("DELETE FROM spell_glyphs WHERE spell_id='".$id."'");
    		    
    		    // Insert new
    		    for($i = 0; $i < 4; $i++)
    		    {
    		     	$item = $_POST['glyph' . $i];
    		     	
    		     	if( $item != "0")
    		    	{				    	 	
    		    	    $query="INSERT INTO spell_glyphs (`spell_id`,`item_id`,`position`) VALUES ('".
    								   $id."','$item','$i')"; 
                       myquery($query);
                    }
    			}
    			echo '<P>Spell glyphs updated</P>';
    		}
		}
		else if ($operation == 'Delete' && $_POST['confirmed'] == "yes")
		{ 
			// delete responses
			$query_name = 'select name from spells where id = ' . $id;
			$result = mysql_query2($query_name);
			$line = mysql_fetch_array($result, MYSQL_NUM);
			$name = $line[0];
			$query_delete = 'delete from spells where id= ' . $id;
			$result = mysql_query2($query_delete);
			$query_delete = 'delete from spell_glyphs where spell_id= ' . $id;
			$result = mysql_query2($query_delete);
			if ($name != ''){
				echo"<p>Deleted spell $name</p>";
			}else{
				echo'<p>Failed to delete spell</p>';
			}
		}
		else if($operation == 'Delete')
		{
			$result = mysql_query2("SELECT name FROM spells WHERE id = '$id'");
			$line = mysql_fetch_array($result,MYSQL_NUM);
			
			printf("<p>Please confirm the deletion of the spell ".$line[0]."</p>");
			printf("<FORM ACTION=index.php?page=spell_actions METHOD=POST>");
			printf("<INPUT TYPE=HIDDEN NAME=id VALUE=%d>", $id); 
			printf("<INPUT TYPE=HIDDEN NAME=operation VALUE=Delete>"); 
			printf("<INPUT TYPE=HIDDEN NAME=confirmed VALUE=yes>"); 
			printf("<INPUT TYPE=SUBMIT VALUE=\"Delete\">");
			printf("</FORM>");
		}
		else
		{
			echo "<p>Error: Unhandled operation $operation</p>";
		}

		echo '<HR>';
		echo '<A HREF="index.php?page=listspells">Back</A>';
	}
}

?>		
