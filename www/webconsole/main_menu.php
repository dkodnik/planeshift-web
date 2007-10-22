<?PHP
function main_menu(){

	?>

<table><tr><td valign='top' width = "150">
	<p ><a class="yellowtitlebig" href="index.php?category=Server_Functions">Server Functions</a></p>
	<p ><a class="yellowtitlebig" href="index.php?category=NPCs">NPCs</a></p>
	<p ><a class="yellowtitlebig" href="index.php?category=Quests">Quests</a></p>
	<p ><a class="yellowtitlebig" href="index.php?category=Items">Items</a></p>
	<p ><a class="yellowtitlebig" href="index.php?category=DescWorld">Describe World</a></p>
	<p ><a class="yellowtitlebig" href="index.php?category=Rules_Functions">Rules Functions</a></p>
	<p ><a class="yellowtitlebig" href="index.php?category=Trade_Skills">Trade Skills</a></p>
	<p ><a class="yellowtitlebig" href="index.php?category=Other">Other</a></p>
	<p ><br><br><a class="yellowtitlebig" href="index.php?page=logout">Logout</a></p>
	</td><td valign='top'>

<?PHP if ($_GET['category'] == 'Trade_Skills')
{
?>
    <p><a href="index.php?page=view_tradeskills&page2=trade_patterns">View trade patterns</a><p>
    
    <!--
    <p><a href="index.php?page=view_tradeskills">View trade skills</a><p>
	<p><a href="index.php?page=view_tradeskills&page2=trade_transformationskills">View trade transformations skills</a><p>
	<p><a href="index.php?page=view_tradeskills&page2=trade_transformations">View trade transformations</a><p>
	<p><a href="index.php?page=view_tradeskills&page2=trade_patterns">View trade patterns</a><p>
	<p><a href="index.php?page=view_tradeskills&page2=trade_designs">View trade designs</a><p>
	<p><a href="index.php?page=view_tradeskills&page2=trade_combinations">View trade combinations</a><p>
	<p>Create new trade skill goals (SettingMember)</p>
	<p>Create new item combinations (SettingMember)</p>
	<p>Create new item transformations (SettingMember)</p>
-->
<?PHP }
	if ($_GET['category'] == 'Rules_Functions'){
		?>	
	<p><a href="index.php?category=Rules_Functions&page=listscripts&type=base">List/Edit scripts (excluded loot, items, char creation, spells)</a> (RulesAdmin)</p>              
	<p><a href="index.php?category=Rules_Functions&page=listscripts&type=loot">List/Edit scripts: RandomLoot only</a> (RulesAdmin)</p>
	<p><a href="index.php?category=Rules_Functions&page=listscripts&type=items">List/Edit scripts: SimpleItems only</a> (RulesAdmin)</p>
	<p><a href="index.php?category=Rules_Functions&page=listscripts&type=charcreate">List/Edit scripts: Char Creation only</a> (RulesAdmin)</p>
	<p><a href="index.php?category=Rules_Functions&page=listscripts&type=spells">List/Edit scripts: Spells only</a> (RulesAdmin)</p>
	<hr>
	<p><a href="index.php?category=Rules_Functions&page=listspells">List/Edit spells</a> (RulesAdmin)</p>
	<p><a href="index.php?category=Rules_Functions&page=whereusedglyph">Where is a Glyph used?</a> (RulesAdmin)</p>
	<p><a href="index.php?category=Rules_Functions&page=listspells">Create a new spell</a> (RulesMember)</p>
	<hr>
	<p>Change W to X rate (RulesAdmin)</p>
	<p><a href="index.php?category=Rules_Functions&page=natural_resources_map">View Map of Natural resources</a> (SettingMember)</p>
	<p><a href="index.php?category=Rules_Functions&page=listnatural_resources">List/Edit Natural resources</a> (SettingMember)</p>
	<p><a href="index.php?category=Rules_Functions&page=waypoints_map">View Map of Waypoints</a> (SettingMember)</p>
	<p><a href="index.php?category=Rules_Functions&page=listwaypoints">List/Edit Waypoints</a> (SettingMember)</p>
	<p><a href="index.php?category=Rules_Functions&page=locations_map">View Map of Locations</a> (SettingMember)</p>
	<p><a href="index.php?category=Rules_Functions&page=listlocations">List/Edit Locations</a> (SettingMember)</p>
	<hr>
	<p><a href="index.php?category=Rules_Functions&page=listskills">Change skills</a> (RulesAdmin)</p>
	<p><a href="index.php?category=Rules_Functions&page=listfactions">Change factions</a> (RulesAdmin)</p>
	<p><a href="index.php?category=Rules_Functions&page=maincharcreate">Change Race starting location or CP points</a></p>
<?PHP }
        if ($_GET['category'] == 'Items')
        {
?>
        <H2>Item Options</H2>
        <TABLE CELLPADDING=5 CELLSPACING=0>
        <TR><TD><a href="index.php?page=listitems">List available base Items</a></TD></TR>
        <TR><TD><A HREF="index.php?page=newbaseitem">Create a new Base Item </A></TD></TR>
        <TR><TD><a href="index.php?page=listitemsinstance">List items instances</a> on the ground </p></TD></TR>
        <TR><TD><a href="index.php?page=listitemcategories">List/Edit item categories</a></TD></TR>
        </TABLE>
        <!--
        <p>List items owned by players (SettingAdmin)</p>
        <p>List items owned by NPCs (SettingMember)</p>
        <p>Create a new Item Instance (SettingMember)</p>
        -->
<?PHP   }
	if ($_GET['category'] == 'DescWorld'){
		?>	
	<p><a href="index.php?page=descworld">List/Edit Action locations</a> (SettingMember)</p>
	<p><a href="index.php?page=checkbooks">Check Action locations for books</a> (SettingMember)</p>
<?PHP }
	if ($_GET['category'] == 'Quests'){
		?>	
		<b>OLD quest system. Triggers/responses used.</b><br>
	<p><a href="index.php?page=listquests">List/Edit available quests, list all steps if assigned or not</a> (SettingAdmin)</p>
	<p><a href="index.php?page=createquest&type=old">Create a new trigger quest </a> (SettingMember)</p><br><br>
		<b>NEW quest system. Text scripts used.</b><br>
	<p><a href="index.php?page=listquestscripts">List/Edit quest scripts</a> (SettingAdmin)</p>
	<p><a href="index.php?page=createquest&type=new">Create a new quest script </a> (SettingMember)</p>
	<p><a href="index.php?page=listnpcquest&type=new">List NPCs involved in quest Scripts</a> (SettingMember)</p>
<?PHP }
	if ($_GET['category'] == 'NPCs'){
		?>
        <H2>NPC 's management</H2>
        <TABLE BORDER=0 CELLPADDING=5 CELLSPACING=0>
        <TR><TD><a href="index.php?page=viewnpcmap">View map of NPCs</a></TD></TR>
        <TR><TD><a href="index.php?page=searchnpc">Search an NPC by name or id. List all NPCs of one sector</a></TD></TR>
        <TR><TD><a href="index.php?page=listnpcsinv">List INVulnerable NPCs, create new, delete</a></TD></TR>
        <TR><TD><a href="index.php?page=listnpcs">List Vulnerable NPCs, create new, delete</a></TD></TR>
        <TR><TD><a href="index.php?page=listmerchants">List/Edit all merchants NPCs, edit categories</a></TD></TR>
        <TR><TD><a href="index.php?page=listtrainers">List/Edit all trainers NPCs, edit skills</a></TD></TR>
        <TR><TD><a href="index.php?page=listlootcategories">List/Edit all loot categories</a></TD></TR>
        <TR><TD><a href="index.php?page=listspawnrules">List/Edit all spawn rules</a></TD></TR>
        </TABLE>

        <HR>
        <H2>Knowledge Areas and synonyms</H2>
        <TABLE BORDER=0 CELLPADDING=5 CELLSPACING=0>
        <TR><TD><a href="index.php?category=NPCs&page=listkas">List/Edit available Knowledge Areas Single triggers</a></TD></TR>
	    <TR><TD><a href="index.php?category=NPCs&page=listkascripts">List/Edit available Knowledge Areas Scripts</a></TD></TR>
        <TR><TD><a href="index.php?page=listsynonyms">List/Edit available Synonyms</a></TD></TR>
        <TR><TD><a href="index.php?page=findtrigger">Find a trigger word used in dialogues</a></TD></TR>
        </TABLE>

        
        <HR>
        <H2>NPCs Integrity Checks</H2>
        <TABLE BORDER=0 CELLPADDING=5 CELLSPACING=0>
        <TR><TD><a href="index.php?page=checknpcloaded">Check which NPCs are not loaded in game</a></TD></TR>
        <TR><TD><a href="index.php?page=checknpctriggers">Check if NPCs have base triggers</a>
        <TR><TD><a href="index.php?page=checknpcchar">List all NPCs and their base dialog information</a>
        </TABLE>

        <!--
	<h3>&nbsp &nbsp &nbsp NPCs 's behaviour</h3>
	<p>&nbsp &nbsp &nbsp List available paths/behaviours (SettingAdmin)</p>
	<p>&nbsp &nbsp &nbsp Create a new behaviour (SettingMember)</p>
	<p>&nbsp &nbsp &nbsp Assign a path/behaviour to an NPC (SettingMember)</p>
	-->

<?PHP }
	if ($_GET['category'] == 'Server_Functions'){
        ?>
        <!-- 
        <p>Start server (SysAdmin)</p>
        <p>Stop server (SysAdmin)</p>
        <p>Stop players from logging in (SysAdmin)</p>
        <p>Kick all players (SysAdmin)</p>
        <p>Set server to &quot;ready&quot;, player may log in(SysAdmin)</p>
        -->
        <p><a href='index.php?page=view_server_options'>View server options</a> (SysAdmin)</p>

<?PHP }
	if ($_GET['category'] == 'Other'){
		?>
	<p><a href="index.php?page=list_tips">View Tips</a>(SettingMember)</p>
	<p><a href="index.php?page=list_guilds">List guilds and members</a> (AnyMember)</p>
	<p><a href="index.php?page=list_petitions">List petitions</a> (AnyMember)</p>
	<p><a href="index.php?page=view_accounts">View accounts</a> (AnyMember)</p>
	<p><a href="index.php?page=view_gms">View/Edit gms</a> (Admins)</p>
	<p><a href="index.php?page=view_commands">View/Edit Command Groups</a> (Admins)</p>
	<p><a href="index.php?page=view_characters">View characters</a> (AnyMember)</p>
	<p><a href="index.php?page=list_traits">List/Edit traits</a> (SysAdmin)</p>
	<p><a href="index.php?page=list_commonstrings">List/Edit Common Strings</a> (AnyMember)</p>
<?PHP }
	?>	
</td></tr></table>
<?
}

?>
