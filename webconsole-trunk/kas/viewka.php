<?
function viewka(){

	?>
<html>
<head>
<title>View/Edit Knowledge Area</title>
<script>
function op() { //This function is used with folders that do not open pages themselves. See online docs.
}
</script>
</head>

<!--
(Please keep all copyright notices.)
This frameset document includes the Treeview script.
Script found in: http://www.treeview.net
Author: Marcelino Alves Martins

You may make other changes, see online instructions, 
but do not change the names of the frames (treeframe and basefrm)
-->


<FRAMESET cols="30%,*" onResize="if (navigator.family == 'nn4') window.location.reload()"> 
  <FRAME src="index.php?page=viewkaLeftFrame&area=<?PHP $area = $_GET['area'];
	echo "$area";
	?>" name="treeframe" >
  <FRAME SRC="index.php?page=npc_actions&operation=viewka&type=ka&area=<?PHP $area = $_GET['area'];
	echo "$area";
	?>" name="basefrm"> 
</FRAMESET>
  
<?
}

?>


