<?PHP
function tips_actions(){

	$operation=$_POST['operation'];
	$tip =$_POST['tip'];
	$id =$_POST['id'];

	if ($operation== 'add'){
		checkAccess('tips', '', 'create');
		mysql_query2("insert into tips (tip) values ('$tip')");
		echo'<SCRIPT language="javascript">document.location = "index.php?page=list_tips";</script>';
	
	}
	elseif ($operation== 'edit'){
		checkAccess('tips', '', 'edit');
		mysql_query2("update tips set tip = '$tip' where id = '$id'");
		echo'<SCRIPT language="javascript">document.location = "index.php?page=list_tips";</script>';
	
	}
	elseif ($operation== 'delete'){
			checkAccess('tips', '', 'delete');
			mysql_query2("delete from tips where id ='$id'");
		echo'<SCRIPT language="javascript">document.location = "index.php?page=list_tips";</script>';
	}
	else echo 'operation not supported';
}	
?>