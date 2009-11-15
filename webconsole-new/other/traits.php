<?php

function SelectLocationT($current_location,$select_name)
{
    $slocation = '';
    $slocation .= sprintf('<SELECT name="%s">', $select_name);
    $location="EYE_COLOR"; if ($current_location == $location){$selected="selected";}else{$selected="";}
    $slocation .= sprintf("<OPTION %s value=\"%s\">%s</OPTION>",$selected,$location,$location);
    $location="HAIR_COLOR"; if ($current_location == $location){$selected="selected";}else{$selected="";}
    $slocation .= sprintf("<OPTION %s value=\"%s\">%s</OPTION>",$selected,$location,$location);
    $location="HAIR_STYLE"; if ($current_location == $location){$selected="selected";}else{$selected="";}
    $slocation .= sprintf("<OPTION %s value=\"%s\">%s</OPTION>",$selected,$location,$location);
    $location="BEARD_STYLE"; if ($current_location == $location){$selected="selected";}else{$selected="";}
    $slocation .= sprintf("<OPTION %s value=\"%s\">%s</OPTION>",$selected,$location,$location);
    $location="SKIN_TONE"; if ($current_location == $location){$selected="selected";}else{$selected="";}
    $slocation .= sprintf("<OPTION %s value=\"%s\">%s</OPTION>",$selected,$location,$location);
    $slocation .= sprintf("</SELECT>");
    return $slocation;
}

function SelectOnlyNPC($current_location,$select_name)
{
    $only = '';
    $only .= sprintf('<SELECT name="%s">', $select_name);
    $location='0'; if ($current_location == $location){$selected='selected="selected"';}else{$selected='';}
    $only .= sprintf('<OPTION %s value="%s">NO</OPTION>',$selected,$location);
    $location='1'; if ($current_location == $location){$selected='selected="selected"';}else{$selected='';}
    $only .= sprintf('<OPTION %s value="%s">YES</OPTION>',$selected,$location);
    $only .= sprintf('</SELECT>');
    return $only;
}



/******************************************************************************
Show the traits for a particluar race
 race_id  The race id from the race_info table we want to see the traits for.
******************************************************************************/
function show_traits( $race_id )
{
    checkAccess('main', '', 'read');
   
    $query = "SELECT  name from  race_info where id=$race_id";
    $result = mysql_query2($query);

    $line = mysql_fetch_array($result, MYSQL_NUM);

    echo "<H1>" . $line[0] . "</H1>";
    echo "<A HREF='#hair_colours'>Hair Colours</A>";

    ShowFaces($race_id);
    ShowHairColours($race_id);
}



/******************************************************************************
Shows the hair colour traits for a particular race.
 ******************************************************************************/
function ShowHairColours($race_id)
{
    $query = "SELECT * FROM traits WHERE location='HAIR_COLOR' AND race_id=$race_id";
    $result = mysql_query2($query);

    echo "<A NAME='hair_colours'/></a>";
    echo "<H2> Hair Colours</H2>";
   
    echo "<TABLE BORDER=1>";
    echo "<TH>Name</TH>";
    echo "<TH>NPC Only</TH>";
    echo "<TH>Shader</TH>";
    echo "<TH>Delete</TH>";
    echo "<TH>Update</TH>";

    while ( $line = mysql_fetch_array($result, MYSQL_BOTH) )
    {
        echo "<FORM ACTION='index.php?do=handletrait' METHOD='post'>";
        echo "<INPUT TYPE=HIDDEN NAME=trait_id VALUE=$line[0]>";
        echo "<INPUT TYPE=HIDDEN NAME=race_id VALUE=$race_id>";
        echo "<INPUT TYPE=HIDDEN NAME=action VALUE=update>";
        echo "<INPUT TYPE=HIDDEN NAME=area VALUE=HAIR_COLOR>";

        echo "<TR>";
        echo "<TD><INPUT TYPE=TEXT NAME=trait_name VALUE='" . $line["name"] . "'></TD>";
        echo "<TD>".SelectOnlyNPC($line[3],"only_npc")."</TD>";
        echo "<TD><INPUT TYPE=TEXT VALUE='" . $line["shader"]. "' NAME=shader></TD>";
        echo "<TD><INPUT TYPE=CHECKBOX NAME=delete_box></TD>";
        echo "<TD><INPUT TYPE=SUBMIT VALUE=Update></TD>";
        echo "</TR>";
        echo "</FORM>";
    }

    echo "<FORM ACTION='index.php?do=handletrait' METHOD='post'>";
    echo "<INPUT TYPE=HIDDEN NAME=race_id VALUE=$race_id>";
    echo "<INPUT TYPE=HIDDEN NAME=action VALUE=new>";
    echo "<INPUT TYPE=HIDDEN NAME=area VALUE=HAIR_COLOR>";

    echo "<TR>";
    echo "<TD><INPUT TYPE=TEXT NAME=trait_name></TD>";
    echo "<TD>".SelectOnlyNPC("0","only_npc")."</TD>";
    echo "<TD><INPUT TYPE=TEXT VALUE='" . $line["shader"]. "' NAME=shader></TD>";
    echo "<TD>-</TD>";
    echo "<TD><INPUT TYPE=SUBMIT VALUE=Add></TD>";
    echo "</TR>";
    echo "</FORM>";
 
    
    echo "</TABLE>";
}



