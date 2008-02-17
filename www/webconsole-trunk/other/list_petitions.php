<?
function list_petitions(){
	checkAccess('petitions', '', 'read');
	$sql='select * from petitions';
	$query=mysql_query2($sql);
	echo'<table  border="1" cellspacing="0"  cellpadding="0">';
	echo'<tr><td>player</td><td>petition</td><td>status</td><td>category</td><td>created_date</td><td>closed_date</td><td>escalation_level</td><td>assigned_gm</td><td>resolution</td></tr>';
	while($result=mysql_fetch_array( $query)){
		$sql="select name from characters where id ='".$result['player']."' limit 1";
		$query2=mysql_query2($sql);
		$result2=mysql_fetch_array( $query2);
		$sql="select name from characters where id ='".$result['assigned_gm']."' limit 1";
		$query3=mysql_query2($sql);
		$result3=mysql_fetch_array( $query3);
		if ($result['assigned_gm'] =='-1'){$result3['name']='none';};
		echo'<tr><td><a href="index.php?page=view_characters&character_id='.$result['player'].'">'.$result2['name'].'</a></td><td>'.$result['petition'].'</td><td>'.$result['status'].'</td><td>'.$result['category'].'</td><td>'.$result['created_date'].'</td><td>'.$result['closed_date'].'</td><td>'.$result['escalation_level'].'</td><td>'.$result3['name'].'</td><td>'.$result['resolution'].'</td></tr>';
   	     	     	     	  
	}
	echo'</table>';
}
?>