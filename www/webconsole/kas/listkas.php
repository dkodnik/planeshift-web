<?
function listkas(){

	?>

<SCRIPT language=javascript>

function confirmDelete()
{
    return confirm("Are you sure you want to delete this KA?");
}

</SCRIPT>

<?PHP

    checkAccess('npc', '', 'read');

	include './npcs/npc_common.php';

	$result = getKAs();

	echo '  <TABLE BORDER=1>';
	echo '  <TH> KA </TH> <TH> Functions</TH>';

	for ($i = 0; $i < sizeof($result); $i++){
		echo "<TR><TD><A HREF='index.php?page=viewka&area=".$result[$i]."'>".$result[$i]."</A> </TD>";
		echo "<TD><FORM ACTION='index.php?page=ka_actions&operation=deleteka' METHOD='POST' onsubmit=\"return confirmDelete()\"'>";
		echo "<INPUT TYPE='hidden' NAME='ka' VALUE=\"$result[$i]\">";
		echo "<INPUT TYPE='SUBMIT' NAME='submit' VALUE='Delete'>";
		echo '</FORM></TD></TR>';
		echo "\n";
	}
	echo '</TABLE><br><br>';

	echo "<FORM ACTION='index.php?page=ka_actions&operation=createka' METHOD='POST'>";
	echo "Create a New KA with name: <INPUT TYPE='text' NAME='ka'>";
	echo " <INPUT TYPE='SUBMIT' NAME='submit' VALUE='Create'>";
	echo '</FORM>';
	echo '<br><br>';
}

?>