/******************************************************************************
Shows the face traits for a particular race.
 ******************************************************************************/
function ShowFaces($race_id)
{
    $query = "SELECT * FROM traits WHERE location='FACE' AND race_id=$race_id";
    $result = mysql_query2($query);

    echo "<H2> Face Variations</H2>";
   
    echo "<TABLE BORDER=1>";
    echo "<TH>Name</TH>";
    echo "<TH>NPC Only</TH>";
    echo "<TH>Material</TH>";
    echo "<TH>Texture</TH>";
    echo "<TH>Delete</TH>";
    echo "<TH>Update</TH>";


    while ( $line = mysql_fetch_array($result, MYSQL_BOTH) )
    {
        echo "<FORM ACTION='index.php?do=handletrait' METHOD='post'>";
        echo "<INPUT TYPE=HIDDEN NAME=trait_id VALUE=$line[0]>";
        echo "<INPUT TYPE=HIDDEN NAME=race_id VALUE=$race_id>";
        echo "<INPUT TYPE=HIDDEN NAME=action VALUE=update>";
        echo "<INPUT TYPE=HIDDEN NAME=area VALUE=FACE>";

        echo '<TR>';
        echo '<TD><INPUT TYPE="TEXT" NAME="trait_name" VALUE="'.$line['name'].'" /></TD>';
        echo '<TD>'.SelectOnlyNPC($line[3],'only_npc').'</TD>';
        echo '<TD> <input type="text" name="cstr_material" value="'.$line[7].'"/>';
        echo '<TD> <input type="text" name="cstr_texture" value="'.$line[8].'"/>';
        echo '<TD><INPUT TYPE="CHECKBOX" NAME="delete_box"></TD>';
        echo '<TD><INPUT TYPE="SUBMIT" VALUE="Update"></TD>';
        echo '</TR>';
        echo '</FORM>';
    }

    echo "<FORM ACTION='index.php?do=handletrait' METHOD='post'>";
    echo "<INPUT TYPE=HIDDEN NAME=trait_id VALUE=$line[0]>";
    echo "<INPUT TYPE=HIDDEN NAME=race_id VALUE=$race_id>";
    echo "<INPUT TYPE=HIDDEN NAME=action VALUE=new>";
    echo "<INPUT TYPE=HIDDEN NAME=area VALUE=FACE>";


    echo '<TR>';
    echo '<TD><INPUT TYPE="TEXT" NAME="trait_name"></TD>';
    echo '<TD>'.SelectOnlyNPC('0','only_npc').'</TD>';
    echo '<TD><input type="text" name="cstr_material" />';
    echo '<TD><input type="text" name="cstr_texture" />';
    echo '<TD>-</TD>';
    echo '<TD><INPUT TYPE="SUBMIT" VALUE="Add"></TD>';
    echo '</TR>';
    echo '</FORM>';
 
    
    echo '</TABLE>';
}


/*****************************************************************************
Shows a list of the different races so you can pick one.
 *****************************************************************************/
function show_races()
{
    checkAccess('main', '', 'read');

    if (isset($_GET['function']) && $_GET['function'] == 'list' )
    {
        show_traits( $_GET['race_id'] );  
    }
    else
    {
    $query = "SELECT id, name, sex from race_info where id < 12";
    $result = mysql_query2($query);
   
    echo "<P>Select the race that you want to change the traits on";
    echo "<CENTER>";
    echo "<TABLE>";
    echo "<TR><TD>";
    echo "<TABLE BORDER=1 CELLPADDING=5 CELLSPACING=0>";
    echo "<TH>ID</TH><TH>Race</TH><TH>Gender</TH>";
    
    while ($line = mysql_fetch_array($result, MYSQL_NUM))
    {
        echo "<TR><TD>" . $line[0] . "</TD><TD><A HREF=index.php?do=showraces&function=list&race_id=" . $line[0] . ">" . $line[1] . "</A></TD><TD>" . $line[2] . "</TD></TR>";

    }

    echo "</TABLE>";
    echo "</TD><TD VALIGN=TOP>";
    echo "<TABLE BORDER=1 CELLPADDING=5 CELLSPACING=0>";
    echo "<TH>ID</TH><TH>Race</TH><TH>Gender</TH>";
    
    $query = "SELECT id, name, sex from race_info where id >= 12 and id <= 22";
    $result = mysql_query2($query);


    while ($line = mysql_fetch_array($result, MYSQL_NUM))
    {
        echo "<TR><TD>" . $line[0] . "</TD><TD><A HREF=index.php?do=showraces&function=list&race_id=" . $line[0] . ">" . $line[1] . "</A></TD><TD>" . $line[2] . "</TD></TR>";

    }

    echo "</TABLE>";

    echo "</TD></TR></TABLE>";
    echo "</CENTER>";
    }
}

