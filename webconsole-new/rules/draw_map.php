<?php

/*
    This file gets called directly, and returns an image based on the parameters used to call it. Use this file as you would
    a .png file. (<img src="draw_map.php") 
    It is assumed an image file  called "sector_name.gif" is in the same directory as this file for every sector as listed in 
    commonfunctions.php getDataFromArea(). This file is different from the rest of the WC in the way it works, but that can't 
    be helped for now.
    methods are ?sector=sector&type=type where sector is the name of the zone, and type is what kind of image you want to draw 
    (resource, waypoint, location).
*/

    include('./../../secure/db_config.php');
    include('./../commonfunctions.php');
    session_save_path('../sessions');
    session_start();


    SetUpDB("$db_hostname", "$db_username", "$db_password", "$db_name");
    if (!isset($_SESSION['totalq']))
    {
        $_SESSION['totalq'] = "SQL Queries Performed:";
    }
    
    natural_resources_draw();
    
function natural_resources_draw()
{

    $sector = isset($_GET['sector']) ? $_GET['sector'] : '';
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    if (!checkAccess('rules', 'read'))  // you can't actually see these echo's if you don't type this file directly in your browser.
    {
        echo 'You do not have access to view this image!';
        return;
    }
    if ($sector == '' || $type == '')
    {
        echo 'You must supply a sector name and type.';
        return;
    }
    header("Content-type: image/png");
    
    
    draw_map($sector, $type);
    
}

function draw_map($sector, $type)
{
    $image_name = '../img/'.$sector.'.gif';

    $data = getDataFromArea($sector);
    $sectors = $data[0];
    $centerx = $data[1];
    $centery = $data[2];
    $scalefactorx = $data[3];
    $scalefactory = $data[4];

    #Open gif and copy into truecolor png.
    $gifim     = imagecreatefromgif($image_name);
    $w         = imagesx($gifim);
    $h         = imagesy($gifim);
    $im        = imagecreatetruecolor($w,$h);
    imagecopy($im,$gifim,0,0,0,0,$w,$h);

    $red        = imagecolorallocate($im, 255,   0,   0);
    $green      = imagecolorallocate($im, 128, 255,   0);
    $dark_green = imagecolorallocate($im,   0, 128,   0);
    $orange     = imagecolorallocate($im, 255, 128,   0);
    $gray       = imagecolorallocate($im, 228, 228, 228);
    $blue       = imagecolorallocate($im,   0,   0, 128);

    if ($type == 'location')
    {
        draw_locations($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$red);
    }
    elseif ($type == 'waypoint')
    {
        draw_waypoints($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$orange,$blue);
    }
    elseif ($type == 'path')
    {
        draw_paths($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$gray,$blue);
    }
    elseif ($type == 'resource')
    {
        draw_natural_resources($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$green,$dark_green);
    }
    elseif ($type == 'live')
    {
        draw_live_paths($im,$data[5],$centerx,$centery,$scalefactorx,$scalefactory, array($red, $green, $dark_green, $orange, $gray, $blue));
    }
    else // do nothing if we don't know the command.
    {
    }
    
    imagepng($im);
    imagedestroy($im);
}

function draw_natural_resources($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$fg_color,$bg_color){
    $query = "SELECT id,loc_x,loc_y,loc_z,radius,visible_radius from natural_resources where " . $sectors;
    $res = mysql_query2($query);

    // exit if there is no data
    $num = mysql_num_rows($res);
    if ($num==0)
      return;

    $i=0;
    while ($line = mysql_fetch_array($res, MYSQL_NUM)){
        $id          = $line[0];
        $x           = $line[1];
        $y           = $line[2];
        $z           = $line[3];
        $radius      = $line[4];
        $vis_radius  = $line[5];

	$ix = $centerx+($x*$scalefactorx);
	$iy = $centery-($z*$scalefactory);
        $ir = $radius*$scalefactorx;
        imagearc($im,$ix,$iy,$ir,$ir,0,360,$fg_color);

        $ivr = $vis_radius*$scalefactorx;
        imagearc($im,$ix,$iy,$ivr,$ivr,0,360,$bg_color);

    }
}

function draw_waypoints($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$fg_color,$fg_color_no_wander){
    $query = " select id,x,y,z,radius from sc_waypoints where " . $sectors;
    //echo $query;
    $res = mysql_query($query);

    // exit if there is no data
    $num = mysql_num_rows($res);
    if ($num==0)
      return;

    $i=0;
    while ($line = mysql_fetch_array($res, MYSQL_NUM)){
        $id      = $line[0];
        $x       = $line[1];
        $y       = $line[2];
        $z       = $line[3];
        $radius  = $line[4];

	$ix = $centerx+($x*$scalefactorx);
	$iy = $centery-($z*$scalefactory);
        $ir = $radius*$scalefactorx;
        imagearc($im,$ix,$iy,$ir,$ir,0,360,$fg_color);

        $query2 = "select wp1.x,wp1.y,wp1.z,wp2.x,wp2.y,wp2.z,l.flags,wp1.id,wp2.id from sc_waypoint_links l, sc_waypoints wp1, sc_waypoints wp2 where l.wp1 = wp1.id and l.wp2 = wp2.id and wp1.id = ".$id;
        $res2=mysql_query($query2); 
        while ($line2 = mysql_fetch_array($res2, MYSQL_NUM)){
            $x1 = $line2[0];
            $y1 = $line2[1];
            $z1 = $line2[2];
            $x2 = $line2[3];
            $y2 = $line2[4];
            $z2 = $line2[5];
            $flags = $line2[6];
            $id1 = $line2[7];
            $id2 = $line2[8];

            $ix1 = $centerx+($x1*$scalefactorx);
            $iy1 = $centery-($z1*$scalefactory);
            $ix2 = $centerx+($x2*$scalefactorx);
            $iy2 = $centery-($z2*$scalefactory);

            if (stristr($flags, 'NO_WANDER'))
            {
                $line_color = $fg_color_no_wander;
            } else
            { 
                $line_color = $fg_color;
            }
            if ($id1 == $id2)
            {
                imagearc($im,$ix1,$iy1+8,16,16,0,360,$fg_color);
                // Move arrow if oneway
                $iy1 += 16; 
                $iy2 += 16; 
            } else
            {
                imageline($im,$ix1,$iy1,$ix2,$iy2 , $line_color);
            }
            if (stristr($flags, 'ONEWAY') != FALSE)
            {
                $cx = ($ix1+$ix2)/2;
                $cy = ($iy1+$iy2)/2;
                $a = atan2($iy1-$iy2,$ix1-$ix2);
                $dx = 10*cos($a+0.8);
                $dy = 10*sin($a+0.8);
                imageline($im,$cx,$cy,$cx+$dx,$cy+$dy,$line_color);
                $dx = 10*cos($a-0.8);
                $dy = 10*sin($a-0.8);
                imageline($im,$cx,$cy,$cx+$dx,$cy+$dy,$line_color);
            }
        }
    }
}

