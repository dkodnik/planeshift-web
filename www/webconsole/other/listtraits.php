<?

include('util.php');

function SelectLocation($current_location,$select_name)
{
        printf("<SELECT name=%s>", $select_name);
        $location="EYE_COLOR"; if ($current_location == $location){$selected="selected";}else{$selected="";}
        printf("<OPTION %s value=\"%s\">%s</OPTION>",$selected,$location,$location);
        $location="HAIR_COLOR"; if ($current_location == $location){$selected="selected";}else{$selected="";}
        printf("<OPTION %s value=\"%s\">%s</OPTION>",$selected,$location,$location);
        $location="HAIR_STYLE"; if ($current_location == $location){$selected="selected";}else{$selected="";}
        printf("<OPTION %s value=\"%s\">%s</OPTION>",$selected,$location,$location);
        $location="BEARD_STYLE"; if ($current_location == $location){$selected="selected";}else{$selected="";}
        printf("<OPTION %s value=\"%s\">%s</OPTION>",$selected,$location,$location);
        $location="SKIN_TONE"; if ($current_location == $location){$selected="selected";}else{$selected="";}
        printf("<OPTION %s value=\"%s\">%s</OPTION>",$selected,$location,$location);
        printf("</SELECT>");
}

function SelectOnlyNPC($current_location,$select_name)
{
        printf("<SELECT name=%s>", $select_name);
        $location="0"; if ($current_location == $location){$selected="selected";}else{$selected="";}
        printf("<OPTION %s value=\"%s\">%s</OPTION>",$selected,$location,"NO");
        $location="1"; if ($current_location == $location){$selected="selected";}else{$selected="";}
        printf("<OPTION %s value=\"%s\">%s</OPTION>",$selected,$location,"YES");
        printf("</SELECT>");
}


/******************************************************************************
Used to create a drop down list from common string using a like search 
******************************************************************************/
function SelectCommonStringLike( $current_string, $like, $select_name )
{
    
    printf("<SELECT name=%s>", $select_name);
    $query_strings = "select id, string from common_strings where string like '$like%'";
    $result = mysql_query2($query_strings);
    while ($list = mysql_fetch_array($result, MYSQL_NUM))
    {
        if ($list[0] == $current_string)
        {
            printf("<OPTION selected value=\"%s\">%s - %s</OPTION>",$list[0], $list[0], $list[1]);
        }
        else
        {
            printf("<OPTION value=\"%s\">%s - %s</OPTION>", $list[0], $list[0], $list[1]);
        }
    }
    printf("</SELECT>");
}