function list_traits()
{

    if (!checkaccess('other', 'read'))
    {
        echo '<p class="error">You are not authorised to use these functions.</p>';
        return;
    }

	$query = "select id, next_trait, race_id, only_npc, location, name, cstr_mesh, cstr_material, cstr_texture from traits";
	$result = mysql_query2($query);

	echo '  <TABLE BORDER=1>';
	echo '  <TH> ID </TH> ';
	echo '  <TH> Next </TH> ';
	echo '  <TH> Race/Location - Only NPC / Name </TH> ';
	echo '  <TH> Mesh/Material/Texture </TH> ';
	echo '  <TH> Functions </TH> ';
    $races = PrepSelect('races');
    
	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		echo '<TR>';
		echo '<FORM ACTION=index.php?do=trait_actions&operation=update METHOD=POST>';
		echo '<TD><INPUT TYPE="hidden" NAME="id" VALUE="'.$line[0].'" />'.$line[0].'</TD>';
		echo '<TD><INPUT SIZE="5" TYPE="text" NAME="next_trait" VALUE="'.$line[1].'"></TD>';
		echo '<TD><TABLE><TR><TD>'.DrawSelectBox('races', $races, 'race_id', $line[2], false).'</TD>';
		echo '<TD>'.SelectOnlyNPC($line[3], 'only_npc').'</TD></TR>';
		echo '<TR><TD>'.SelectLocationT($line[4],'location').'</TD>';
		echo '<TD><INPUT TYPE="text" NAME="name" VALUE="'.$line[5].'" /></TD></TR></TABLE></TD>';
		echo '<TD><input type="text" name="cstr_mesh" value="'.$line[6].'" /><BR />';
		echo '<input type="text" name="cstr_material" value="'.$line[7].'" /><BR />';
		echo '<input type="text" name="cstr_texture" value="'.$line[8].'" /></TD>';
		echo '<TD><TABLE><TR><TD><INPUT TYPE="SUBMIT" NAME="submit" VALUE="Update" /></FORM></TD>';
		echo '<TD><FORM ACTION="index.php?do=trait_actions&operation=delete" METHOD="POST">';
		echo '<INPUT TYPE="hidden" NAME="id" VALUE="'.$line[0].'" />';
		echo '<INPUT TYPE="SUBMIT" NAME="submit" VALUE="Delete" /></FORM></TD></TR></TABLE>';
		echo '</TD></TR>';
	}
	echo '<TR>';
	echo '<FORM ACTION="index.php?do=trait_actions&operation=add" METHOD="POST">';
	echo '<TD></TD>';
	echo '<TD><INPUT SIZE="5" TYPE="text" NAME="next_trait" /></TD>';
	echo '<TD><TABLE><TR><TD>'.DrawSelectBox('races', $races, 'race_id', '', false).'</TD>';
	echo '<TD>'.SelectOnlyNPC('0','only_npc').'</TD></TR>';
	echo '<TR><TD>'.SelectLocationT('EYE_COLOR','location').'</TD>';
	echo '<TD><INPUT TYPE="text" NAME="name" /></TD></TR></TABLE></TD>';
	echo '<TD><input type="text" name="cstr_mesh" /><BR />';
	echo '<input type="text" name="cstr_material" /><BR />';
	echo '<input type="text" name="cstr_texture" /></TD>';
	echo '<TD><INPUT TYPE="SUBMIT" NAME="submit" VALUE="Add"></FORM>';

	echo '</FORM></TD></TR>';
	echo '</TABLE><br><br>';

	echo '<br><br>';
}

