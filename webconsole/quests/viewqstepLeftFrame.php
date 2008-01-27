<?
function viewqstepLeftFrame(){
	include "./npcs/npc_common.php";

	?>
	<!--
	     (Please keep all copyright notices.)
	     This page document includes the Treeview script.
	     Script found at: http://www.treeview.net
	     Author: Marcelino Alves Martins
	-->
	
	<html>
	<head>
	
	<title>View Quest Step</title>
	

	
	<!-- As in a client-side built tree, all the tree infrastructure is put in place
	     within the HEAD block, but the actual tree rendering is trigered within the
	     BODY -->
	
	<!-- Code for browser detection -->
	<script src="ua.js"></script>
	
	<!-- Infrastructure code for the tree -->
	<script src="ftiens4.js"></script>
	
	<!-- Execution of the code that actually builds the specific tree.
	     The variable foldersTree creates its structure with calls to
		 gFld, insFld, and insDoc -->
	
	
	
	<script>
	USETEXTLINKS = 1
	STARTALLOPEN = 0
	PERSERVESTATE = 1
	ICONPATH = 'images/' 
	
	
	<?PHP

	$area = $_GET['id']; 
	// output knowledge folder
	echo "foldersTree = gFld(\"<b>Quest Step: $area</b>\", \"index.php?page=npc_actions&operation=viewka&type=queststep&area=$area\");\n"; 
	// start printing the triggers from triggers with prior=0
	printChildTriggers("foldersTree", $area, 0, 2, 1, "queststep");

	?>
	
	
	
	
	
	</script>
	</head>
	
	<body topmargin=16 marginheight=16 >
	
	<!-- By removing the follwoing code you are violating your user agreement.
	     Corporate users or any others that want to remove the link should check 
		 the online FAQ for instructions on how to obtain a version without the link -->
	<!-- Removing this link will make the script stop from working -->
	<div style="position:absolute; top:0; left:0; "><table border=0><tr><td><font size=-2><a style="font-size:7pt;text-decoration:none;color:silver" href="http://www.treemenu.net/" target=_blank>JavaScript Tree Menu</a></font></td></tr></table></div>
	
	<!-- Build the browser's objects and display default view of the 
	     tree. -->
	<script>
	initializeDocument()
	</script>
	<noscript>
	A tree for site navigation will open here if you enable JavaScript in your browser.
	</noscript>
	
	
	<div valign='bottom'><p><A HREF='index.php?page=listquests' target='_top'>Back to Quests Index </A></p></div>
	</body>
	
	</html>
<?
}

?>