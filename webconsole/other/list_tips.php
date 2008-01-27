<?PHP
function list_tips(){

	checkAccess('tips', '', 'read');
	$sql = 'select * from tips order by tip';
	$query = mysql_query2($sql);
	echo'<table border="1">';
	while ($result = mysql_fetch_array($query)){
		echo"<tr><td>
		<form action='index.php?page=tips_actions' method='post'>
		<input type='text' size='70' name ='tip' value='".$result['tip']."'></td><td>
		<input type='hidden' value ='edit' name='operation'>
		<input type='hidden' value ='".$result['id']."' name='id'>
		<input type='submit' value='Save'></form>
		<form action='index.php?page=tips_actions' method='post'>
		<input type='hidden' value ='delete' name='operation'>
		<input type='hidden' value ='".$result['id']."' name='id'>
		<input type='submit' value='Delete'></form>
		</td></tr> ";
	}
	echo'</table>';
	echo'
	<form action="index.php?page=tips_actions" method="post">
	<input type="hidden" value ="add" name="operation">
	<input type="text" name ="tip">
	<input type="submit" value="Add tip">
	</form>
	';
	
}

?>