function handle_trait() {
    if (!checkaccess('other', 'edit'))
    {
        echo '<p class="error">You are not authorised to use these functions.</p>';
        return;
    }
    $race_id  = $_POST['race_id'];
    $trait_id = (isset($_POST['trait_id']) ? $_POST['trait_id'] : '-1');
    $name     = $_POST['trait_name'];
    $only_npc = $_POST['only_npc'];
    $mat      = (isset($_POST['cstr_material']) ? $_POST['cstr_material'] : '');
    $delete   = (isset($_POST['delete_box']) ? 'on' : '');
    $action   = $_POST['action'];
    $cstr_texture = (isset($_POST['cstr_texture']) ? $_POST['cstr_texture'] : '');
    $area     = $_POST['area'];
    $shader   = (isset($_POST['shader']) ? $_POST['shader'] : '');

    //echo "$name $trait $only_npc $mat $delete";

    if ( $delete == 'on' )
    {
        if (!checkaccess('other', 'delete'))
        {
            echo '<p class="error">You are not authorised to use these functions.</p>';
            return;
        }
        $query = "DELETE FROM traits WHERE id='$trait_id'";
        mysql_query2($query);    
    }

    else if ( $action == "update" )
    {
       
        switch ( $area )
        {
            case "FACE":
            {
                $query = "UPDATE traits SET only_npc='$only_npc', name='$name', cstr_material='$mat', cstr_texture='$cstr_texture' WHERE id='$trait_id'";
                break;
            }

            case "HAIR_COLOR":
            {
                $query = "UPDATE traits SET only_npc='$only_npc', name='$name', shader='$shader' WHERE id='$trait_id'";
                break; 
            }
        }

        mysql_query2($query);    
    }

    else if ( $action == "new" )
    {
        if (!checkaccess('other', 'create'))
        {
            echo '<p class="error">You are not authorised to use these functions.</p>';
            return;
        }

        
        $cstr_mesh = 'Head';



        switch ( $area )
        {
            case "FACE":
            {
                $query = "INSERT INTO  traits(next_trait, 
                                      race_id,
                                      only_npc,
                                      location, 
                                      name, 
                                      cstr_mesh, 
                                      cstr_material, 
                                      cstr_texture) 
                                    VALUES(  '-1', 
                                             '$race_id', 
                                             '$only_npc', 
                                             'FACE',
                                              '$name',
                                              '$cstr_mesh', 
                                              '$mat', 
                                              '$cstr_texture')";
                break;
            }

            case "HAIR_COLOR":
            {
               $query = "INSERT INTO  traits(next_trait, 
                                      race_id,
                                      only_npc,
                                      location, 
                                      name, 
                                      shader ) 
                                    VALUES(  '-1', 
                                             '$race_id', 
                                             '$only_npc', 
                                             'HAIR_COLOR',
                                              '$name',
                                              '$shader')";
      
                break;
            }

        }
        mysql_query2($query);
    }
    unset($_POST);
    show_traits($race_id);
}

function trait_actions()
{
    // gets operation to perform
    $operation = $_GET['operation'];

	/**
	 * update script
	 */
    if ($operation == 'update')
    {
        $id = $_POST['id'];
        $next_trait = $_POST['next_trait'];
        $race_id = $_POST['race_id'];
        $location = $_POST['location'];
        $name = $_POST['name'];
        $only_npc = $_POST['only_npc'];
        $cstr_mesh = $_POST['cstr_mesh'];
        $cstr_material = $_POST['cstr_material'];
        $cstr_texture = $_POST['cstr_texture'];

        // insert script
        $query = "update traits set next_trait='$next_trait', race_id='$race_id',only_npc='$only_npc',location='$location',name='$name' ,cstr_mesh='$cstr_mesh' ,cstr_material='$cstr_material' ,cstr_texture='$cstr_texture' where id='$id'";
        $result = mysql_query2($query); 
        // redirect

    }
    else if ($operation == 'add')
    {
        $next_trait = ($_POST['next_trait'] == '' ? -1 : $_POST['next_trait']);
        $race_id = $_POST['race_id'];
        $only_npc = $_POST['only_npc'];
        $location = $_POST['location'];
        $name = $_POST['name'];
        $cstr_mesh = $_POST['cstr_mesh'];
        $cstr_material = $_POST['cstr_material'];
        $cstr_texture = $_POST['cstr_texture'];

        // insert script
        $query = "insert into traits (next_trait,race_id,only_npc,location,name,cstr_mesh,cstr_material,cstr_texture) values ('$next_trait','$race_id','$only_npc','$location','$name','$cstr_mesh','$cstr_material','$cstr_texture')";
        $result = mysql_query2($query); 
        // redirect

    }
    else if ($operation == 'delete')
    {
        $id = $_POST['id'];
        // insert script
        $query = "delete from traits where id='$id'";
        $result = mysql_query2($query); 
        // redirect
    }
    else
    { 
        // manage another operation here
        echo "Operation $operation not supported.";
    }
    unset($_POST);
    list_traits();
}
?>