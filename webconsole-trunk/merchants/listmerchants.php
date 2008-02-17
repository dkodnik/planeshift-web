<?
function listmerchants(){
	include('util.php');

	?>
	<SCRIPT language=javascript>
	
	function confirmDelete()
	{
	    return confirm("Are you sure you want to remove this category?");
	}
	
	</SCRIPT>
	<?PHP

    checkAccess('listmerchants', '', 'read');
  
	$query = "select distinct m.player_id,c.name,c.id,s.name from merchant_item_categories m, characters c, sectors s where c.id=m.player_id and c.loc_sector_id=s.id order by s.name, c.name";
	$result = mysql_query2($query);

	while ($line = mysql_fetch_array($result, MYSQL_NUM)){ 
		// Find number of items in category
		$category = "select m.category_id,c.name from merchant_item_categories m, item_categories c where m.category_id=c.category_id AND m.player_id='$line[0]'";
		$cate_res = mysql_query2($category);

		echo "  <P><b><A HREF=index.php?page=viewnpc&id=$line[0]>$line[1]</A> in $line[3]</b></P>";
		echo '  <TABLE BORDER=1>';
		echo "  <TH> Category </TH> <TH> Functions </TH>";
		while ($cate = mysql_fetch_array($cate_res, MYSQL_NUM)){
			echo "<TR><TD>$cate[1]</TD>";
			echo "<TD><FORM ACTION=index.php?page=merchant_actions&operation=remove METHOD=POST>";
			echo "<INPUT TYPE=hidden NAME=category_id VALUE=\"$cate[0]\">";
			echo "<INPUT TYPE=hidden NAME=player_id VALUE=\"$line[0]\">";
			echo "<INPUT TYPE=SUBMIT NAME=submit VALUE=Remove></FORM>";
		}
		echo '<TR><TD>';
		echo "<FORM ACTION=index.php?page=merchant_actions&operation=add METHOD=POST>";
		SelectItemCateogory("", "category_id");
		echo "<INPUT TYPE=hidden NAME=player_id VALUE=\"$line[2]\">";
		echo "<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Add>";
		echo '</FORM></TD></TR>';

		echo '</TABLE>';
	}

	echo ' <P>Create new merchant</P>';
	echo '  <TABLE BORDER=1>';
	echo "<TR><TD><FORM ACTION=index.php?page=merchant_actions&operation=add METHOD=POST>";
	SelectItemCateogory("", "category_id");
	echo '</TD><TD>';
	SelectNPCs("", "player_id", "both");
	echo "<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Add>";
	echo '</FORM></TD></TR>';
	echo '</TABLE>';

	echo '<br><br>';
}

?>
