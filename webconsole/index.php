<?PHP
$path_session = 'sessions';
session_save_path($path_session);
session_start();
include ('config.php');
include ('commonfunctions.php');


$link = mysql_connect($db_hostname, $db_username, $db_password);
mysql_select_db($db_name, $link);

if (isset($_POST['autologin']))
{
	setcookie ("username", $username, time() + (60 * 60 * 24 * 30));
	setcookie ("password", $password, time() + (60 * 60 * 24 * 30));
}


if ($_SESSION['loggedin'] == 'yes' && (
	$_GET['page'] == 'npc_actions' || 
	$_GET['page'] == 'viewnpcLeftFrame' ||
	$_GET['page'] == 'viewkaLeftFrame' ||
	$_GET['page'] == 'adjustraces' ||
	$_GET['page'] == 'viewqstepLeftFrame' ||
	$_GET['page'] == 'merchant_actions' ||
	$_GET['page'] == 'trainer_actions' ||
	$_GET['page'] == 'syn_actions' ||
	$_GET['page'] == 'viewnpc' ||
	$_GET['page'] == 'viewqueststep'||
	$_GET['page'] == 'viewka' ||
	$_GET['page'] == 'ka_actions' ||
	$_GET['page'] == 'itemcategory_actions' ||
	$_GET['page'] == 'descworldactions' ||
	$_GET['page'] == 'tips_actions' ||
	$_GET['page'] == 'waypoints_draw' ||
	$_GET['page'] == 'locations_draw' ||
	$_GET['page'] == 'natural_resources_draw' ||
	$_GET['page'] == 'viewnpcdraw' ||
	$_GET['page'] == 'script_actions'))
    { 
	// those pages use frames, or only does actions so they should not output 
    //  header and footers this is not a clean way to do it , i need to clean 
    // it later
	// clean way should be to declare and call function  into the file that 
    // need it , not here. and use $_GET['action'] instead of $_GET['page'], 
    // this can lead to some potential exploits , but if eatch function begin 
    // by a checkaccess() like they should be , then the problem is solved. 
	include('login.php');
	login();

	switch ($_GET['page'])
    {
		case 'script_actions':
			include('./scripts/script_actions.php');
			script_actions();
            break;		
		
		case 'viewka':
			include('./kas/viewka.php');
			viewka();
            break;
	
		case 'npc_actions':
			include('./npcs/npc_actions.php');
			npc_actions();
            break;

		case 'viewnpcLeftFrame':
			include('./npcs/viewnpcLeftFrame.php');
			viewnpcLeftFrame();
            break;

		case 'viewkaLeftFrame':
			include('./kas/viewkaLeftFrame.php');
			viewkaLeftFrame();
            break;

		case 'adjustraces':
			include('adjustraces.php');
			adjustraces();
            break;

		case 'viewqstepLeftFrame':
			include('./quests/viewqstepLeftFrame.php');
			viewqstepLeftFrame();
            break;

		case 'merchant_actions':
			include('./merchants/merchant_actions.php');
			merchant_actions();	
            break;

		case 'trainer_actions':
			include('./trainers/trainers_actions.php');
			trainers_actions();	
            break;

		case 'syn_actions':
			include('./synonyms/syn_actions.php');
			syn_actions();
            break;
			
		case 'viewnpc':
			include('./npcs/viewnpc.php');
			viewnpc();
            break;	
			
		case 'viewqueststep':
			include('./quests/viewqueststep.php');
			viewqueststep();
            break;

		case 'ka_actions':
			include('./kas/ka_actions.php');
			ka_actions();
            break;

		case 'itemcategory_actions':
			include('./items/itemcategory_actions.php');
			itemcategory_actions();	
            break;		

		case 'descworldactions':
			include('./other/descworldactions.php');
			descworldactions();
            break;

		case 'tips_actions':
			include('./other/tips_actions.php');
			tips_actions();	
            break;

		case 'waypoints_draw':
			include('./waypoints/waypoints_draw.php');
			waypoints_draw();
            break;

		case 'natural_resources_draw':
			include('./natural_resources/natural_resources_draw.php');
			natural_resources_draw();
            break;

		case 'locations_draw':
			include('./locations/locations_draw.php');
			locations_draw();
            break;

		case 'viewnpcdraw':
			include('./npcs/viewnpcdraw.php');
			viewnpcdraw();
            break;

	    }
    }
    else
    {
	outputHtmlHeader();

	include('login.php');
	login();

	if ($_SESSION['loggedin'] == 'yes')
    {
		switch ($_GET['page'])
        {
			case 'viewnpcmap':
				include('./npcs/viewnpcmap.php');
				viewnpcmap();
                break;


			case 'searchnpc':
				include('./npcs/searchnpc.php');
				searchnpc();
                break;

			case 'listnpcs':
				include('./npcs/listnpcs.php');
				listnpcs(false);
                break;

			case 'listnpcquest':
				include('./npcs/listnpcquest.php');
				listnpcquest();
                break;

			case 'listnpcsinv':
				include('./npcs/listnpcs.php');
				listnpcs(true);
                break;

			case 'listnpcscombat':
				include('./npcs/listnpcscombat.php');
				listnpcscombat($_GET['sorting']);
                break;

			case 'listspawnrules':
				include('./npcs/listspawnrules.php');
				listspawnrules($_GET['selectedrule']);
                break;

			case 'spawnrules_actions':
				include('./npcs/spawnrules_actions.php');
				spawnrules_actions();
                break;

			case 'listsynonyms':
				include('./synonyms/listsynonyms.php');
				listsynonyms();
                break;

			case 'listkas':
				include('./kas/listkas.php');
				listkas();
                break;
			case 'listkascripts':
				include('./kas/listkascripts.php');
				listkascripts();
                break;
			case 'editkascript':
				include('./kas/editkascript.php');
				editkascript();
				break;
			case 'listmerchants':
				include('./merchants/listmerchants.php');
				listmerchants();
                break;

			case 'listtrainers':
				include('./trainers/listtrainers.php');
				listtrainers();
                break;

			case 'checktrainers':
				include('./trainers/checktrainers.php');
				checktrainers();
                break;

			case 'checknpctriggers':
				include('./npcs/checknpctriggers.php');
				checknpctriggers();
                break;

			case 'checknpcchar':
				include('./npcs/checknpcchar.php');
				checknpcchar();
                break;

			case 'checknpcloaded':
				include('./npcs/checknpcloaded.php');
				checknpcloaded();
                break;

			case 'findtrigger':
				include('./npcs/findtrigger.php');
				findtrigger();
                break;

			case 'listquests':
				include('./quests/listquests.php');
				listquests();
                break;

			case 'listquestscripts':
				include('./quests/listquestscripts.php');
				listquestscripts();
                break;

			case 'listitems':
				include('./items/listitems.php');
				listitems();
                break;
                        
            case 'newbaseitem':
                include('./items/createnewbase.php');
                break;

			case 'descworld':
				include('./other/descworldsectors.php');
				descworldsectors($_GET['sector']);
                break;

        	case 'checkbooks':
        		include('./other/checkbooks.php');
        		checkbooks();
                break;

			case 'createquest':
				include('./quests/createquest.php');
				createquest();
                break;

			case 'listitemsinstance':
				include('./items/listitemsinstance.php');
				listitemsinstance();
                break;

			case 'listscripts':
				include('./scripts/listscripts.php');
				listscripts($_GET['type']);
                break;

			case 'listspells':
				include('./spells/listspells.php');
				listspells();
                break;

			case 'whereusedglyph':
				include('./spells/listspells.php');
				whereusedglyph();
                break;

			case 'listskills':
				include('./skills/listskills.php');
				listskills();
                break;

			case 'skill_actions':
				include('./skills/skill_actions.php');
				skill_actions();

                break;
                       case 'listfactions':
                               include('./factions/listfactions.php');
                               listfactions();

                break;
                       case 'faction_actions':
                               include('./factions/faction_actions.php');
                               faction_actions();
                break;

                        case 'listnatural_resources':
                                include('./natural_resources/listnatural_resources.php');

                break;

			case 'listnatural_resources':
				include('./natural_resources/listnatural_resources.php');
				listnatural_resources($_GET['selected']);
                break;

			case 'natural_resources_actions':
				include('./natural_resources/natural_resources_actions.php');
				natural_resource_actions();
                break;

			case 'natural_resources_map':
				include('./natural_resources/natural_resources_map.php');
				natural_resources_map();
                break;

			case 'listwaypoints':
				include('./waypoints/listwaypoints.php');
				listwaypoints($_GET['selected']);
                break;

			case 'waypoints_actions':
				include('./waypoints/waypoints_actions.php');
				waypoints_actions();
                break;

			case 'waypoints_map':
				include('./waypoints/waypoints_map.php');
				waypoints_map();
                break;

			case 'listlocations':
				include('./locations/listlocations.php');
				listlocations($_GET['selected'],
                              $_GET['selectedtype'],
                              $_GET['selectedsector']);
                break;

			case 'locations_actions':
				include('./locations/locations_actions.php');
				locations_actions($_GET['selected'],
                                  $_GET['selectedtype'],
                                  $_GET['selectedsector']);
                break;

			case 'locations_map':
				include('./locations/locations_map.php');
				locations_map();
                break;

			case 'maincharcreate':
				include('maincharcreate.php');
				maincharcreate();
                break;

			case 'listitemcategories':
				include('./items/listitemcategories.php');
				listitemcategories();
                break;

			case 'quest_actions':
				include('./quests/quest_actions.php');
				quest_actions();
                break;

			case 'questscript_actions':
				include('./quests/questscript_actions.php');
				questscript_actions();
                break;

			case 'viewquest':
				include('./quests/viewquest.php');
				viewquest();
                break;

			case 'viewquestscript':
				include('./quests/viewquestscript.php');
				viewquestscript();
                break;
				
				
			case 'spell_actions':
				include('./spells/spell_actions.php');
				spell_actions();
                break;

			case 'assignquest':
				include('./quests/assignquest.php');
				assignquest();
                break;

			case 'list_tips':
				include('./other/list_tips.php');
				list_tips();
                break;

			case 'list_guilds':
				include('./other/list_guilds.php');
				list_guilds();
                break;
				
			case 'view_server_options':
				include('./server/view_server_options.php');
				view_server_options();	
                break;
				
			case 'list_petitions':
				include('./other/list_petitions.php');
				list_petitions();	
                break;
				
			case 'view_tradeskills':
				include('./tradeskills/view_tradeskills.php');
				view_tradeskills();	
                break;
			
			case 'view_characters':
				include('./other/view_characters.php');
				view_characters();	
                break;				
				
			case 'view_accounts':
				include('./other/view_accounts.php');
				view_accounts();	
                break;

			case 'view_gms':
				include('./other/view_gms.php');
				view_gms();	
                break;

			case 'view_commands':
				include('./other/view_commands.php');
				view_commands();	
                break;

			case 'logout':
			    display_login('no');
                break;	

		    case 'viewlootrule':
			    include('./npcs/viewlootrule.php');
			    viewlootrule();
                break;

		    case 'listlootcategories':
			    include('./npcs/listlootcategories.php');
			    listlootcategories($_GET['selectedloot']);
                break;

		    case 'lootcategories_actions':
			    include('./npcs/lootcategories_actions.php');
			    lootcategories_actions();
                break;

            case 'list_traits':
				include('./other/listtraits.php');
				show_races();
                break;

			case 'trait_actions':
				include('./other/trait_actions.php');
				trait_actions();
                break;

			case 'list_commonstrings':
				include('./other/listcommonstrings.php');
				list_commonstrings();
                break;

			case 'commonstring_actions':
				include('./other/commonstring_actions.php');
				commonstring_actions();
                break;

            case 'list_stat_group':
				include('./other/list_stats.php');
				show_stats();
                break;

			case 'view_statistic':
				include('./other/view_stats.php');
				view_stats();	
	            break;

			default:
				include('main_menu.php');
				main_menu();
		}
	} 
	
    // checkAccess("main", "", "read");
	outputHtmlFooter();
}
?>
