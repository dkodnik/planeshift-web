<?
function viewnpcLeftFrame(){
	include('npc_common.php');

	?>	
	<!--
	     (Please keep all copyright notices.)
	     This page document includes the Treeview script.
	     Script found at: http://www.treeview.net
	     Author: Marcelino Alves Martins
	-->

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

	$id = $_GET['id']; 
	// get name of NPC
	$query = "select name, lastname from characters where id=" . $id;
	$result = mysql_query2($query);
	$line = mysql_fetch_array($result, MYSQL_NUM);
	$area = $line[0] . " " . $line[1]; 
	// output main stats folder
	echo "foldersTree = gFld(\"<b>$area</b>\"); "; 
	// output main node
	echo "temp1 = insDoc(foldersTree, gLnk(\"R\", \"main\", \"index.php?page=npc_actions&operation=viewmain&npcid=$id\"));\n"; 
	// output traits node
	echo "temp2 = insDoc(foldersTree, gLnk(\"R\", \"skills\", \"index.php?page=npc_actions&operation=viewskills&npcid=$id\"));\n"; 
	// output skills node
	echo "temp3 = insDoc(foldersTree, gLnk(\"R\", \"traits\", \"index.php?page=npc_actions&operation=viewtraits&npcid=$id\"));\n"; 
	// output KAs node
	echo "temp4 = insDoc(foldersTree, gLnk(\"R\", \"knowledge areas\", \"index.php?page=npc_actions&operation=viewkas&npcid=$id\"));\n"; 
	// output items node
	echo "temp5 = insDoc(foldersTree, gLnk(\"R\", \"items\", \"index.php?page=npc_actions&operation=viewitems&npcid=$id\"));\n"; 
	// output training node
	echo "temp6 = insDoc(foldersTree, gLnk(\"R\", \"trainer\", \"index.php?page=npc_actions&operation=viewtrainer&npcid=$id\"));\n"; 
	// output merchant node
	echo "temp7 = insDoc(foldersTree, gLnk(\"R\", \"merchant\", \"index.php?page=npc_actions&operation=viewmerchant&npcid=$id\"));\n";
	// output knowledge folder
	echo "aux1 = insFld(foldersTree, gFld(\"specific knowledge\", \"index.php?page=npc_actions&operation=viewka&type=npc&area=\"+escape(\"$area\")));\n"; 
	// start printing the triggers from triggers with prior=0
	printChildTriggers("aux1", $area, 0, 2, 1, "npc");

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
	<div valign='bottom'><p><A HREF='index.php?page=listnpcs' target='_top'>Back to NPC Index</A><br><A HREF='index.php?page=listnpcsinv' target='_top'>Back to invulnerable NPC Index</A></p></div>
<?
}

?>