/******************************************************************************
Show the traits for a particluar race
 race_id  The race id from the race_info table we want to see the traits for.
******************************************************************************/
function show_traits( $race_id )
{
    checkAccess('main', '', 'read');
   
    $query = "SELECT  name from  race_info where race_id=$race_id";
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
    $result = mysql_query($query);

    echo "<A NAME='hair_colours'/>";
    echo "<H2> Hair Colours</H2>";
   
    echo "<TABLE BORDER=1>";
    echo "<TH>Name</TH>";
    echo "<TH>NPC Only</TH>";
    echo "<TH>Shader</TH>";
    echo "<TH>Delete</TH>";
    echo "<TH>Update</TH>";

    while ( $line = mysql_fetch_array($result, MYSQL_BOTH) )
    {
        echo "<FORM ACTION='other/handle_trait.php' METHOD='post'>";
        echo "<INPUT TYPE=HIDDEN NAME=trait_id VALUE=$line[0]>";
        echo "<INPUT TYPE=HIDDEN NAME=race_id VALUE=$race_id>";
        echo "<INPUT TYPE=HIDDEN NAME=action VALUE=update>";
        echo "<INPUT TYPE=HIDDEN NAME=area VALUE=HAIR_COLOR>";

        echo "<TR>";
        echo "<TD><INPUT TYPE=TEXT NAME=trait_name VALUE='" . $line["name"] . "'></TD>";
        echo "<TD>"; SelectOnlyNPC($line[3],"only_npc"); echo "</TD>";
        echo "<TD><INPUT TYPE=TEXT VALUE='" . $line["shader"]. "' NAME=shader></TD>";
        echo "<TD><INPUT TYPE=CHECKBOX NAME=delete_box></TD>";
        echo "<TD><INPUT TYPE=SUBMIT VALUE=Update></TD>";
        echo "</TR>";
        echo "</FORM>";
    }

    echo "<FORM ACTION='other/handle_trait.php' METHOD='post'>";
    echo "<INPUT TYPE=HIDDEN NAME=trait_id VALUE=$line[0]>";
    echo "<INPUT TYPE=HIDDEN NAME=race_id VALUE=$race_id>";
    echo "<INPUT TYPE=HIDDEN NAME=action VALUE=new>";
    echo "<INPUT TYPE=HIDDEN NAME=area VALUE=HAIR_COLOR>";

    echo "<TR>";
    echo "<TD><INPUT TYPE=TEXT NAME=trait_name></TD>";
    echo "<TD>"; SelectOnlyNPC("0","only_npc"); echo "</TD>";
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
    $result = mysql_query($query);

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
        echo "<FORM ACTION='other/handle_trait.php' METHOD='post'>";
        echo "<INPUT TYPE=HIDDEN NAME=trait_id VALUE=$line[0]>";
        echo "<INPUT TYPE=HIDDEN NAME=race_id VALUE=$race_id>";
        echo "<INPUT TYPE=HIDDEN NAME=action VALUE=update>";
        echo "<INPUT TYPE=HIDDEN NAME=area VALUE=FACE>";

        echo "<TR>";
        echo "<TD><INPUT TYPE=TEXT NAME=trait_name VALUE='" . $line["name"] . "'></TD>";
        echo "<TD>"; SelectOnlyNPC($line[3],"only_npc"); echo "</TD>";
        echo "<TD>"; SelectCommonStringLike($line[7], "$"."F_head","cstr_id_material");
        echo "<TD>"; SelectCommonStringLike($line[8], "/planeshift/models/".  "$". "F/". "$" . "F_head","cstr_id_texture");
        echo "<TD><INPUT TYPE=CHECKBOX NAME=delete_box></TD>";
        echo "<TD><INPUT TYPE=SUBMIT VALUE=Update></TD>";
        echo "</TR>";
        echo "</FORM>";
    }

    echo "<FORM ACTION='other/handle_trait.php' METHOD='post'>";
    echo "<INPUT TYPE=HIDDEN NAME=trait_id VALUE=$line[0]>";
    echo "<INPUT TYPE=HIDDEN NAME=race_id VALUE=$race_id>";
    echo "<INPUT TYPE=HIDDEN NAME=action VALUE=new>";
    echo "<INPUT TYPE=HIDDEN NAME=area VALUE=FACE>";


    echo "<TR>";
    echo "<TD><INPUT TYPE=TEXT NAME=trait_name></TD>";
    echo "<TD>"; SelectOnlyNPC("0","only_npc"); echo "</TD>";
    echo "<TD>"; SelectCommonStringLike("", "$"."F_head","cstr_id_material");
    echo "<TD>"; SelectCommonStringLike("", "/planeshift/models/".  "$". "F/". "$" . "F_head","cstr_id_texture");
    echo "<TD>-</TD>";
    echo "<TD><INPUT TYPE=SUBMIT VALUE=Add></TD>";
    echo "</TR>";
    echo "</FORM>";
 
    
    echo "</TABLE>";
}


/*****************************************************************************
Shows a list of the different races so you can pick one.
 *****************************************************************************/
