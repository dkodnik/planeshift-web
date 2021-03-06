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
    
    natural_resources_draw();
    
function natural_resources_draw()
{

    $sector = isset($_GET['sector']) ? $_GET['sector'] : '';
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    if (!checkAccess('natres', 'read'))  // you can't actually see these echo's if you don't type this file directly in your browser.
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

// THIS FUNCTION WILL WORK ONLY IF THE INCLUDE ABOVE DO NOT ADD ANY EXTRA SPACES TO THE PAGE
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
    $dark_green = imagecolorallocate($im,  64, 180,   0);
    $orange     = imagecolorallocate($im, 255, 128,   0);
    $gray       = imagecolorallocate($im, 228, 228, 228);
    $blue       = imagecolorallocate($im,   0,   0, 128);
    $brown      = imagecolorallocate($im, 165,  42,  42);
    $cyan       = imagecolorallocate($im,   0, 255, 255);

    $type = strtolower($type);

    if (strpos($type,'location')!==FALSE)
    {
        draw_locations($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$red);
    }
    if (strpos($type, 'waypoint')!==FALSE)
    {
        draw_waypoints($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$orange,$blue);
    }
    if (strpos($type, 'path')!==FALSE)
    {
        draw_paths($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$gray,$orange);
    }
    if (strpos($type, 'resource')!==FALSE)
    {
        draw_natural_resources($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$green,$dark_green);
    }
    if (strpos($type, 'spawn')!==FALSE)
    {
        draw_spawn($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$brown,$cyan);
    }
    if (strpos($type, 'tribe')!==FALSE)
    {
        draw_tribe($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$blue,$dark_green);
    }
    if (strpos($type, 'live')!==FALSE)
    {
        draw_live_paths($im,$data[5],$centerx,$centery,$scalefactorx,$scalefactory, array($red, $green, $dark_green, $orange, $gray, $blue));
    }
    
    imagepng($im);
    imagedestroy($im);
}

function draw_natural_resources($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$fg_color,$bg_color){
    $query = "SELECT id,loc_x,loc_y,loc_z,radius,visible_radius from natural_resources where " . $sectors;
    $res = mysql_query2($query);

    // exit if there is no data
    $num = sqlNumRows($res);
    if ($num==0)
      return;

    $i=0;
    while ($line = fetchSqlRow($res)){
        $id          = $line[0];
        $x           = $line[1];
        $y           = $line[2];
        $z           = $line[3];
        $radius      = $line[4];
        $vis_radius  = $line[5];

        $ix = (($x-$centerx)*$scalefactorx)/100;
        $iy = (($y-$centery)*$scalefactory)/100;
        $ir = $radius*$scalefactorx;
        imagearc($im,$ix,$iy,2*$ir,2*$ir,0,360,$fg_color);

        $ivr = $vis_radius*$scalefactorx;
        imagearc($im,$ix,$iy,2*$ivr,2*$ivr,0,360,$bg_color);

    }
}

function draw_spawn($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$fg_color,$bg_color){
    $spawn_sectors = str_replace("loc_sector_id","sector_id",$sectors);
    $query = "SELECT id,x1,y1,z1,x2,y2,z2,radius,range_type_code FROM npc_spawn_ranges WHERE ". $spawn_sectors;
    $res = mysql_query2($query);

    // exit if there is no data
    $num = sqlNumRows($res);
    if ($num==0)
      return;

    while ($line = fetchSqlRow($res)){
        $id          = $line[0];
        $x1          = $line[1];
        $y1          = $line[2];
        $z1          = $line[3];
        $x2          = $line[4];
        $y2          = $line[5];
        $z2          = $line[6];
        $radius      = $line[7];
        $range_type  = $line[8];

	$ix1 = (($x1-$centerx)*$scalefactorx)/100;
	$iy1 = (($y1-$centery)*$scalefactory)/100;
	$ix2 = (($x2-$centerx)*$scalefactorx)/100;
	$iy2 = (($y2-$centery)*$scalefactory)/100;
        $ir = $radius*$scalefactorx;
        if ($range_type == "C") // Circle
        {
            imagearc($im,$ix1,$iy1,2*$ir,2*$ir,0,360,$fg_color);
        } else if ($range_type == "A") // Area
        {
            imageline($im,$ix1,$iy1,$ix1,$iy2 , $fg_color);
            imageline($im,$ix1,$iy2,$ix2,$iy2 , $fg_color);
            imageline($im,$ix2,$iy2,$ix2,$iy1 , $fg_color);
            imageline($im,$ix2,$iy1,$ix1,$iy1 , $fg_color);
        } else if ($range_type == "L") // Line with round edges
        {
            $a = atan2($iy1-$iy2,$ix1-$ix2);
            $dx = $ir*cos($a+M_PI_2);
            $dy = $ir*sin($a+M_PI_2);
            imagearc($im,$ix1,$iy1,2*$ir,2*$ir,rad2deg($a)-90,rad2deg($a)+90,$fg_color);
            imageline($im,$ix1-$dx,$iy1-$dy,$ix2-$dx,$iy2-$dy , $fg_color);
            imageline($im,$ix1+$dx,$iy1+$dy,$ix2+$dx,$iy2+$dy , $fg_color);
            imagearc($im,$ix2,$iy2,2*$ir,2*$ir,rad2deg($a)+90,rad2deg($a)-90,$fg_color);
        }
    }

}

