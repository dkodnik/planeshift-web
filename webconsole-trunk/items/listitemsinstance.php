<?
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

    $query = "select c.id, sec.name, ist.name, c.item_stats_id_standard, c.parent_item_id, c.location_in_parent, c.stack_count, c.creator_mark_id, c.guild_mark_id, c.loc_x, c.loc_y, c.loc_z, c.loc_yrot, c.flags from item_instances as c, sectors as sec, item_stats as ist ";
    $query = $query . "  where char_id_owner =0 and c.loc_sector_id=sec.id and c.item_stats_id_standard=ist.id ";
    $result = mysql_query2($query);

    echo "  <TABLE BORDER=1>";
    echo "  <TH> ID </TH> <TH> SECTOR</TH> <TH> Base_Item_Name (id)</TH> <TH> parent_item_id </TH> <TH> location_in_parent </TH><TH> stack_count</TH> <TH> creator_mark_id</TH> <TH> guild_mark_id</TH> <TH> POSITION</TH> <TH> FLAGS</TH><TH> FUNCTIONS</TH>";

    while ($line = mysql_fetch_array($result, MYSQL_NUM))
    {
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
}

?>

