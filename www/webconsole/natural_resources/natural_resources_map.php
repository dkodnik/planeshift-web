<?
function natural_resources_map(){
  include('util.php');

  checkAccess('listnpc', '', 'read');

  $sector = $_POST['sector'];

  if ($sector!=null and $sector!="") {


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
echo "Natural Resources painted in green<br>";
echo "NPC Waypoints painted in orange<br>";
echo "NPC Locations painted in red<br><br>";

echo "<div id=Layer2 style=\"position:absolute; width:1968px; height:954px; z-index:1; left:0px; top:250px\">";    
echo "<img src=\"index.php?page=natural_resources_draw&sector=".$sector."\" >";

  $data = getDataFromArea($sector);
  $sectors = $data[0];
  $query = $query . " select id,loc_x,loc_y,loc_z, radius, visible_radius, probability,reward_nickname from natural_resources where " . $sectors;
  //echo $query;
  $res = mysql_query2($query);

  $i=0;
  while ($line = mysql_fetch_array($res, MYSQL_NUM)){
    $elem = $line[0] . "|R:" . $line[5] . " P:".$line[6]." I:".$line[7]."|x|" . $line[1]  . "|" . $line[3]."|".$line[6];
    $result .= $elem . "\n";
   }
// get each line
$tok = strtok($result, "\n");
while ($tok) {
   $peoples[]=$tok;
   $tok = strtok("\n");
}

// get all info for each line
foreach( $peoples as $people ) {

   // skips commented lines
   $pos = strstr($people, "#");

   if ($pos=="0") {
     $tok2 = strtok($people, "|");
     $infos[] = "";
     $count = 1;
     while ($tok2) {
      $tok2 = str_replace("\n", "", $tok2);
      $tok2 = str_replace("\r", "", $tok2);
      $infos[$count]=$tok2;
      $tok2 = strtok("|");
      $count++;
     }

    $centerx = $data[1];
    $centery = $data[2];
    $scalefactor = $data[3];

     $x= $centerx+($infos[4]*$scalefactor);
     $y= $centery-($infos[5]*$scalefactor);
     
     if ($infos[6]=="Y") {
      $ball = "ball01m.gif";
     echo "<div id=Layer1 onMouseover=\"ddrivetip('$infos[2]')\"; onMouseout=\"hideddrivetip()\" style=\"position:absolute; offsetTop:20px; width:10px; height:10px; z-index:2; left:".$x."px; top:".$y."px\">";
     echo "<A HREF=index.php?page=listnatural_resources&category={$_GET['category']}&selected=$infos[1]><img border=0 src=$ball width=8 height=8></a></div>\n";

      } else {
      $ball = "ball04m.gif";
     echo "<div id=Layer1 onMouseover=\"ddrivetip('$infos[2]')\"; onMouseout=\"hideddrivetip()\" style=\"position:absolute; offsetTop:20px; width:10px; height:10px; z-index:2; left:".$x."px; top:".$y."px\">";
     echo "<A HREF=index.php?page=listnatural_resources&category={$_GET['category']}&selected=$infos[1]><img border=0 src=$ball width=10 height=10></a></div>\n";
      }

  }
}


  }


  echo "  <FORM action=\"index.php?page=natural_resources_map&category={$_GET['category']}\" METHOD=POST>";
  echo "  <b>Select one area:</b> <br><br> Area: ";
  SelectAreas("","sector");
  echo " <br><br><INPUT type=submit value=view><br><br>";
  
  echo "</FORM>";

}

?>

      
      
      