function draw_tribe($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$fg_color,$bg_color){
    $tribe_sectors = str_replace("loc_sector_id","home_sector_id",$sectors);
    $query = "SELECT id,home_x,home_y,home_z,home_radius from tribes where ". $tribe_sectors;
    $res = mysql_query2($query);

    // exit if there is no data
    $num = sqlNumRows($res);
    if ($num==0)
      return;

    $i=0;
    while ($line = fetchSqlRow($res)){
        $id          = $line[0];
        $x           = $line[1];
        $y           = $line[2];
        $z           = $line[3];
        $radius      = $line[4];

	$ix = (($x-$centerx)*$scalefactorx)/100;
	$iy = (($y-$centery)*$scalefactory)/100;
        $ir = $radius*$scalefactorx;
        imagearc($im,$ix,$iy,2*$ir,2*$ir,0,360,$fg_color);
    }
}

function draw_waypoints($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$fg_color,$fg_color_no_wander){
    $query = " select id,x,y,z,radius,flags from sc_waypoints where " . $sectors;
    //echo $query;
    $res = mysql_query2($query);

    // exit if there is no data
    $num = sqlNumRows($res);
    if ($num==0)
      return;

    $i=0;
    while ($line = fetchSqlRow($res)){
        $id      = $line[0];
        $x       = $line[1];
        $y       = $line[2];
        $z       = $line[3];
        $radius  = $line[4];
        $flags   = $line[5];

        $ix = (($x-$centerx)*$scalefactorx)/100;
        $iy = (($y-$centery)*$scalefactory)/100;
        $ir = $radius*$scalefactorx;

        $style = array($fg_color,$fg_color,$fg_color);

        if (stristr($flags, "PRIVATE") !== FALSE)
        {
           $col = imagecolorallocate($im, 255, 0, 0); // Red
           $style = array_merge($style,array($col,$col,$col,$col));
        }
        if (stristr($flags, "PUBLIC") !== FALSE)
        {
           $col = imagecolorallocate($im, 0, 255, 0); // Green
           $style = array_merge($style,array($col,$col,$col,$col));
        }
        if (stristr($flags, "UNDERGROUND") !== FALSE)
        {
           $col = imagecolorallocate($im, 128, 128, 128); // Gray
           $style = array_merge($style,array($col,$col,$col,$col));
        }
        if (stristr($flags, "UNDERWATER") !== FALSE)
        {
           $col = imagecolorallocate($im, 0, 0, 255); // Blue
           $style = array_merge($style,array($col,$col,$col,$col));
        }
        if (stristr($flags, "CITY") !== FALSE)
        {
           $col = imagecolorallocate($im, 238, 130, 238); // Violet
           $style = array_merge($style,array($col,$col,$col,$col));
        }
        if (stristr($flags, "INDOOR") !== FALSE)
        {
           $col = imagecolorallocate($im, 255,192,203); // Pink
           $style = array_merge($style,array($col,$col,$col,$col));
        }
        if (stristr($flags, "PATH") !== FALSE)
        {
           $col = imagecolorallocate($im, 165, 42, 42); // Brown
           $style = array_merge($style,array($col,$col,$col,$col));
        }
        if (stristr($flags, "ROAD") !== FALSE)
        {
           $col = imagecolorallocate($im, 0, 0, 0); // Black
           $style = array_merge($style,array($col,$col,$col,$col));
        }
        if (stristr($flags, "GROUND") !== FALSE)
        {
           $col = imagecolorallocate($im, 0, 100, 0); // Dark Green
           $style = array_merge($style,array($col,$col,$col,$col));
        }

        imagesetstyle($im,$style);
        imagearc($im,$ix,$iy,2*$ir,2*$ir,0,360,IMG_COLOR_STYLED);

        $query2 = "select wp1.x,wp1.y,wp1.z,wp2.x,wp2.y,wp2.z,l.flags,wp1.id,wp2.id from sc_waypoint_links l, sc_waypoints wp1, sc_waypoints wp2 where l.wp1 = wp1.id and l.wp2 = wp2.id and wp1.id = ".$id;
        $res2=mysql_query2($query2); 
        while ($line2 = fetchSqlRow($res2)){
            $x1 = $line2[0];
            $y1 = $line2[1];
            $z1 = $line2[2];
            $x2 = $line2[3];
            $y2 = $line2[4];
            $z2 = $line2[5];
            $flags = $line2[6];
            $id1 = $line2[7];
            $id2 = $line2[8];

            $ix1 = (($x1-$centerx)*$scalefactorx)/100;
            $iy1 = (($y1-$centery)*$scalefactory)/100;
            $ix2 = (($x2-$centerx)*$scalefactorx)/100;
            $iy2 = (($y2-$centery)*$scalefactory)/100;

            if (stristr($flags, 'NO_WANDER') !== FALSE)
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
                //imageline($im,$ix1,$iy1,$ix2,$iy2 , $line_color);
            }
            if (stristr($flags, 'ONEWAY') !== FALSE)
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

function draw_paths($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$fg_color,$bg_color){
    $query = "select distinct wl.id,wl.flags from sc_waypoint_links wl, sc_waypoints wp where (wp.id = wl.wp1 or wp.id=wl.wp2) and ".$sectors;
    //echo $query;
    $res = mysql_query2($query);

    // exit if there is no data
    $num = sqlNumRows($res);
    if ($num==0)
      return;

    while ($line = fetchSqlRow($res))
    {
       $path_id = $line[0];
       $flags   = $line[1];
       
       $style = array($bg_color,$bg_color,$bg_color);

       if (stristr($flags, "NO_WANDER") !== FALSE)
       {
          $red = imagecolorallocate($im, 255, 0, 0);
          $style = array_merge($style,array($red,$red,$red));
       }
       if (stristr($flags, "TELEPORT") !== FALSE)
       {
          $blue = imagecolorallocate($im, 0, 255, 0);
          $style = array_merge($style,array($blue,$blue,$blue));
       }

       imagesetstyle($im,$style);

       $ix2 = 0;
       $iy2 = 0;
       // Get start point, from waypoint
       {
            // Draw from start wp to first point
            $query2 = "select w.x,w.y from sc_waypoints w, sc_waypoint_links wl where w.id = wl.wp1 and wl.id=".$path_id;
            $res2=mysql_query2($query2);
            while ($line2 = fetchSqlRow($res2))
            {
                $x1 = $line2[0];
                $y1 = $line2[1];
                $ix2 = (($x1-$centerx)*$scalefactorx)/100;
                $iy2 = (($y1-$centery)*$scalefactory)/100;
            }
       }
       // Get path points and draw line from last point
       {
            $point_id = 0;
            $found = false;
            do
            {
                $found = false;
		// Draw from start wp to first point
            	$query2 = "select id,x,y from sc_path_points where path_id=".$path_id." and prev_point=".$point_id;
            	$res2=mysql_query2($query2);
            	while ($line2 = fetchSqlRow($res2))
	    	{
                    $found = true;

		    // Shift the start point    
                    $ix1 = $ix2;
                    $iy1 = $iy2;

		    $point_id = $line2[0];
                    $x1 = $line2[1];
                    $y1 = $line2[2];
                    $ix2 = (($x1-$centerx)*$scalefactorx)/100;
                    $iy2 = (($y1-$centery)*$scalefactory)/100;

                    $ir = 3;
                    imageline($im,$ix1,$iy1,$ix2,$iy2,  IMG_COLOR_STYLED );
                    imagearc($im,$ix2,$iy2,$ir,$ir,0,360,$fg_color);
                }
            } while ($found);
        }
            
        {
            // Draw from start wp to first point
            $query2 = "select w.x,w.y  from sc_waypoints w, sc_waypoint_links wl where w.id = wl.wp2 and wl.id=".$path_id;
            $res2=mysql_query2($query2);
            while ($line2 = fetchSqlRow($res2))
            {
                // Shift the start point    
                $ix1 = $ix2;
                $iy1 = $iy2;

                $x1 = $line2[0];
                $y1 = $line2[1];
                $ix2 = (($x1-$centerx)*$scalefactorx)/100;
                $iy2 = (($y1-$centery)*$scalefactory)/100;

                imageline($im, $ix1, $iy1, $ix2, $iy2, IMG_COLOR_STYLED );
            }
        } 

    }
}

function draw_locations($im,$sectors,$centerx,$centery,$scalefactorx,$scalefactory,$fg_color){
    $query = "select id,x,y,z,radius,id_prev_loc_in_region from sc_locations where " . $sectors;
    $res = mysql_query2($query);

    // exit if there is no data
    $num = sqlNumRows($res);
    if ($num==0)
      return;

    $i=0;
    while ($line = fetchSqlRow($res)){
        $id      = $line[0];
        $x       = $line[1];
        $y       = $line[2];
        $z       = $line[3];
        $radius  = $line[4];
        $id_prev = $line[5];

        $ix1 = (($x-$centerx)*$scalefactorx)/100;
        $iy1 = (($y-$centery)*$scalefactory)/100;
        $ir = $radius*$scalefactorx;
        imagearc($im,$ix1,$iy1,2*$ir,2*$ir,0,360,$fg_color);

        if ( $id_prev > 0 )
        {
            $query2 = "select x,y,z from sc_locations where " . $sectors . " and id = " . $id_prev;
            $res2 = mysql_query2($query2);
            while ($line2 = fetchSqlRow($res2))
            {
                $x2       = $line2[0];
                $y2       = $line2[1];
                $z2       = $line2[2];
 
                $ix2 = (($x2-$centerx)*$scalefactorx)/100;
                $iy2 = (($y2-$centery)*$scalefactory)/100;
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
		
						$x = (($pieces[0]-$centerx)*$scalefactorx)/100;
						$y = (($pieces[1]-$centery)*$scalefactory)/100;
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
