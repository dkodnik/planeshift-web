<?php
// *almost* identical to all the other /rules/*map.php files
// This file requires rules/ball01m.gif and rules/ball04m.gif to exist.
function natural_resources_map(){
  if (!checkAccess('rules', 'read'))
  {
    echo 'You do not have permission to use this page.';
    return;
  }

  $sector = (isset($_POST['sector']) ? $_POST['sector'] : '');

  if ($sector != null && $sector != '') {


?>
<script language="JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
// -->
</script>

<style type="text/css">
#dhtmltooltip{
position: absolute;
width: 150px;
border: 2px solid black;
padding: 2px;
background-color: lightyellow;
visibility: hidden;
z-index: 100;
/*Remove below line to remove shadow. Below line should always appear last within this CSS*/
filter: progid:DXImageTransform.Microsoft.Shadow(color=gray,direction=135);
}

</style>

</head>

<body bgcolor="#FFFFFF" text="#000000">


<div id="dhtmltooltip"></div>

<script type="text/javascript">

/***********************************************
* Cool DHTML tooltip script- � Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/

var offsetxpoint=-60 //Customize x offset of tooltip
var offsetypoint=20 //Customize y offset of tooltip
var ie=document.all
var ns6=document.getElementById && !document.all
var enabletip=false
if (ie||ns6)
var tipobj=document.all? document.all["dhtmltooltip"] : document.getElementById? document.getElementById("dhtmltooltip") : ""

function ietruebody(){
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

function ddrivetip(thetext, thecolor, thewidth){
if (ns6||ie){
if (typeof thewidth!="undefined") tipobj.style.width=thewidth+"px"
if (typeof thecolor!="undefined" && thecolor!="") tipobj.style.backgroundColor=thecolor
tipobj.innerHTML=thetext
enabletip=true
return false
}
}

function positiontip(e){
if (enabletip){
var curX=(ns6)?e.pageX : event.x+ietruebody().scrollLeft;
var curY=(ns6)?e.pageY : event.y+ietruebody().scrollTop;
//Find out how close the mouse is to the corner of the window
var rightedge=ie&&!window.opera? ietruebody().clientWidth-event.clientX-offsetxpoint : window.innerWidth-e.clientX-offsetxpoint-20
var bottomedge=ie&&!window.opera? ietruebody().clientHeight-event.clientY-offsetypoint : window.innerHeight-e.clientY-offsetypoint-20

var leftedge=(offsetxpoint<0)? offsetxpoint*(-1) : -1000

//if the horizontal distance isn't enough to accomodate the width of the context menu
if (rightedge<tipobj.offsetWidth)
//move the horizontal position of the menu to the left by it's width
tipobj.style.left=ie? ietruebody().scrollLeft+event.clientX-tipobj.offsetWidth+"px" : window.pageXOffset+e.clientX-tipobj.offsetWidth+"px"
else if (curX<leftedge)
tipobj.style.left="5px"
else
//position the horizontal position of the menu where the mouse is positioned
tipobj.style.left=curX+offsetxpoint+"px"

//same concept with the vertical position
if (bottomedge<tipobj.offsetHeight)
tipobj.style.top=ie? ietruebody().scrollTop+event.clientY-tipobj.offsetHeight-offsetypoint+"px" : window.pageYOffset+e.clientY-tipobj.offsetHeight-offsetypoint+"px"
else
tipobj.style.top=curY+offsetypoint+"px"
tipobj.style.visibility="visible"
}
}

function hideddrivetip(){
if (ns6||ie){
enabletip=false
tipobj.style.visibility="hidden"
tipobj.style.left="-1000px"
tipobj.style.backgroundColor=''
tipobj.style.width=''
}
}

document.onmousemove=positiontip;

</script>

<?PHP

echo "<h1>Natural Resources Map View $sector</h1>";

if (! extension_loaded('gd')) { echo "You need to install GD<BR>"; return; }
echo '<b>Legend:</b><br/>';
echo 'Natural Resources <img src=img/ball04m.gif><br/>';
echo 'Natural Resources which are also hunt locations <img src=img/ball01m.gif><br/>';
echo 'Hunt Locations <img src=img/ball02m.gif><br/>';
echo 'Natural Resources range painted in green<br/><br/>';


$restype = (isset($_POST['restype']) ? $_POST['restype'] : '');

echo "<div id=Layer2 style=\"position:relative; \">";    
//echo "<div id=Layer2 style=\"position:absolute; width:1968px; height:954px; z-index:1; left:0px; top:250px\">";    
echo "<img src=\"rules/draw_map.php?sector=$sector&type=resource\" >";

  $data = getDataFromArea($sector);
  $sectors = $data[0];
  $hunt_sectors = str_replace('loc_sector_id','sector',$data[0]);
  $result = '';

  // natural resources
  if ($restype=='nat' || $restype=='both') {
    $query = "SELECT id, loc_x, loc_y, loc_z, radius, visible_radius, probability, reward_nickname, amount from natural_resources where ".$sectors;
    //echo "query is $query";
    $res = mysql_query2($query);

    while ($line = mysql_fetch_array($res, MYSQL_NUM)){
      if ($line[8]!=0)
        $amount = 2; // indicates this is also an hunt location
      else
        $amount = 1; // indicates this is only a natural res
      $elem = $line[0] . "|R:" . $line[5] . " P:".$line[6]." I:".$line[7]."|x|" . $line[1]  . "|" . $line[3]."|".$line[6]."|".$amount;
      $result .= ($elem . "\n");
    }
  }

  // hunt locations
  if ($restype=='hunt' || $restype=='both') {
    $query = "SELECT h.id, h.x, h.y, h.z, h.`range`, h.`range`, 0, s.name from hunt_locations h, item_stats s where h.itemid=s.id and ".$hunt_sectors;
    //echo "query is $query";
    $res = mysql_query2($query);

    while ($line = mysql_fetch_array($res, MYSQL_NUM)){
      $elem = $line[0] . "|R:" . $line[5] . " P:".$line[6]." I:".$line[7]."|x|" . $line[1]  . "|" . $line[3]."|".$line[6]."|3|33";
      $result .= ($elem . "\n");
    }
  }

  // get each line
  $tok = strtok($result, "\n");
  $peoples = null;
  while ($tok !== false) {
     $peoples[]=$tok;
     $tok = strtok("\n");
  }

  
// get all info for each line
foreach((array) $peoples as $people) {

   // skips commented lines
   $pos = strstr($people, '#');

   if ($pos == '0') {
     $tok2 = strtok($people, '|');
     $infos[] = '';
     $count = 1;
     while ($tok2!=null) {
      $tok2 = str_replace("\n", '', $tok2);
      $tok2 = str_replace("\r", '', $tok2);
      $infos[$count]=$tok2;
      $tok2 = strtok('|');
      $count++;
     }

    $centerx = $data[1];
    $centery = $data[2];
    $scalefactorx = $data[3];
    $scalefactory = $data[4];

    $x = $centerx+($infos[4]*$scalefactorx);
    $y = $centery-($infos[5]*$scalefactory);
    
    // determine icon
    if ($infos[7] == 1) {
       $ball = 'img/ball04m.gif';
       $resourceurl = "resource";
    } else if ($infos[7] == 2) {
       $ball = 'img/ball01m.gif';
       $resourceurl = "resource";
    } else if ($infos[7] == 3) {
       $ball = 'img/ball02m.gif';
       $resourceurl = "resourcehunt";
    }

    echo "<div id=Layer1 onMouseover=\"ddrivetip('$infos[2]')\"; onMouseout=\"hideddrivetip()\" style=\"position:absolute; offsetTop:20px; width:10px; height:10px; z-index:2; left:".$x."px; top:".$y."px\">";
    echo "<A HREF=index.php?do=$resourceurl&id=$infos[1]><img border=0 src=$ball width=10 height=10></a></div>\n";


  }
}


}

 $sectors_list = PrepSelect('sector');
  echo '  <FORM action="index.php?do=resourcemap" METHOD=POST>';
  echo '  <b>Select one area:</b> <br><br> Area: ';
//  echo DrawSelectBox('sector', $sectors_list, 'sector', '', false);
  SelectAreas($sector,'sector');
  echo ' Type: <SELECT name=restype><OPTION value="nat" selected>natural resources</OPTION><OPTION value="hunt">hunt locations</OPTION><OPTION value="both">both</OPTION></SELECT>';
  echo ' <br><br><INPUT type=submit value=view><br><br>';
  echo '</FORM>';
  echo '</div>';

}

?>