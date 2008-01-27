<?PHP
    checkAccess('main', '', 'read');
?>

<SCRIPT language=javascript>


</SCRIPT>
<H2> Basic Item Data:</H2>
<TABLE BORDER=1 CELLPADDING=5 CELLSPACING=0>
<TR>
    <TD>Object Name:</TD>
    <TD><INPUT TYPE=TEXT></TD>
</TR>
<TR>
    <TD>Description:</TD>
    <TD><INPUT TYPE=TEXT></TD>
</TR>
<TR>
    <TD>Weight:</TD>
    <TD><INPUT TYPE=TEXT></TD>
</TR>
<TR>
    <TD>Base Sale Price:</TD>
    <TD><INPUT TYPE=TEXT></TD>
</TR>
<TR>
    <TD>Valid Slots:</TD>
    <TD>    
    <?PHP ValidSlotCheckArea( "validSlots" ); ?>
    </TD>
</TR>

<TR>
    <TD>Flags</TD>
    <TD>    
    <?PHP FlagCheckArea( "flags" ); ?>
    </TD>
</TR>

<TR>
    <TD>Item Category</TD>
    <TD> <?PHP ItemCategorySelect("itemCat"); ?></TD>
</TABLE>


<H2>Object Skill Requirements</H2>
<P>These are the Requirements for the item to be equipped/used.
<TABLE BORDER=1 CELLPADDING=5 CELLSPACING=0>
<TH></TH><TH>Requirement</TH><TH>Value</TH>
<TR>
    <TD> Requirement 1 </TD>
    <TD> <?PHP CreateSkillOptionList( "requirement_id_1"); ?></TD>
    <TD><INPUT TYPE=TEXT></TD>
</TR>
<TR>
    <TD> Requirement 2 </TD>
    <TD> <?PHP CreateSkillOptionList( "requirement_id_2"); ?></TD>
    <TD><INPUT TYPE=TEXT></TD>
</TR>
<TR>
    <TD> Requirement 3 </TD>
    <TD> <?PHP CreateSkillOptionList( "requirement_id_3"); ?></TD>
    <TD><INPUT TYPE=TEXT></TD>
</TR>
</TABLE>


<H2> Skill Usage </H2>
<P> These are the skills trained when the item is succesfully used.
<TABLE BORDER=1 CELLPADDING=5 CELLSPACING=0>
<TR>
    <TD> Skill 1: </TD>
    <TD> <?PHP CreateSkillOptionList( "skill_id_1"); ?></TD>
</TR>
<TR>
    <TD> Skill 2: </TD>
    <TD> <?PHP CreateSkillOptionList( "skill_id_2"); ?></TD>
</TR>
<TR>
    <TD> Skill 3: </TD>
    <TD> <?PHP CreateSkillOptionList( "skill_id_3"); ?></TD>
</TR>
</TABLE>

<H2> Weapon Attributes </H2>
<TABLE BORDER=1 CELLPADDING=5 CELLSPACING=0>
    <TR>
        <TD>Weapon Speed</TD>
        <TD><INPUT TYPE=TEXT></TD>
    </TR>
    <TR>
        <TD>Penetration</TD>
        <TD><INPUT TYPE=TEXT></TD>
    </TR>
    <TR>
        <TD>Block Targeted</TD>
        <TD><INPUT TYPE=TEXT></TD>
    </TR>
    <TR>
        <TD>Block Un-Targeted</TD>
        <TD><INPUT TYPE=TEXT></TD>
    </TR>
    <TR>
        <TD>Counter Block</TD>
        <TD><INPUT TYPE=TEXT></TD>
    </TR>            
</TABLE>

<H2> Armour Attributes </H2>
<TABLE BORDER=1 CELLPADDING=5 CELLSPACING=0>
    <TR>
        <TD>Hardness</TD>
        <TD><INPUT TYPE=TEXT></TD>
    </TR>
    <TR>
        <TD>VS Weapon Type</TD>
        <TD><INPUT TYPE=TEXT></TD>
    </TR>               
</TABLE>


<H2> Properties </H2>
<TABLE BORDER=1 CELLPADDING=5 CELLSPACING=0>
    <TH></TH><TH>Inflict Damage</TH><TH>Percent Chance</TH><TH>Protection Against</TH>
    <TR>
        <TD> Slash </TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>
    </TR>
    <TR>
        <TD> Blunt </TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>        
    </TR>
    <TR>
        <TD> Pierce </TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>        
    </TR>
    <TR>
        <TD> Force </TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>        
    </TR>
    <TR>
        <TD> Fire </TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>        
    </TR>
    <TR>
        <TD> Ice </TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>        
    </TR>
    <TR>
        <TD> Air </TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>        
    </TR>
    <TR>
        <TD> Posion </TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>        
    </TR>
    <TR>
        <TD> Disease </TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>        
    </TR>
    <TR>
        <TD> Holy </TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>        
    </TR>
    <TR>
        <TD> Unholy </TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>
        <TD><INPUT TYPE=TEXT></TD>        
    </TR>    
</TABLE>

<?PHP
    
/*
	$query = "select c.id, sec.name, ist.name, c.item_stats_id_standard, c.parent_item_id, c.location_in_parent, c.stack_count, c.creator_mark_id, c.guild_mark_id, c.loc_x, c.loc_y, c.loc_z, c.loc_yrot, c.flags from item_instances as c, sectors as sec, item_stats as ist ";
	$query = $query . "  where char_id_owner =0 and c.loc_sector_id=sec.id and c.item_stats_id_standard=ist.id ";
	$result = mysql_query2($query);

	echo "  <TABLE BORDER=1>";
	echo "  <TH> ID </TH> <TH> SECTOR</TH> <TH> Base_Item_Name (id)</TH> <TH> parent_item_id </TH> <TH> location_in_parent </TH><TH> stack_count</TH> <TH> creator_mark_id</TH> <TH> guild_mark_id</TH> <TH> POSITION</TH> <TH> FLAGS</TH><TH> FUNCTIONS</TH>";

	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		printf("<TR><TD>%s</TD><TD>%s</TD><TD>%s (%s)</TD><TD>%s</TD><TD>%s</TD><TD>%s</TD><TD>%s</TD><TD>%s</TD><TD>%s,%s,%s, (%s)</TD><TD>%s</TD>",
			$line[0], $line[1], $line[2], $line[3], $line[4], $line[5], $line[6], $line[7], $line[8], $line[9], $line[10], $line[11], $line[12], $line[13]);
		printf("<TD><FORM ACTION=processcommand.php METHOD=POST>");
		printf("<INPUT TYPE=HIDDEN NAME=id VALUE=%d \">", $line[0]);
		printf("<INPUT TYPE=SUBMIT NAME=%s VALUE=\"DELETE\" onclick=\"return confirmDelete()\">", $line[0]);
		printf("<INPUT TYPE=SUBMIT NAME=submit VALUE=\"DE/ACTIVATE\">");
		printf('</FORM></TD></TR>');
	}
	echo '</TABLE>';

	echo '<br><br>';
*/
?>