function show_races()
{
    checkAccess('main', '', 'read');

    if ( $_GET['function'] == 'list' )
    {
        show_traits( $_GET['race_id'] );  
    }
    else
    {
    $query = "SELECT race_id, name, sex from race_info where race_id < 12";
    $result = mysql_query2($query);
   
    echo "<P>Select the race that you want to change the traits on";
    echo "<CENTER>";
    echo "<TABLE>";
    echo "<TR><TD>";
    echo "<TABLE BORDER=1 CELLPADDING=5 CELLSPACING=0>";
    echo "<TH>ID</TH><TH>Race</TH><TH>Gender</TH>";
    
    while ($line = mysql_fetch_array($result, MYSQL_NUM))
    {
echo "<TR><TD>" . $line[0] . "</TD><TD><A HREF=index.php?page=list_traits&function=list&race_id=" . $line[0] . ">" . $line[1] . "</A></TD><TD>" . $line[2] . "</TD></TR>";

    }

    echo "</TABLE>";
    echo "</TD><TD VALIGN=TOP>";
    echo "<TABLE BORDER=1 CELLPADDING=5 CELLSPACING=0>";
    echo "<TH>ID</TH><TH>Race</TH><TH>Gender</TH>";
    
    $query = "SELECT race_id, name, sex from race_info where race_id >= 12 and race_id <= 22";
    $result = mysql_query2($query);


    while ($line = mysql_fetch_array($result, MYSQL_NUM))
    {
        echo "<TR><TD>" . $line[0] . "</TD><TD><A HREF=index.php?page=list_traits&function=list&race_id=" . $line[0] . ">" . $line[1] . "</A></TD><TD>" . $line[2] . "</TD></TR>";

    }

    echo "</TABLE>";

    echo "</TD></TR></TABLE>";
    echo "</CENTER>";
    }
}

function list_traits(){

    checkAccess('main', '', 'read');

	$query = "select id, next_trait, race_id, only_npc, location, name, cstr_id_mesh, cstr_id_material, cstr_id_texture from traits";
	$result = mysql_query2($query);

	echo '  <TABLE BORDER=1>';
	echo '  <TH> ID </TH> ';
	echo '  <TH> Next </TH> ';
	echo '  <TH> Race/Location - Only NPC / Name </TH> ';
	echo '  <TH> Mesh/Material/Texture </TH> ';
	echo '  <TH> Functions </TH> ';

	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		echo '<TR>';
		echo '<FORM ACTION=index.php?page=trait_actions&operation=update METHOD=POST>';
		echo "<TD><INPUT TYPE=hidden NAME=id VALUE=\"$line[0]\">$line[0]</TD>";
		echo "<TD><INPUT SIZE=5 TYPE=text NAME=next_trait VALUE=\"$line[1]\"></TD>";
		echo "<TD><TABLE><TR><TD>"; SelectRace($line[2],"race_id"); echo "</TD>";
		echo "<TD>"; SelectOnlyNPC($line[3],"only_npc"); echo "</TD></TR>";
		echo "<TR><TD>"; SelectLocation($line[4],"location"); echo "</TD>";
		echo "<TD><INPUT TYPE=text NAME=name VALUE=\"$line[5]\"></TD></TR></TABLE></TD>";
		echo "<TD>"; SelectCommonString($line[6],"cstr_id_mesh"); echo "<BR>";
		echo ""; SelectCommonString($line[7],"cstr_id_material"); echo "<BR>";
		echo ""; SelectCommonString($line[8],"cstr_id_texture"); echo "</TD>";
		echo '<TD><TABLE><TR><TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Update></FORM></TD>';
		echo '<TD><FORM ACTION=index.php?page=trait_actions&operation=delete METHOD=POST>';
		echo "<INPUT TYPE=hidden NAME=id VALUE=\"$line[0]\">";
		echo '<INPUT TYPE=SUBMIT NAME=submit VALUE=Delete></FORM></TD></TR></TABLE>';
		echo '</TD></TR>';
	}
	echo '<TR>';
	echo '<FORM ACTION=index.php?page=trait_actions&operation=add METHOD=POST>';
	echo "<TD></TD>";
	echo "<TD><INPUT SIZE=5 TYPE=text NAME=next_trait></TD>";
	echo "<TD><TABLE><TR><TD>"; SelectRace("1","race_id"); echo "</TD>";
	echo "<TD>"; SelectOnlyNPC("0","only_npc"); echo "</TD></TR>";
	echo "<TR><TD>"; SelectLocation("EYE_COLOR","location"); echo "</TD>";
	echo "<TD><INPUT TYPE=text NAME=name ></TD></TR></TABLE></TD>";
	echo "<TD>"; SelectCommonString("0","cstr_id_mesh"); echo "<BR>";
	echo ""; SelectCommonString("0","cstr_id_material"); echo "<BR>";
	echo ""; SelectCommonString("0","cstr_id_texture"); echo "</TD>";
	echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Add></FORM>';

	echo '</FORM></TD></TR>';
	echo '</TABLE><br><br>';

	echo '<br><br>';
}

?>
  
