<?
function findtrigger(){

	checkAccess('listnpc', '', 'read');

	echo '<br><TABLE BORDER=0>';
  echo "<FORM ACTION=index.php?page=npc_actions&operation=findtrigger METHOD=POST >";
	echo '<tr><td>Enter the word to search for: </td><td><INPUT TYPE=text NAME=word>';
  echo " <INPUT TYPE=SUBMIT NAME=submit VALUE=Search></td></tr>";
	echo '</TABLE></FORM>';

	echo '<br><br>';
}

?>

			
			
			

