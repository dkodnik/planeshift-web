<?PHP
function list_tips(){

	checkAccess('tips', '', 'read');
	$sql = 'select * from tips order by tip';
	$query = mysql_query2($sql);
	echo'<table border="1">';
	while ($result = mysql_fetch_array($query)){
		echo"<tr><td>
		<form action='index.php?page=tips_actions' method='post'>
		<textarea cols='50' rows='2' name='tip'>".$result['tip']."</textarea>
		<input type='hidden' value ='edit' name='operation'>
		<input type='hidden' value ='".$result['id']."' name='id'>
		</td><td>
		<input type='submit' value='Save'></form>
		<form action='index.php?page=tips_actions' method='post'>
		<input type='hidden' value ='delete' name='operation'>
		<input type='hidden' value ='".$result['id']."' name='id'>
		<input type='submit' value='Delete'></form>
		</td></tr> ";
	}
	echo'<tr><td>
	<form action="index.php?page=tips_actions" method="post">
	<input type="hidden" value ="add" name="operation">
	<textarea cols="50" rows="2" name="tip"></textarea>
	</td><td>
	<input type="submit" value="Add tip">
	</form>
	</table>
	';
	
}

?>