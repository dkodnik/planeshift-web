<?

function SelectSectorsSP($current_sector,$select_name){
	printf("<SELECT name=%s>", $select_name);
	$query_events = "select name from sectors";
	$result = mysql_query2($query_events);

	//add the special entries
	printf("<OPTION %svalue=\"all\">all</OPTION>", ($current_sector == "all" || $current_sector == "") ? "selected " : "");
	printf("<OPTION %svalue=\"excludeprivate\">exclude private</OPTION>", ($current_sector == "excludeprivate") ? "selected " : "");

	while ($list = mysql_fetch_array($result, MYSQL_NUM)){
		if ($list[0] == $current_sector){
			printf("<OPTION selected value=\"%s\">%s</OPTION>", $list[0], $list[0]);
		}else{
			printf("<OPTION value=\"%s\">%s</OPTION>", $list[0], $list[0]);
		}
	}
	printf("</SELECT>");
}

function listitemsinstance()
{
?>
                  

<SCRIPT language=javascript>

function confirmDelete()
{
    return confirm("Are you sure you want to delete this Item Instance?");
}

</SCRIPT>

<?PHP
    checkAccess('main', '', 'read');

	$sector = $_GET['sector'];

    $query = "SELECT c.id, sec.name, ist.name, c.item_stats_id_standard, c.parent_item_id, c.location_in_parent, c.stack_count, c.creator_mark_id, c.guild_mark_id, c.loc_x, c.loc_y, c.loc_z, c.loc_yrot, c.flags, c.loc_instance from item_instances as c, sectors as sec, item_stats as ist ";
	$query = $query . "  WHERE char_id_owner =0  AND c.item_stats_id_standard=ist.id ";

	//check if it's a special case
	if($sector == "all" || $sector == "") //list all the items on ground
		$query = $query . "AND c.loc_sector_id=sec.id ";
	else if($sector == "excludeprivate") //exclude the guild and npc sectors
		$query = $query . " AND c.loc_sector_id=sec.id AND sec.name != 'guildsimple' AND sec.name != 'guildlaw' AND sec.name != 'NPCroom' ";
	else //select only a sector provided by the user
		$query = $query . "AND sec.id=(SELECT id FROM sectors where name='$sector') AND c.loc_sector_id=sec.id ";

    $result = mysql_query2($query);
	
	echo "  <FORM action=\"index.php?page=listitemsinstance\" METHOD=GET>";
	echo "  <INPUT TYPE=hidden NAME=page VALUE=\"listitemsinstance\">";
	echo "  <b>Select one area:</b> <br><br> Area: ";
	SelectSectorsSP($sector,"sector");
	echo " <br><br><INPUT type=submit value=view><br><br>";
	echo "</FORM>";

    echo "  <TABLE BORDER=1>";
    echo "  <TH> ID </TH> <TH> SECTOR</TH> <TH> Base_Item_Name (id)</TH> <TH> parent_item_id </TH> <TH> location_in_parent </TH><TH> stack_count</TH> <TH> creator_mark_id</TH> <TH> guild_mark_id</TH> <TH> POSITION</TH> <TH> FLAGS</TH><TH> FUNCTIONS</TH>";

    while ($line = mysql_fetch_array($result, MYSQL_NUM))
    {
        printf("<TR><TD>%s</TD><TD>%s</TD><TD>%s (%s)</TD><TD>%s</TD><TD>%s</TD><TD>%s</TD><TD>%s</TD><TD>%s</TD><TD>%s,%s,%s, (%s), [%s]</TD><TD>%s</TD>",
                  $line[0], $line[1], $line[2], $line[3], $line[4], $line[5], $line[6], $line[7], $line[8], $line[9], $line[10], $line[11], $line[12], $line[14], $line[13]);
        printf("<TD><FORM ACTION=processcommand.php METHOD=POST>");
        printf("<INPUT TYPE=HIDDEN NAME=id VALUE=%d \">", $line[0]);
        printf("<INPUT TYPE=SUBMIT NAME=%s VALUE=\"DELETE\" onclick=\"return confirmDelete()\">", $line[0]);
        printf("<INPUT TYPE=SUBMIT NAME=submit VALUE=\"DE/ACTIVATE\">");
        printf('</FORM></TD></TR>');
    }
    
    echo '</TABLE>';

    echo '<br><br>';
}

?>