function draw_paths($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$fg_color,$fg_color_no_wander){
    $query = " select id,x,y,z,prev_point from sc_path_points where " . $sectors;
    //echo $query;
    $res = mysql_query($query);

    // exit if there is no data
    $num = mysql_num_rows($res);
    if ($num==0)
      return;

    $i=0;
    while ($line = mysql_fetch_array($res, MYSQL_NUM)){
        $id      = $line[0];
        $x       = $line[1];
        $y       = $line[2];
        $z       = $line[3];
        $radius  = 5;

	$ix = $centerx+($x*$scalefactorx);
	$iy = $centery-($z*$scalefactory);
        $ir = $radius;
        imagearc($im,$ix,$iy,$ir,$ir,0,360,$fg_color);

	if ($line[4] != 0)
        {
            $query2 = "select p1.x,p1.y,p1.z,p2.x,p2.y,p2.z from sc_path_points p1, sc_path_points p2 where p1.id = p2.prev_point and p2.id = ".$id;
            $res2=mysql_query($query2); 
            while ($line2 = mysql_fetch_array($res2, MYSQL_NUM)){
                $x1 = $line2[0];
                $y1 = $line2[1];
                $z1 = $line2[2];
                $x2 = $line2[3];
                $y2 = $line2[4];
                $z2 = $line2[5];

                $ix1 = $centerx+($x1*$scalefactorx);
                $iy1 = $centery-($z1*$scalefactory);
                $ix2 = $centerx+($x2*$scalefactorx);
                $iy2 = $centery-($z2*$scalefactory);

                $line_color = $fg_color;
 
                imageline($im,$ix1,$iy1,$ix2,$iy2 , $line_color);
            }           
        }

    }
}

function draw_locations($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$fg_color){
    $query = "select id,x,y,z,radius,id_prev_loc_in_region from sc_locations where " . $sectors;
    $res = mysql_query($query);

    // exit if there is no data
    $num = mysql_num_rows($res);
    if ($num==0)
      return;

    $i=0;
    while ($line = mysql_fetch_array($res, MYSQL_NUM)){
        $id      = $line[0];
        $x       = $line[1];
        $y       = $line[2];
        $z       = $line[3];
        $radius  = $line[4];
        $id_prev = $line[5];

	$ix1 = $centerx+($x*$scalefactorx);
	$iy1 = $centery-($z*$scalefactory);
        $ir = $radius*$scalefactorx;
        imagearc($im,$ix1,$iy1,$ir,$ir,0,360,$fg_color);

	if ( $id_prev > 0 )
        {
	    $query2 = "select x,y,z from sc_locations where " . $sectors . " and id = " . $id_prev;
	    $res2 = mysql_query($query2);
            while ($line2 = mysql_fetch_array($res2, MYSQL_NUM)){
               $x2       = $line2[0];
               $y2       = $line2[1];
               $z2       = $line2[2];
 
	       $ix2 = $centerx+($x2*$scalefactorx);
               $iy2 = $centery-($z2*$scalefactory);
               imageline($im,$ix1,$iy1,$ix2,$iy2,$fg_color);
	    }
        }

    }
}

function draw_live_paths($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory, $colors){
	$colorindex = 0;

	$dir = "../../psserver/tracking/";
	if($files = @scandir($dir)) {
		foreach($files as $file) {
			if($file == '.' || $file == '..') continue;
			
			$handle = @fopen($dir.$file,"r");
			if($handle) {
				$prevx = 999.0;
				$prevy = 999.0;
				while(!feof($handle))
				{
					$buffer = fgets($handle, 4096);
					$pieces = explode(",", $buffer);
					$found = FALSE;
					foreach($sectors as $sectorname)
					{
						if(trim($pieces[2]) === $sectorname)
							$found = TRUE;
					}
					if($found)
					{
		
						$x = $centerx+($pieces[0]*$scalefactorx);
						$y = $centery-($pieces[1]*$scalefactory);
						if(!($prevx == 999.0 && $prevy == 999.0))
						{
							imageline($im,$prevx,$prevy,$x,$y,$colors[$colorindex]);
						}
						$prevx = $x;
						$prevy = $y;
					}
				}
				fclose($handle);
				// draw each npc as a different colour
				$colorindex = ($colorindex + 1) % count($colors);
			}
		}
	}
}

?>
