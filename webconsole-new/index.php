<?php
  include('./../secure/db_config.php');
  include('./commonfunctions.php');
  SetUpDB("$db_hostname", "$db_username", "$db_password", "$db_name");
  session_save_path('sessions');
  session_start();
  date_default_timezone_set('UTC');
  if (!isset($_SESSION['totalq'])){
    $_SESSION['totalq'] = "SQL Queries Performed:";
  }
  StripInput();
  if (isset($_GET['logout'])){
    if (isset($_COOKIE['autologin'])){
      setcookie('autologin', "", time()-60*60*24*30);
      setcookie('autoname', "", time()-60*60*24*30);
      setcookie('autopass', "", time()-60*60*24*30);
    }
    setcookie('PHPSESSID', "", time()-60*60*24*30);
    session_unset();
    session_destroy();
    session_start();
  }
  if (!CheckLogin() && !DoLogin()){
    include('./header.php');
    echo '<div class="menu">';
    echo '<p class="error">You must be logged in to access these pages!</p>'."\n";
    echo '</div><div class="center"><p class="header">PlaneShift Administrator Console - Login</p>';
    DisplayLogin();
    echo '</div>';
    include('./footer.php');
    exit;
  }
  include('./header.php');
  CacheAccess();
  echo '<div class="menu">';
  if (checkaccess('npcs', 'read')){
    echo '<a href="./index.php?do=npcs">NPCs</a> -- ';
  }
  if (checkaccess('quests', 'read')){
    echo '<a href="./index.php?do=quests">Quests</a> -- ';
  }
  if (checkaccess('items', 'read')){
    echo '<a href="./index.php?do=items">Items</a> -- ';
  }
  if (checkaccess('als', 'read')){
    echo '<a href="./index.php?do=als">Action Locations</a> -- ';
  }
  if (checkaccess('rules','read')){
    echo '<a href="./index.php?do=rules">Rules</a>  -- ';
  }
  if (checkaccess('crafting', 'read')){
    echo '<a href="./index.php?do=crafting">Crafting</a> -- ';
  }
  if (checkaccess('other', 'read')){
    echo '<a href="./index.php?do=other">Other</a> -- ';
  }
  if (checkaccess('statistics', 'read')){
    echo '<a href="./index.php?do=statistics">Statistics</a> -- ';
  }
  if (checkaccess('assets', 'read')){
    echo '<a href="./index.php?do=assets">Assets</a> -- ';
  }
  if (checkaccess('admin', 'read')){
    echo '<a href="./index.php?do=admin">Admin</a> -- ';
  }
  echo '<a href="./index.php?logout">Logout</a>';
  echo '</div><hr/>';
  if (isset($_GET['do'])){
    switch ($_GET['do']){
      case 'quests':
        include('./quests/questmain.php');
        include('./quests/listquests.php');
        questmain();
        countquests();
        break;
      case 'listquests':
        include('./quests/questmain.php');
        include('./quests/listquests.php');
        questmain();
        listquests();
        countquests();
        break;
      case 'readquest':
        include('./quests/questmain.php');
        include('./quests/listquests.php');
        questmain();
        readquest();
        break;
      case 'validatequest':
        include('./quests/questmain.php');
        include('./quests/validatequest.php');
        questmain();
        validatequest();
        break;
      case 'editquest':
        include('./quests/questmain.php');
        include('./quests/editquest.php');
        questmain();
        editquest();
        break;
      case 'npcquests':
        include('./quests/questmain.php');
        include('./quests/listquests.php');
        questmain();
        npcquests();
        break;
      case 'createquest':
        include('./quests/questmain.php');
        include('./quests/createquest.php');
        questmain();
        createquest();
        break;
      case 'deletequest':
        include('./quests/questmain.php');
        include('./quests/deletequest.php');
        questmain();
        deletequest();
        break;
      case 'items':
        include('./items/itemmain.php');
        itemmain();
        break; 
      case 'listitems':
        include('./items/itemmain.php');
        include('./items/listitems.php');
        itemmain();
        listitems();
        break;
      case 'listitemicons':
        include('./items/itemmain.php');
        include('./items/listitemicons.php');
        itemmain();
        listitemicons();
        break;
      case 'showitemusage':
        include('./items/itemmain.php');
        include('./items/listitems.php');
        itemmain();
        showitemusage();
        break;
      case 'edititem':
        include('./items/itemmain.php');
        include('./items/edititem.php');
        itemmain();
        edititem();
        break;
      case 'deleteitem':
        include('./items/itemmain.php');
        include('./items/deleteitem.php');
        itemmain();
        deleteitem();
        break;
      case 'createitem':
        include('./items/itemmain.php');
        include('./items/createitem.php');
        itemmain();
        createitem();
        break;
      case 'editcategory':
        include('./items/itemmain.php');
        include('./items/editcategory.php');
        itemmain();
        editcategory();
        break;
      case 'deletecategory':
        include('./items/itemmain.php');
        include('./items/deletecategory.php');
        itemmain();
        deletecategory();
        break;
      case 'createcategory':
        include('./items/itemmain.php');
        include('./items/editcategory.php');
        itemmain();
        createcategory();
        break;
      case 'finditem':
        include('./items/itemmain.php');
        include('./items/locateitem.php');
        itemmain();
        locateitem();
        break;
      case 'als':
        include('./actionlocations/almain.php');
        include('./actionlocations/als.php');
        almain();
        alsector();
        break;
      case 'alsector':
        include('./actionlocations/almain.php');
        include('./actionlocations/als.php');
        almain();
        alsector();
        break;
      case 'listals':
        include('./actionlocations/almain.php');
        include('./actionlocations/als.php');
        almain();
        listals();
        break;
      case 'edital':
        include('./actionlocations/almain.php');
        include('./actionlocations/als.php');
        almain();
        edital();
        break;
      case 'deleteal':
        include('./actionlocations/almain.php');
        include('./actionlocations/als.php');
        almain();
        deleteal();
        break;
      case 'checkbooks':
        include('./actionlocations/almain.php');
        include('./actionlocations/checkbooks.php');
        almain();
        checkbooks();
        break;
      case 'npcs':
        include('./npcs/npcmain.php');
        npcmain();
        break;
      case 'listnpcs':
        include('./npcs/npcmain.php');
        include('./npcs/listnpcs.php');
        npcmain();
        listnpcs('true');
        break;
      case 'listvuln':
        include('./npcs/npcmain.php');
        include('./npcs/listnpcs.php');
        npcmain();
        listnpcs('false');
        break;
      case 'listnpcscombat':
        include('./npcs/npcmain.php');
        include('./npcs/listnpcscombat.php');
        npcmain();
        listnpcscombat();
        break;
      case 'listspawn':
        include('./npcs/npcmain.php');
        include('./npcs/listspawnrules.php');
        npcmain();
        listspawnrules();
        break;
      case 'editspawnrule':
        include('./npcs/npcmain.php');
        include('./npcs/listspawnrules.php');
        npcmain();
        editspawnrule();
        break;
      case 'listloot':
        include('./npcs/npcmain.php');
        include('./npcs/listlootrules.php');
        npcmain();
        listlootrules();
        break;
      case 'editlootruledetail':
        include('./npcs/npcmain.php');
        include('./npcs/listlootrules.php');
        npcmain();
        editlootruledetail();
        break;
      case 'createlootruledetail':
        include('./npcs/npcmain.php');
        include('./npcs/listlootrules.php');
        npcmain();
        createlootruledetail();
        break;
      case 'editlootrule':
        include('./npcs/npcmain.php');
        include('./npcs/listlootrules.php');
        npcmain();
        editlootrule();
        break;
      case 'listmerchant':
        include('./npcs/npcmain.php');
        include('./npcs/listmerchant.php');
        npcmain();
        listmerchant();
        break;
      case 'editmerchant':
        include('./npcs/npcmain.php');
        include('./npcs/listmerchant.php');
        npcmain();
        editmerchant();
        break;
      case 'listtrainer':
        include('./npcs/npcmain.php');
        include('./npcs/listtrainer.php');
        npcmain();
        listtrainer();
        break;
      case 'edittrainer':
        include('./npcs/npcmain.php');
        include('./npcs/listtrainer.php');
        npcmain();
        edittrainer();
        break;
      case 'npc_details':
        include('./npcs/npcmain.php');
        include('./npcs/npc_details.php');
        npcmain();
        npcdetails();
        break;
      case 'searchnpc':
        include('./npcs/npcmain.php');
        include('./npcs/npc_search.php');
        npcmain();
        npc_search();
        break;
      case 'synonyms':
        include('./npcs/npcmain.php');
        include('./npcs/synonyms.php');
        npcmain();
        synonyms();
        break;
      case 'ka_trigg':
        include('./npcs/npcmain.php');
        include('./npcs/ka_trigger.php');
        npcmain();
        ka_trigger();
        break;
      case 'ka_detail':
        include('./npcs/npcmain.php');
        include('./npcs/ka_trigger.php');
        npcmain();
        ka_detail();
        break;
      case 'ka_scripts':
        include('./npcs/npcmain.php');
        include('./npcs/ka_scripts.php');
        npcmain();
        ka_scripts();
        break;
      case 'findtrigger':
        include('./npcs/npcmain.php');
        include('./npcs/findtrigger.php');
        npcmain();
        findtrigger();
        break;
      case 'checknpctriggers':
        include('./npcs/npcmain.php');
        include('./npcs/checknpctriggers.php');
        npcmain();
        checknpctriggers();
        break;
      case 'checknpcloaded':
        include('./npcs/npcmain.php');
        include('./npcs/checknpcloaded.php');
        npcmain();
        checknpcloaded();
        break;
      case 'checktrainers':
        include('./npcs/npcmain.php');
        include('./npcs/checktrainers.php');
        npcmain();
        checktrainers();
        break;
      case 'checknpcchar':
        include('./npcs/npcmain.php');
        include('./npcs/checknpcchar.php');
        npcmain();
        checknpcchar();
        break;
      case 'viewnpcmap':
        include('./npcs/npcmain.php');
        include('./npcs/viewnpcmap.php');
        npcmain();
        viewnpcmap();
        break;
      case 'rules':
        include('./rules/rulesmain.php');
        rulesmain();
        break;
      case 'scripts':
        include('./rules/rulesmain.php');
        include('./rules/scripts.php');
        rulesmain();
        rule_scripts();
        break;
      case 'mscripts':
        include('./rules/rulesmain.php');
        include('./rules/mscripts.php');
        rulesmain();
        rule_mscripts();
        break;
      case 'spells':
        include('./rules/rulesmain.php');
        include('./rules/spells.php');
        rulesmain();
        spells();
        break;
      case 'spell':
        include('./rules/rulesmain.php');
        include('./rules/spells.php');
        rulesmain();
        spell();
        break;
      case 'listglyph':
        include('./rules/rulesmain.php');
        include('./rules/glyphs.php');
        rulesmain();
        glyphs_used();
        break;
      case 'createspell':
        include('./rules/rulesmain.php');
        include('./rules/spells.php');
        rulesmain();
        createspell();
        break;
      case 'resource':
        include('./rules/rulesmain.php');
        include('./rules/resources.php');
        rulesmain();
        listresources();
        break;
      case 'resourcemap':
        include('./rules/rulesmain.php');
        include('./rules/resourcemap.php');
        rulesmain();
        natural_resources_map();
        break;
      case 'waypoint':
        include('./rules/rulesmain.php');
        include('./rules/waypoints.php');
        rulesmain();
        listwaypoints();
        break;
      case 'listwaypointlinks':
        include('./rules/rulesmain.php');
        include('./rules/waypointlinks.php');
        rulesmain();
        listwaypointlinks();
        break;
      case 'editwaypointlink':
        include('./rules/rulesmain.php');
        include('./rules/waypointlinks.php');
        rulesmain();
        editwaypointlink();
        break;
      case 'createwaypointlink':
        include('./rules/rulesmain.php');
        include('./rules/waypointlinks.php');
        rulesmain();
        createwaypointlink();
        break;
      case 'deletewaypointlink':
        include('./rules/rulesmain.php');
        include('./rules/waypointlinks.php');
        rulesmain();
        deletewaypointlink();
        break;
      case 'waypointmap':
        include('./rules/rulesmain.php');
        include('./rules/waypointmap.php');
        rulesmain();
        waypoint_map();
        break;
      case 'listpathpoints':
        include('./rules/rulesmain.php');
        include('./rules/pathpoints.php');
        rulesmain();
        listpathpoints();
        break;
      case 'editpathpoint':
        include('./rules/rulesmain.php');
        include('./rules/pathpoints.php');
        rulesmain();
        editpathpoint();
        break;
      case 'createpathpoint':
        include('./rules/rulesmain.php');
        include('./rules/pathpoints.php');
        rulesmain();
        createpathpoint();
        break;
      case 'deletepathpoint':
        include('./rules/rulesmain.php');
        include('./rules/pathpoints.php');
        rulesmain();
        deletepathpoint();
        break;
      case 'location':
        include('./rules/rulesmain.php');
        include('./rules/locations.php');
        rulesmain();
        listlocations();
        break;
      case 'locationmap':
        include('./rules/rulesmain.php');
        include('./rules/locationmap.php');
        rulesmain();
        location_map();
        break;
      case 'skills':
        include('./rules/rulesmain.php');
        include('./rules/skills.php');
        rulesmain();
        listskills();
        break;
      case 'factions':
        include('./rules/rulesmain.php');
        include('./rules/factions.php');
        rulesmain();
        listfactions();
        break;
      case 'raceinfo':
        include('./rules/rulesmain.php');
        include('./rules/raceinfo.php');
        rulesmain();
        raceinfo();
        break;
      case 'crafting':
        include('./crafting/craftingmain.php');
        craftingmain();
        break;
      case 'listpatterns':
        include('./crafting/craftingmain.php');
        include('./crafting/patterns.php');
        craftingmain();
        listpatterns();
        break;
      case 'editpattern':
        include('./crafting/craftingmain.php');
        include('./crafting/patterns.php');
        craftingmain();
        editpattern();
        break;
      case 'createpattern':
        include('./crafting/craftingmain.php');
        include('./crafting/patterns.php');
        craftingmain();
        createpattern();
        break;
      case 'deletepattern':
        include('./crafting/craftingmain.php');
        include('./crafting/patterns.php');
        craftingmain();
        deletepattern();
        break;
      case 'editcombine':
        include('./crafting/craftingmain.php');
        include('./crafting/combines.php');
        craftingmain();
        editcombine();
        break;
      case 'createcombine':
        include('./crafting/craftingmain.php');
        include('./crafting/combines.php');
        craftingmain();
        createcombine();
        break;
      case 'deletecombine':
        include('./crafting/craftingmain.php');
        include('./crafting/combines.php');
        craftingmain();
        deletecombine();
        break;
      case 'transform':
        include('./crafting/craftingmain.php');
        include('./crafting/transforms.php');
        craftingmain();
        edittransform();
        break;
      case 'createtransform':
        include('./crafting/craftingmain.php');
        include('./crafting/transforms.php');
        craftingmain();
        createtransform();
        break;
      case 'deletetransform':
        include('./crafting/craftingmain.php');
        include('./crafting/transforms.php');
        craftingmain();
        deletetransform();
        break;
      case 'listprocess':
        include('./crafting/craftingmain.php');
        include('./crafting/process.php');
        craftingmain();
        listprocess();
        break;
      case 'process':
        include('./crafting/craftingmain.php');
        include('./crafting/process.php');
        craftingmain();
        editprocess();
        break;
      case 'editsubprocess':
        include('./crafting/craftingmain.php');
        include('./crafting/process.php');
        craftingmain();
        editsubprocess();
        break;
      case 'createprocess':
        include('./crafting/craftingmain.php');
        include('./crafting/process.php');
        craftingmain();
        createprocess();
        break;
      case 'deleteprocess':
        include('./crafting/craftingmain.php');
        include('./crafting/process.php');
        craftingmain();
        deleteprocess();
        break;
      case 'other':
        include('./other/othermain.php');
        othermain();
        break;
      case 'statistics':
        include('./statistics/statsmain.php');
        statsmain();
        break;
      case 'statshardware':
        include('./statistics/statsmain.php');
        include('./statistics/statshardware.php');
        statsmain();
        statshardware();
        break;
      case 'liststats':
        include('./statistics/statsmain.php');
        include('./statistics/liststats.php');
        statsmain();
        liststats();
        break;
      case 'liststats_retention':
        include('./statistics/statsmain.php');
        include('./statistics/liststats_retention.php');
        statsmain();
        liststats_retention();
        break;
      case 'liststats_charstats':
        include('./statistics/statsmain.php');
        include('./statistics/liststats_charstats.php');
        statsmain();
        liststats_charstats();
        break;
      case 'assets':
        include('./assets/assetsmain.php');
        assetsmain();
        break;
	case 'assetsnpc':
        include('./assets/assetsmain.php');
        include('./assets/assetsnpc.php');
        assetsmain();
        assetsnpc();
        break;
	case 'assetsitem':
        include('./assets/assetsmain.php');
        include('./assets/assetsitem.php');
        assetsmain();
        assetsitem();
        break;
	case 'listguilds':
        include('./other/othermain.php');
        include('./other/guilds.php');
        othermain();
        listguilds();
        break;
      case 'editguildmember':
        include('./other/othermain.php');
        include('./other/guilds.php');
        othermain();
        editguildmember();
        break;
      case 'deleteguildmember':
        include('./other/othermain.php');
        include('./other/guilds.php');
        othermain();
        deleteguildmember();
        break;
      case 'listpetitions':
        include('./other/othermain.php');
        include('./other/listpetitions.php');
        othermain();
        listpetitions();
        break;
        /*  commented pending a decision on what to do with this since there is no more such table, but the information is still out there.
      case 'listcommonstrings':
          include('./other/othermain.php');
          include('./other/commonstrings.php');
          othermain();
          listcommonstrings();
          break;
      case 'addcommonstrings':
        include('./other/othermain.php');
        include('./other/commonstrings.php');
        othermain();
        addcommonstrings();
        break;
      case 'editcommonstrings':
        include('./other/othermain.php');
        include('./other/commonstrings.php');
        othermain();
        editcommonstrings();
        break;
      case 'deletecommonstrings':
        include('./other/othermain.php');
        include('./other/commonstrings.php');
        othermain();
        deletecommonstrings();
        break;
        */
      case 'listaccounts':
        include('./other/othermain.php');
        include('./other/accounts.php');
        othermain();
        listaccounts();
        break;
      case 'viewaccount':
        include('./other/othermain.php');
        include('./other/accounts.php');
        othermain();
        viewaccount();
        break;
      case 'listcharacters':
        include('./other/othermain.php');
        include('./other/characters.php');
        othermain();
        listcharacters();
        break;
      case 'viewcharacter':
        include('./other/othermain.php');
        include('./other/characters.php');
        othermain();
        viewcharacter();
        break;
      case 'listtraits':
        include('./other/othermain.php');
        include('./other/traits.php');
        othermain();
        list_traits();
        break;
      case 'showraces':
        include('./other/othermain.php');
        include('./other/traits.php');
        othermain();
        show_races();
        break;
      case 'trait_actions':
        include('./other/othermain.php');
        include('./other/traits.php');
        othermain();
        trait_actions();
        break;
      case 'handletrait':
        include('./other/othermain.php');
        include('./other/traits.php');
        othermain();
        handle_trait();
        break;
      case 'admin':
        include('./admin/adminmain.php');
        adminmain();
        break; 
      case 'listtips':
        include('./admin/adminmain.php');
        include('./admin/tips.php');
        adminmain();
        listtips();
        break;
      case 'edittips':
        include('./admin/adminmain.php');
        include('./admin/tips.php');
        adminmain();
        edittips();
        break;
      case 'viewcommands':
        include('./admin/adminmain.php');
        include('./admin/viewcommands.php');
        adminmain();
        viewcommands();
        break;
      case 'deletecommand':
        include('./admin/adminmain.php');
        include('./admin/viewcommands.php');
        adminmain();
        deletecommand();
        break;
       case 'createcommand':
        include('./admin/adminmain.php');
        include('./admin/viewcommands.php');
        adminmain();
        createcommand();
        break;
      case 'viewserveroptions':
        include('./admin/adminmain.php');
        include('./admin/viewserveroptions.php');
        adminmain();
        viewserveroptions();
        break;
      case 'listgms':
        include('./admin/adminmain.php');
        include('./admin/gms.php');
        adminmain();
        listgms();
        break;
      case 'viewgmlog':
        include('./admin/adminmain.php');
        include('./admin/gms.php');
        adminmain();
        viewgmlog();
        break;
      case 'addgm':
        include('./admin/adminmain.php');
        include('./admin/gms.php');
        adminmain();
        addgm();
        break;
      case 'editgm':
        include('./admin/adminmain.php');
        include('./admin/gms.php');
        adminmain();
        editgm();
        break;
      case 'events';
      	include('./other/othermain.php');
      	include('./other/events.php');
      	othermain();
      	listevents();
      	break;
      case 'viewevent';
      	include('./other/othermain.php');
      	include('./other/events.php');
      	othermain();
      	viewevent();
      	break;
      default:
        echo '<p class="error">shouldn\'t reach this!</p>';
    }
  }
  else
  {
    echo '<div class="main">';
    echo "Server Information:<br/>\n";
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        echo "cannot display server status on Windows hosts.";
    }
    else 
    {
        exec("ps -eo user,etime,%mem,comm|grep psserver", $info);
        if (count($info) == 0)
        {
            echo '<p class="error">ERROR: psserver does not appear to be running</p>';
        }
        else
        {
            foreach ($info as $i)
            {
                echo $i."<br/>\n";
            }
        }
        echo "NPC Client Information:<br/>\n";
        unset($info);
        exec("ps -eo user,etime,%mem,comm|grep psnpcclient", $info);
        if (count($info) == 0)
        {
            echo '<p class="error">ERROR: npcclient does not appear to be running</p>';
        }
        else
        {
            foreach ($info as $i){
                echo $i."<br/>\n";
            }
        }
        echo "Server Load:<br/>\n";
        echo exec("uptime")."<br/>\n";
        echo "Mysql Status:<br/>\n";
        echo exec("mysqladmin status")."<br/>\n";
    }
  }
  echo "</div><hr/>This is Debugging Information Only: ".($_SESSION['totalq']);
  unset($_SESSION['totalq']);
  include('./footer.php');
  exit;
?>
