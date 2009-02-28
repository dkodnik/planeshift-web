<?
  include('./../secure/db_config.php');
  include('./commonfunctions.php');
  SetUpDB("$db_hostname", "$db_username", "$db_password", "$db_name");
  session_save_path('sessions');
  session_start();
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
    echo '<a href="./index.php?do=quests">quests</a> -- ';
  }
  if (checkaccess('items', 'read')){
    echo '<a href="./index.php?do=items">items</a> -- ';
  }
  if (checkaccess('als', 'read')){
    echo '<a href="./index.php?do=als">Action Locations</a> -- ';
  }
  if (checkaccess('rules','read')){
    echo '<a href="./index.php?do=rules">Rules</a>  -- ';
  }
  if (checkaccess('crafting', 'read')){
    echo '<a href="./index.php?do=crafting">crafting</a> -- ';
  }
  if (checkaccess('other', 'read')){
    echo 'other -- ';
  }
  if (checkaccess('admin', 'read')){
    echo 'admin -- ';
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
     case 'waypoint':
        include('./rules/rulesmain.php');
        include('./rules/waypoints.php');
        rulesmain();
        listwaypoints();
        break;
      case 'location':
        include('./rules/rulesmain.php');
        include('./rules/locations.php');
        rulesmain();
        listlocations();
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
      case 'transform':
        include('./crafting/craftingmain.php');
        include('./crafting/transforms.php');
        craftingmain();
        edittransform();
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
      default:
        echo '<p class="error">shouldn\'t reach this!</p>';
    }
  }else{
    echo '<div class="main">';
    echo "Server Information:<br/>\n";
    exec("ps -eo user,etime,%mem,comm|grep psserver", $info);
    if (count($info) == 0){
      echo '<p class="error">ERROR: psserver does not appear to be running</p>';
    }else{
      foreach ($info as $i){
        echo $i."<br/>\n";
      }
    }
    echo "NPC Client Information:<br/>\n";
    unset($info);
    exec("ps -eo user,etime,%mem,comm|grep psnpcclient", $info);
    if (count($info) == 0){
      echo '<p class="error">ERROR: npcclient does not appear to be running</p>';
    }else{
      foreach ($info as $i){
        echo $i."<br/>\n";
      }
    }
    echo "Server Load:<br/>\n";
    echo exec("uptime")."<br/>\n";
    echo "Mysql Status:<br/>\n";
    echo exec("mysqladmin status")."<br/>\n";
  }
  echo "</div><hr/>This is Debugging Information Only: ".($_SESSION['totalq']);
  unset($_SESSION['totalq']);
  include('./footer.php');
  exit;
?>
