<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<title>Jupiter Ionosphere/Magnetosphere Online Mapping Tool - Beta Version</title>
<link rel="stylesheet" href="style.css" type="text/css" />
<link rel="stylesheet" href="layout.css" type="text/css" media="screen" />
</head>
<body>

<?

if ($_POST['username'] != '' and $_POST['email_contact'] != '' ) { //only perform mapping if user provides contact info
    

function map_ion_to_mag($latpt,$longpt,$sslong,$model)
{
    $longpt = 360.0 - $longpt + 360.0; // covert to right handed
    $longpt = fmod($longpt,360.0);
    
    $latpt_start = $latpt;
    
    $xpt = sin(deg2rad(90.-$latpt))*cos(deg2rad($longpt));
    $ypt = sin(deg2rad(90.-$latpt))*sin(deg2rad($longpt));
    
    $r1_array = array();
    $r2_array = array();
    $r3_array = array();
    $r4_array = array();
    $ltime1_array = array();
    $ltime2_array = array();
    $ltime3_array = array();
    $ltime4_array = array();
    $x1_array = array();
    $x2_array = array();
    $x3_array = array();
    $x4_array = array();
    $y1_array = array();
    $y2_array = array();
    $y3_array = array();
    $y4_array = array();
    
    if ($latpt < 0.) {
        $hemisphere = 's';
        $latpt = 90.0 + $latpt; // convert to colatitude
    } else {
        $latpt = 90.0 - $latpt; // convert to colatitude
        $hemisphere = '';
    }
    $sslong_int = (int) $sslong;
    $sslong_text = (string) $sslong_int;
    $sslong_text1 = "sslong" . $sslong_text;
    $sslong_text2 = $sslong_text1 . $hemisphere;
    $sslong_text3 = $sslong_text2 . '_';
    $sslong_text4 = $sslong_text3 . $model;
    $filetext = $sslong_text4 . '.txt';
    
    //echo '<p>';
    //printf("file test was %s",$filetext);
    $handle = fopen($filetext, "r");
    $nelements = -1;
    $filematch = -1;
    
    while (!feof($handle)) {
        $coordinates = fscanf($handle, "%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\n");
        if ($coordinates) {
            list ($r1, $ltime1, $lat1, $long1, $r2, $ltime2, $lat2, $long2, $r3, $ltime3, $lat3, $long3, $r4, $ltime4, $lat4, $long4) = $coordinates;
            $r1_array[] = $r1;
            $r2_array[] = $r2;
            $r3_array[] = $r3;
            $r4_array[] = $r4;
            $ltime1_array[] = $ltime1;
            $ltime2_array[] = $ltime2;
            $ltime3_array[] = $ltime3;
            $ltime4_array[] = $ltime4;
            
            $x1 = sin(deg2rad($lat1))*cos(deg2rad($long1));
            $y1 = sin(deg2rad($lat1))*sin(deg2rad($long1));
            $x2 = sin(deg2rad($lat2))*cos(deg2rad($long2));
            $y2 = sin(deg2rad($lat2))*sin(deg2rad($long2));
            $x3 = sin(deg2rad($lat3))*cos(deg2rad($long3));
            $y3 = sin(deg2rad($lat3))*sin(deg2rad($long3));
            $x4 = sin(deg2rad($lat4))*cos(deg2rad($long4));
            $y4 = sin(deg2rad($lat4))*sin(deg2rad($long4));
            
            $x1_array[] = $x1;
            $x2_array[] = $x2;
            $x3_array[] = $x3;
            $x4_array[] = $x4;
            $y1_array[] = $y1;
            $y2_array[] = $y2;
            $y3_array[] = $y3;
            $y4_array[] = $y4;
            
            $nelements = $nelements + 1;
            $xmin = min($x1, $x2, $x3, $x4);
            $xmax = max($x1, $x2, $x3, $x4);
            $ymin = min($y1, $y2, $y3, $y4);
            $ymax = max($y1, $y2, $y3, $y4);
            
            if ($xpt >= $xmin and $xpt <= $xmax and $ypt >= $ymin and $ypt <= $ymax) {
                //echo '<p> found match point <p>';
                //$filematch = $nelements;
                
                $x_array = array();
                $y_array = array();
                $x_array[0] = $x1;
                $x_array[1] = $x3;
                $x_array[2] = $x4;
                $x_array[3] = $x2;
                $y_array[0] = $y1;
                $y_array[1] = $y3;
                $y_array[2] = $y4;
                $y_array[3] = $y2;
                $c = 0;
                $iloop = 0;
                $jloop = 3;
                $jloop_start = $jloop;
                $nvert = $jloop+1;
                while ($iloop < $nvert) {
                    if ( (($y_array[$iloop] > $ypt) != ($y_array[$jloop] > $ypt)) and ($xpt < ($x_array[$jloop]-$x_array[$iloop])*($ypt-$y_array[$iloop])/($y_array[$jloop]-$y_array[$iloop]) + $x_array[$iloop]) )
                    {
                        if ($c == 1) {
                            $c = 0;
                        }
                        else {
                            $c = 1;
                        }
                    }
                    $jloop = $iloop;
                    $iloop = $iloop + 1;
                }
                if ($c == 1) {
                    $filematch = $nelements;
                }
            }
        }
        $coordinates=NULL;
    }
    fclose($handle);
    //	echo '<p>';
    //	printf("filematch is %d",$filematch);
    //	echo '<p>';
    
    if ($filematch != -1) {
        $x1 = $x1_array[$filematch];
        $x2 = $x2_array[$filematch];
        $x3 = $x3_array[$filematch];
        $x4 = $x4_array[$filematch];
        $y1 = $y1_array[$filematch];
        $y2 = $y2_array[$filematch];
        $y3 = $y3_array[$filematch];
        $y4 = $y4_array[$filematch];
        
        $x1prime = $x1 - $x1;
        $x2prime = $x2 - $x1;
        $x3prime = $x3 - $x1;
        $x4prime = $x4 - $x1;
        $xpt_prime = $xpt - $x1;
        $y1prime = $y1 - $y1;
        $y2prime = $y2 - $y1;
        $y3prime = $y3 - $y1;
        $y4prime = $y4 - $y1;
        $ypt_prime = $ypt - $y1;
        
        $theta = atan($y3prime/$x3prime);
        if ($y3prime < 0.0 and $x3prime < 0.0) {
            $theta = $theta + pi();
        }
        if ($y3prime > 0.0 and $x3prime < 0.0) {
            $theta = $theta + pi();
        }
        
        $a1 = $x1prime*cos($theta) + $y1prime*sin($theta);
        $a2 = $x2prime*cos($theta) + $y2prime*sin($theta);
        $a3 = $x3prime*cos($theta) + $y3prime*sin($theta);
        $a4 = $x4prime*cos($theta) + $y4prime*sin($theta);
        $apt = $xpt_prime*cos($theta) + $ypt_prime*sin($theta);
        $b1 = -1.0*$x1prime*sin($theta) + $y1prime*cos($theta);
        $b2 = -1.0*$x2prime*sin($theta) + $y2prime*cos($theta);
        $b3 = -1.0*$x3prime*sin($theta) + $y3prime*cos($theta);
        $b4 = -1.0*$x4prime*sin($theta) + $y4prime*cos($theta);
        $bpt = -1.0*$xpt_prime*sin($theta) + $ypt_prime*cos($theta);
        
        $c = $b2 - (($b4-$b2)/($a4-$a2))*$a2;
        $b5 = (($b4-$b2)/($a4-$a2))*$apt + $c;
        $r_interpol = $r1_array[$filematch] + $bpt*5.0/$b5;
        
        $dist1 = ($apt-$a1)*($apt-$a1) + ($bpt-$b1)*($bpt-$b1);
        $dist2 = ($apt-$a2)*($apt-$a2) + ($bpt-$b2)*($bpt-$b2);
        $dist3 = ($apt-$a3)*($apt-$a3) + ($bpt-$b3)*($bpt-$b3);
        $dist4 = ($apt-$a4)*($apt-$a4) + ($bpt-$b4)*($bpt-$b4);
        
        $ltime1 = $ltime1_array[$filematch];
        $ltime2 = $ltime2_array[$filematch];
        $ltime3 = $ltime3_array[$filematch];
        $ltime4 = $ltime4_array[$filematch];
        if ($ltime1 < 0.) {
            $ltime1 = $ltime1 + 24.0;
        }
        if ($ltime2 < 0.) {
            $ltime2 = $ltime2 + 24.0;
        }
        if ($ltime3 < 0.) {
            $ltime3 = $ltime3 + 24.0;
        }
        if ($ltime4 < 0.) {
            $ltime4 = $ltime4 + 24.0;
        }
        
        if (($dist1 <= $dist2 and $dist1 <= $dist4) or (($dist3 <= $dist2 and $dist3 <= $dist4))) {
            if (abs($ltime3 - $ltime1) < 12.0) {
                $ltime_interpol = $ltime1 + (($ltime3 - $ltime1)/($a3-$a1))*($apt-$a1);
            }
            else
            {
                $ltime_interpol = $ltime1 + ((24.0-abs($ltime3 - $ltime1))/($a3-$a1))*($apt-$a1);
            }
        }
        else
        {
            if (abs($ltime4 - $ltime2) < 12.0) {
                $ltime_interpol = $ltime2 + (($ltime4 - $ltime2)/($a4-$a2))*($apt-$a2);
            }
            else
            {
                $ltime_interpol = $ltime2 + ((24.0-abs($ltime4 - $ltime2))/($a4-$a2))*($apt-$a2);
            }
        }
    } else { // if filematch = -1
        // test to see if point is inside of the 15 Rj contour
        
        $x1_array15 = array();
        $y1_array15 = array();
        $jloop = 0;
        while ($r1_array[$jloop] == 15.) {
            $jloop = $jloop + 1;
        }
        $jloop = $jloop - 1;
        
        $c = 0;
        $iloop = 0;
        $jloop_start = $jloop;
        $jloop = $jloop;
        $nvert = $jloop+1;
        
        while ($iloop < $nvert) {
            if ( (($y1_array[$iloop] > $ypt) != ($y1_array[$jloop] > $ypt)) and ($xpt < ($x1_array[$jloop]-$x1_array[$iloop])*($ypt-$y1_array[$iloop])/($y1_array[$jloop]-$y1_array[$iloop]) + $x1_array[$iloop]) )
            {
                //echo '<p>inside if loop<p>';
                if ($c == 1) {
                    $c = 0;
                }
                else {
                    $c = 1;
                }
            }
            $jloop = $iloop;
            $iloop = $iloop + 1;
        }
        
        $inside15 = $c;
        if ($inside15 == 1) {
            $r_interpol = -999.;
            $ltime_interpol = -999.;
        } else {
            $r_interpol = -998.;
            $ltime_interpol = -998.;
        }
    }
    
    $return_array = array();
    $return_array[0] = $r_interpol;
    $return_array[1] = $ltime_interpol;
    return $return_array;
}




    
    
    
    
    
    
    
    
    
    
    
    function map_ion_to_mag_tracing($latpt,$longpt,$sslong,$model)
    {
        $longpt = 360.0 - $longpt + 360.0; // covert to right handed
        $longpt = fmod($longpt,360.0);
        
        $latpt_start = $latpt;
        
        $xpt = sin(deg2rad(90.-$latpt))*cos(deg2rad($longpt));
        $ypt = sin(deg2rad(90.-$latpt))*sin(deg2rad($longpt));
        
        $r1_array = array();
        $r2_array = array();
        $r3_array = array();
        $r4_array = array();
        $ltime1_array = array();
        $ltime2_array = array();
        $ltime3_array = array();
        $ltime4_array = array();
        $x1_array = array();
        $x2_array = array();
        $x3_array = array();
        $x4_array = array();
        $y1_array = array();
        $y2_array = array();
        $y3_array = array();
        $y4_array = array();
        
        if ($latpt < 0.) {
            $hemisphere = 'south';
            $latpt = 90.0 + $latpt; // convert to colatitude
        } else {
            $latpt = 90.0 - $latpt; // convert to colatitude
            $hemisphere = 'north';
        }
        $sslong_int = (int) $sslong;
        $sslong_text = (string) $sslong_int;
        $sslong_text1 = "fieldline_tracing_all_r_" . $model;
        $sslong_text2 = $sslong_text1 . '_';
        $sslong_text3 = $sslong_text2 . $hemisphere;
        $sslong_text4 = $sslong_text3 . '_cml';
        $sslong_text5 = $sslong_text4 . $sslong_text;
        $filetext = $sslong_text5 . '.txt';
        
        //echo '<p>';
        //echo 'hi';
        //printf("file test was %s",$filetext);
        $handle = fopen($filetext, "r");
        $nelements = -1;
        $filematch = -1;
        
        while (!feof($handle)) {
            $coordinates = fscanf($handle, "%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\n");
            if ($coordinates) {
                //list ($r1, $ltime1, $lat1, $long1, $r2, $ltime2, $lat2, $long2, $r3, $ltime3, $lat3, $long3, $r4, $ltime4, $lat4, $long4) = $coordinates;
                list ($r1, $ltime1, $lat1, $long1, $r2, $ltime2, $lat2, $long2, $r4, $ltime4, $lat4, $long4, $r3, $ltime3, $lat3, $long3) = $coordinates;
                $r1_array[] = $r1;
                $r2_array[] = $r2;
                $r3_array[] = $r3;
                $r4_array[] = $r4;
                $ltime1_array[] = $ltime1;
                $ltime2_array[] = $ltime2;
                $ltime3_array[] = $ltime3;
                $ltime4_array[] = $ltime4;
                
                $x1 = sin(deg2rad($lat1))*cos(deg2rad($long1));
                $y1 = sin(deg2rad($lat1))*sin(deg2rad($long1));
                $x2 = sin(deg2rad($lat2))*cos(deg2rad($long2));
                $y2 = sin(deg2rad($lat2))*sin(deg2rad($long2));
                $x3 = sin(deg2rad($lat3))*cos(deg2rad($long3));
                $y3 = sin(deg2rad($lat3))*sin(deg2rad($long3));
                $x4 = sin(deg2rad($lat4))*cos(deg2rad($long4));
                $y4 = sin(deg2rad($lat4))*sin(deg2rad($long4));
                
                $x1_array[] = $x1;
                $x2_array[] = $x2;
                $x3_array[] = $x3;
                $x4_array[] = $x4;
                $y1_array[] = $y1;
                $y2_array[] = $y2;
                $y3_array[] = $y3;
                $y4_array[] = $y4;
                
                $nelements = $nelements + 1;
                $xmin = min($x1, $x2, $x3, $x4);
                $xmax = max($x1, $x2, $x3, $x4);
                $ymin = min($y1, $y2, $y3, $y4);
                $ymax = max($y1, $y2, $y3, $y4);
                
                if ($xpt >= $xmin and $xpt <= $xmax and $ypt >= $ymin and $ypt <= $ymax) {
                    //echo '<p> found match point <p>';
                    //$filematch = $nelements;
                    
                    $x_array = array();
                    $y_array = array();
                    $x_array[0] = $x1;
                    $x_array[1] = $x3;
                    $x_array[2] = $x4;
                    $x_array[3] = $x2;
                    $y_array[0] = $y1;
                    $y_array[1] = $y3;
                    $y_array[2] = $y4;
                    $y_array[3] = $y2;
                    $c = 0;
                    $iloop = 0;
                    $jloop = 3;
                    $jloop_start = $jloop;
                    $nvert = $jloop+1;
                    while ($iloop < $nvert) {
                        if ( (($y_array[$iloop] > $ypt) != ($y_array[$jloop] > $ypt)) and ($xpt < ($x_array[$jloop]-$x_array[$iloop])*($ypt-$y_array[$iloop])/($y_array[$jloop]-$y_array[$iloop]) + $x_array[$iloop]) )
                        {
                            if ($c == 1) {
                                $c = 0;
                            }
                            else {
                                $c = 1;
                            }
                        }
                        $jloop = $iloop;
                        $iloop = $iloop + 1;
                    }
                    if ($c == 1) {
                        $filematch = $nelements;
                    }
                }
            }
            $coordinates=NULL;
        }
        fclose($handle);
        //	echo '<p>';
        //	printf("filematch is %d",$filematch);
        //	echo '<p>';
        
        if ($filematch != -1) {
            $x1 = $x1_array[$filematch];
            $x2 = $x2_array[$filematch];
            $x3 = $x3_array[$filematch];
            $x4 = $x4_array[$filematch];
            $y1 = $y1_array[$filematch];
            $y2 = $y2_array[$filematch];
            $y3 = $y3_array[$filematch];
            $y4 = $y4_array[$filematch];
            
            $x1prime = $x1 - $x1;
            $x2prime = $x2 - $x1;
            $x3prime = $x3 - $x1;
            $x4prime = $x4 - $x1;
            $xpt_prime = $xpt - $x1;
            $y1prime = $y1 - $y1;
            $y2prime = $y2 - $y1;
            $y3prime = $y3 - $y1;
            $y4prime = $y4 - $y1;
            $ypt_prime = $ypt - $y1;
            
            $theta = atan($y3prime/$x3prime);
            if ($y3prime < 0.0 and $x3prime < 0.0) {
                $theta = $theta + pi();
            }
            if ($y3prime > 0.0 and $x3prime < 0.0) {
                $theta = $theta + pi();
            }
            
            $a1 = $x1prime*cos($theta) + $y1prime*sin($theta);
            $a2 = $x2prime*cos($theta) + $y2prime*sin($theta);
            $a3 = $x3prime*cos($theta) + $y3prime*sin($theta);
            $a4 = $x4prime*cos($theta) + $y4prime*sin($theta);
            $apt = $xpt_prime*cos($theta) + $ypt_prime*sin($theta);
            $b1 = -1.0*$x1prime*sin($theta) + $y1prime*cos($theta);
            $b2 = -1.0*$x2prime*sin($theta) + $y2prime*cos($theta);
            $b3 = -1.0*$x3prime*sin($theta) + $y3prime*cos($theta);
            $b4 = -1.0*$x4prime*sin($theta) + $y4prime*cos($theta);
            $bpt = -1.0*$xpt_prime*sin($theta) + $ypt_prime*cos($theta);
            
            $c = $b2 - (($b4-$b2)/($a4-$a2))*$a2;
            $b5 = (($b4-$b2)/($a4-$a2))*$apt + $c;
            $r_interpol = $r1_array[$filematch] + $bpt*5.0/$b5;
            
            $dist1 = ($apt-$a1)*($apt-$a1) + ($bpt-$b1)*($bpt-$b1);
            $dist2 = ($apt-$a2)*($apt-$a2) + ($bpt-$b2)*($bpt-$b2);
            $dist3 = ($apt-$a3)*($apt-$a3) + ($bpt-$b3)*($bpt-$b3);
            $dist4 = ($apt-$a4)*($apt-$a4) + ($bpt-$b4)*($bpt-$b4);
            
            $ltime1 = $ltime1_array[$filematch];
            $ltime2 = $ltime2_array[$filematch];
            $ltime3 = $ltime3_array[$filematch];
            $ltime4 = $ltime4_array[$filematch];
            if ($ltime1 < 0.) {
                $ltime1 = $ltime1 + 24.0;
            }
            if ($ltime2 < 0.) {
                $ltime2 = $ltime2 + 24.0;
            }
            if ($ltime3 < 0.) {
                $ltime3 = $ltime3 + 24.0;
            }
            if ($ltime4 < 0.) {
                $ltime4 = $ltime4 + 24.0;
            }
            
            if (($dist1 <= $dist2 and $dist1 <= $dist4) or (($dist3 <= $dist2 and $dist3 <= $dist4))) {
                if (abs($ltime3 - $ltime1) < 12.0) {
                    $ltime_interpol = $ltime1 + (($ltime3 - $ltime1)/($a3-$a1))*($apt-$a1);
                }
                else
                {
                    $ltime_interpol = $ltime1 + ((24.0-abs($ltime3 - $ltime1))/($a3-$a1))*($apt-$a1);
                }
            }
            else
            {
                if (abs($ltime4 - $ltime2) < 12.0) {
                    $ltime_interpol = $ltime2 + (($ltime4 - $ltime2)/($a4-$a2))*($apt-$a2);
                }
                else
                {
                    $ltime_interpol = $ltime2 + ((24.0-abs($ltime4 - $ltime2))/($a4-$a2))*($apt-$a2);
                }
            }
        } else { // if filematch = -1
            // test to see if point is inside of the 15 Rj contour
            
            $x1_array15 = array();
            $y1_array15 = array();
            $jloop = 0;
            while ($r1_array[$jloop] == 15.) {
                $jloop = $jloop + 1;
            }
            $jloop = $jloop - 1;
            
            $c = 0;
            $iloop = 0;
            $jloop_start = $jloop;
            $jloop = $jloop;
            $nvert = $jloop+1;
            
            while ($iloop < $nvert) {
                if ( (($y1_array[$iloop] > $ypt) != ($y1_array[$jloop] > $ypt)) and ($xpt < ($x1_array[$jloop]-$x1_array[$iloop])*($ypt-$y1_array[$iloop])/($y1_array[$jloop]-$y1_array[$iloop]) + $x1_array[$iloop]) )
                {
                    //echo '<p>inside if loop<p>';
                    if ($c == 1) {
                        $c = 0;
                    }
                    else {
                        $c = 1;
                    }
                }
                $jloop = $iloop;
                $iloop = $iloop + 1;
            }
            
            $inside15 = $c;
            if ($inside15 == 1) {
                $r_interpol = -999.;
                $ltime_interpol = -999.;
            } else {
                $r_interpol = -998.;
                $ltime_interpol = -998.;
            }
        }
        
        $return_array = array();
        $return_array[0] = $r_interpol;
        $return_array[1] = $ltime_interpol;
        return $return_array;
    }
    



    
    
    

    
    
    





function map_mag_to_ion($rj,$loctime,$sslong,$model,$hemisphere)
{
    $loctime_rad = $loctime*pi()/12.0;
    
    $xpt = $rj*cos($loctime_rad);
    $ypt = $rj*sin($loctime_rad);
    
    $r1_array = array();
    $r2_array = array();
    $r3_array = array();
    $r4_array = array();
    $ltime1_array = array();
    $ltime2_array = array();
    $ltime3_array = array();
    $ltime4_array = array();
    $x1_array = array();
    $x2_array = array();
    $x3_array = array();
    $x4_array = array();
    $y1_array = array();
    $y2_array = array();
    $y3_array = array();
    $y4_array = array();
    
    if ($hemisphere == 'south') {
        $hemisphere_text = 's';
    } else {
        $hemisphere_text = '';
    }
    $sslong_int = (int) $sslong;
    $sslong_text = (string) $sslong_int;
    $sslong_text1 = "sslong" . $sslong_text;
    $sslong_text2 = $sslong_text1 . $hemisphere_text;
    $sslong_text3 = $sslong_text2 . '_';
    $sslong_text4 = $sslong_text3 . $model;
    $filetext = $sslong_text4 . '.txt';
    
    
    //echo '<p>';
    //printf("file test was %s",$filetext);
    $handle = fopen($filetext, "r");
    $nelements = -1;
    $filematch = -1;
    
    while (!feof($handle)) {
        $coordinates = fscanf($handle, "%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\n");
        if ($coordinates) {
            list ($r1, $ltime1, $lat1, $long1, $r2, $ltime2, $lat2, $long2, $r3, $ltime3, $lat3, $long3, $r4, $ltime4, $lat4, $long4) = $coordinates;
            $r1_array6[] = $r1;
            $r2_array6[] = $r2;
            $r3_array6[] = $r3;
            $r4_array6[] = $r4;
            $ltime1_array6[] = $ltime1;
            $ltime2_array6[] = $ltime2;
            $ltime3_array6[] = $ltime3;
            $ltime4_array6[] = $ltime4;
            
            $x1 = $r1*cos($ltime1*pi()/12.0);
            $y1 = $r1*sin($ltime1*pi()/12.0);
            $x2 = $r2*cos($ltime2*pi()/12.0);
            $y2 = $r2*sin($ltime2*pi()/12.0);
            $x3 = $r3*cos($ltime3*pi()/12.0);
            $y3 = $r3*sin($ltime3*pi()/12.0);
            $x4 = $r4*cos($ltime4*pi()/12.0);
            $y4 = $r4*sin($ltime4*pi()/12.0);
            
            $x1_array6[] = $x1;
            $x2_array6[] = $x2;
            $x3_array6[] = $x3;
            $x4_array6[] = $x4;
            $y1_array6[] = $y1;
            $y2_array6[] = $y2;
            $y3_array6[] = $y3;
            $y4_array6[] = $y4;
            
            $x1ion = sin(deg2rad($lat1))*cos(deg2rad($long1));
            $y1ion = sin(deg2rad($lat1))*sin(deg2rad($long1));
            $x2ion = sin(deg2rad($lat2))*cos(deg2rad($long2));
            $y2ion = sin(deg2rad($lat2))*sin(deg2rad($long2));
            $x3ion = sin(deg2rad($lat3))*cos(deg2rad($long3));
            $y3ion = sin(deg2rad($lat3))*sin(deg2rad($long3));
            $x4ion = sin(deg2rad($lat4))*cos(deg2rad($long4));
            $y4ion = sin(deg2rad($lat4))*sin(deg2rad($long4));
            
            $x1_arrayion6[] = $x1ion;
            $x2_arrayion6[] = $x2ion;
            $x3_arrayion6[] = $x3ion;
            $x4_arrayion6[] = $x4ion;
            $y1_arrayion6[] = $y1ion;
            $y2_arrayion6[] = $y2ion;
            $y3_arrayion6[] = $y3ion;
            $y4_arrayion6[] = $y4ion;
            
            $nelements = $nelements + 1;
            $xmin = min($x1, $x2, $x3, $x4);
            $xmax = max($x1, $x2, $x3, $x4);
            $ymin = min($y1, $y2, $y3, $y4);
            $ymax = max($y1, $y2, $y3, $y4);
            
            if ($xpt >= $xmin and $xpt <= $xmax and $ypt >= $ymin and $ypt <= $ymax) {
                //echo '<p> found match point <p>';
                //$filematch = $nelements;
                
                $x_array = array();
                $y_array = array();
                $x_array[0] = $x1;
                $x_array[1] = $x3;
                $x_array[2] = $x4;
                $x_array[3] = $x2;
                $y_array[0] = $y1;
                $y_array[1] = $y3;
                $y_array[2] = $y4;
                $y_array[3] = $y2;
                $c = 0;
                $iloop = 0;
                $jloop = 3;
                $jloop_start = $jloop;
                $nvert = $jloop+1;
                while ($iloop < $nvert) {
                    if ( (($y_array[$iloop] > $ypt) != ($y_array[$jloop] > $ypt)) and ($xpt < ($x_array[$jloop]-$x_array[$iloop])*($ypt-$y_array[$iloop])/($y_array[$jloop]-$y_array[$iloop]) + $x_array[$iloop]) )
                    {
                        if ($c == 1) {
                            $c = 0;
                        }
                        else {
                            $c = 1;
                        }
                    }
                    $jloop = $iloop;
                    $iloop = $iloop + 1;
                }
                if ($c == 1) {
                    $filematch = $nelements;
                }
            }
        }
        $coordinates=NULL;
    }
    fclose($handle);
    
    $x1 = $x1_array6[$filematch];
    $x2 = $x2_array6[$filematch];
    $x3 = $x3_array6[$filematch];
    $x4 = $x4_array6[$filematch];
    $y1 = $y1_array6[$filematch];
    $y2 = $y2_array6[$filematch];
    $y3 = $y3_array6[$filematch];
    $y4 = $y4_array6[$filematch];
    
    $x1ion = $x1_arrayion6[$filematch];
    $x2ion = $x2_arrayion6[$filematch];
    $x3ion = $x3_arrayion6[$filematch];
    $x4ion = $x4_arrayion6[$filematch];
    $y1ion = $y1_arrayion6[$filematch];
    $y2ion = $y2_arrayion6[$filematch];
    $y3ion = $y3_arrayion6[$filematch];
    $y4ion = $y4_arrayion6[$filematch];
    
    $x1prime = $x1 - $x1;
    $x2prime = $x2 - $x1;
    $x3prime = $x3 - $x1;
    $x4prime = $x4 - $x1;
    $xpt_prime = $xpt - $x1;
    $y1prime = $y1 - $y1;
    $y2prime = $y2 - $y1;
    $y3prime = $y3 - $y1;
    $y4prime = $y4 - $y1;
    $ypt_prime = $ypt - $y1;
    
    $theta = atan($y3prime/$x3prime);
    if ($y3prime < 0.0 and $x3prime < 0.0) {
        $theta = $theta + pi();
    }
    if ($y3prime > 0.0 and $x3prime < 0.0) {
        $theta = $theta + pi();
    }
    
    $a1 = $x1prime*cos($theta) + $y1prime*sin($theta);
    $a2 = $x2prime*cos($theta) + $y2prime*sin($theta);
    $a3 = $x3prime*cos($theta) + $y3prime*sin($theta);
    $a4 = $x4prime*cos($theta) + $y4prime*sin($theta);
    $apt = $xpt_prime*cos($theta) + $ypt_prime*sin($theta);
    $b1 = -1.0*$x1prime*sin($theta) + $y1prime*cos($theta);
    $b2 = -1.0*$x2prime*sin($theta) + $y2prime*cos($theta);
    $b3 = -1.0*$x3prime*sin($theta) + $y3prime*cos($theta);
    $b4 = -1.0*$x4prime*sin($theta) + $y4prime*cos($theta);
    $bpt = -1.0*$xpt_prime*sin($theta) + $ypt_prime*cos($theta);
    
    $x1prime = $x1ion - $x1ion;
    $x2prime = $x2ion - $x1ion;
    $x3prime = $x3ion - $x1ion;
    $x4prime = $x4ion - $x1ion;
    $y1prime = $y1ion - $y1ion;
    $y2prime = $y2ion - $y1ion;
    $y3prime = $y3ion - $y1ion;
    $y4prime = $y4ion - $y1ion;
    
    $thetaion = atan($y3prime/$x3prime);
    if ($y3prime < 0.0 and $x3prime < 0.0) {
        $thetaion = $thetaion + pi();
    }
    if ($y3prime > 0.0 and $x3prime < 0.0) {
        $thetaion = $thetaion + pi();
    }
    
    $a1ion = $x1prime*cos($thetaion) + $y1prime*sin($thetaion);
    $a2ion = $x2prime*cos($thetaion) + $y2prime*sin($thetaion);
    $a3ion = $x3prime*cos($thetaion) + $y3prime*sin($thetaion);
    $a4ion = $x4prime*cos($thetaion) + $y4prime*sin($thetaion);
    $b1ion = -1.0*$x1prime*sin($thetaion) + $y1prime*cos($thetaion);
    $b2ion = -1.0*$x2prime*sin($thetaion) + $y2prime*cos($thetaion);
    $b3ion = -1.0*$x3prime*sin($thetaion) + $y3prime*cos($thetaion);
    $b4ion = -1.0*$x4prime*sin($thetaion) + $y4prime*cos($thetaion);
    
    $aption = $a1ion + ($a3ion-$a1ion)*($apt-$a1)/($a3-$a1);
    $bption = $b1ion + ($b2ion-$b1ion)*($bpt-$b1)/($b2-$b1);
    
    $xption = $aption*cos($thetaion) - $bption*sin($thetaion) + $x1ion;
    $yption = $aption*sin($thetaion) + $bption*cos($thetaion) + $y1ion;
    
    $lat_interpol = rad2deg(asin(sqrt($xption*$xption + $yption*$yption))); // will need to be fixed for southern hemisphere
    $long_interpol = rad2deg(atan($yption/$xption));
    if ($yption < 0.0 and $xption < 0.0) {
        $long_interpol = $long_interpol + 180.;
    }
    if ($yption > 0.0 and $xption < 0.0) {
        $long_interpol = $long_interpol + 180.;
    }
    
    $long_interpol = fmod((360.0 - $long_interpol + 360.0),360.0); // convert to LH
    
    $return_array = array();
    if ($hemisphere == 'south') {
        $return_array[0] = -1.0*(90.0 - $lat_interpol);
    } else {
        $return_array[0] = 90.0 - $lat_interpol;
    }
    $return_array[1] = $long_interpol;
    return $return_array;
}






    
    
    
    
    
    
    

    function map_mag_to_ion_tracing($rj,$loctime,$sslong,$model,$hemisphere)
    {
        $loctime_rad = $loctime*pi()/12.0;
        
        $xpt = $rj*cos($loctime_rad);
        $ypt = $rj*sin($loctime_rad);
        
        $r1_array = array();
        $r2_array = array();
        $r3_array = array();
        $r4_array = array();
        $ltime1_array = array();
        $ltime2_array = array();
        $ltime3_array = array();
        $ltime4_array = array();
        $x1_array = array();
        $x2_array = array();
        $x3_array = array();
        $x4_array = array();
        $y1_array = array();
        $y2_array = array();
        $y3_array = array();
        $y4_array = array();
        
        if ($hemisphere == 'south') {
            $hemisphere_text = 's';
        } else {
            $hemisphere_text = '';
        }
        $sslong_int = (int) $sslong;
        $sslong_text = (string) $sslong_int;
        $sslong_text1 = "fieldline_tracing_all_r_" . $model;
        $sslong_text2 = $sslong_text1 . '_';
        $sslong_text3 = $sslong_text2 . $hemisphere;
        $sslong_text4 = $sslong_text3 . '_cml';
        $sslong_text5 = $sslong_text4 . $sslong_text;
        $filetext = $sslong_text5 . '.txt';
        
        
        //echo '<p>';
        //printf("file test was %s",$filetext);
        $handle = fopen($filetext, "r");
        $nelements = -1;
        $filematch = -1;
        
        while (!feof($handle)) {
            $coordinates = fscanf($handle, "%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\t%f\n");
            if ($coordinates) {
                //list ($r1, $ltime1, $lat1, $long1, $r2, $ltime2, $lat2, $long2, $r3, $ltime3, $lat3, $long3, $r4, $ltime4, $lat4, $long4) = $coordinates;
                list ($r1, $ltime1, $lat1, $long1, $r2, $ltime2, $lat2, $long2, $r4, $ltime4, $lat4, $long4, $r3, $ltime3, $lat3, $long3) = $coordinates;
                $r1_array6[] = $r1;
                $r2_array6[] = $r2;
                $r3_array6[] = $r3;
                $r4_array6[] = $r4;
                $ltime1_array6[] = $ltime1;
                $ltime2_array6[] = $ltime2;
                $ltime3_array6[] = $ltime3;
                $ltime4_array6[] = $ltime4;
                
                $x1 = $r1*cos($ltime1*pi()/12.0);
                $y1 = $r1*sin($ltime1*pi()/12.0);
                $x2 = $r2*cos($ltime2*pi()/12.0);
                $y2 = $r2*sin($ltime2*pi()/12.0);
                $x3 = $r3*cos($ltime3*pi()/12.0);
                $y3 = $r3*sin($ltime3*pi()/12.0);
                $x4 = $r4*cos($ltime4*pi()/12.0);
                $y4 = $r4*sin($ltime4*pi()/12.0);
                
                $x1_array6[] = $x1;
                $x2_array6[] = $x2;
                $x3_array6[] = $x3;
                $x4_array6[] = $x4;
                $y1_array6[] = $y1;
                $y2_array6[] = $y2;
                $y3_array6[] = $y3;
                $y4_array6[] = $y4;
                
                $x1ion = sin(deg2rad($lat1))*cos(deg2rad($long1));
                $y1ion = sin(deg2rad($lat1))*sin(deg2rad($long1));
                $x2ion = sin(deg2rad($lat2))*cos(deg2rad($long2));
                $y2ion = sin(deg2rad($lat2))*sin(deg2rad($long2));
                $x3ion = sin(deg2rad($lat3))*cos(deg2rad($long3));
                $y3ion = sin(deg2rad($lat3))*sin(deg2rad($long3));
                $x4ion = sin(deg2rad($lat4))*cos(deg2rad($long4));
                $y4ion = sin(deg2rad($lat4))*sin(deg2rad($long4));
                
                $x1_arrayion6[] = $x1ion;
                $x2_arrayion6[] = $x2ion;
                $x3_arrayion6[] = $x3ion;
                $x4_arrayion6[] = $x4ion;
                $y1_arrayion6[] = $y1ion;
                $y2_arrayion6[] = $y2ion;
                $y3_arrayion6[] = $y3ion;
                $y4_arrayion6[] = $y4ion;
                
                $nelements = $nelements + 1;
                $xmin = min($x1, $x2, $x3, $x4);
                $xmax = max($x1, $x2, $x3, $x4);
                $ymin = min($y1, $y2, $y3, $y4);
                $ymax = max($y1, $y2, $y3, $y4);
                
                if ($xpt >= $xmin and $xpt <= $xmax and $ypt >= $ymin and $ypt <= $ymax and $filematch == -1) {
                    //echo '<p> found match point <p>';
                    //printf('filematch %0.3f', $nelements);
                    //echo '<p>';
                    $filematch = $nelements;
                    
                    $x_array = array();
                    $y_array = array();
                    $x_array[0] = $x1;
                    $x_array[1] = $x3;
                    $x_array[2] = $x4;
                    $x_array[3] = $x2;
                    $y_array[0] = $y1;
                    $y_array[1] = $y3;
                    $y_array[2] = $y4;
                    $y_array[3] = $y2;
                    $c = 0;
                    $iloop = 0;
                    $jloop = 3;
                    $jloop_start = $jloop;
                    $nvert = $jloop+1;
                    while ($iloop < $nvert) {
                        if ( (($y_array[$iloop] > $ypt) != ($y_array[$jloop] > $ypt)) and ($xpt < ($x_array[$jloop]-$x_array[$iloop])*($ypt-$y_array[$iloop])/($y_array[$jloop]-$y_array[$iloop]) + $x_array[$iloop]) )
                        {
                            if ($c == 1) {
                                $c = 0;
                            }
                            else {
                                $c = 1;
                            }
                        }
                        $jloop = $iloop;
                        $iloop = $iloop + 1;
                    }
                    if ($c == 1) {
                        $filematch = $nelements;
                    }
                }
            }
            $coordinates=NULL;
        }
        fclose($handle);
        
        $x1 = $x1_array6[$filematch];
        $x2 = $x2_array6[$filematch];
        $x3 = $x3_array6[$filematch];
        $x4 = $x4_array6[$filematch];
        $y1 = $y1_array6[$filematch];
        $y2 = $y2_array6[$filematch];
        $y3 = $y3_array6[$filematch];
        $y4 = $y4_array6[$filematch];
        
        $x1ion = $x1_arrayion6[$filematch];
        $x2ion = $x2_arrayion6[$filematch];
        $x3ion = $x3_arrayion6[$filematch];
        $x4ion = $x4_arrayion6[$filematch];
        $y1ion = $y1_arrayion6[$filematch];
        $y2ion = $y2_arrayion6[$filematch];
        $y3ion = $y3_arrayion6[$filematch];
        $y4ion = $y4_arrayion6[$filematch];
        
        $x1prime = $x1 - $x1;
        $x2prime = $x2 - $x1;
        $x3prime = $x3 - $x1;
        $x4prime = $x4 - $x1;
        $xpt_prime = $xpt - $x1;
        $y1prime = $y1 - $y1;
        $y2prime = $y2 - $y1;
        $y3prime = $y3 - $y1;
        $y4prime = $y4 - $y1;
        $ypt_prime = $ypt - $y1;
        
        $theta = atan($y3prime/$x3prime);
        if ($y3prime < 0.0 and $x3prime < 0.0) {
            $theta = $theta + pi();
        }
        if ($y3prime > 0.0 and $x3prime < 0.0) {
            $theta = $theta + pi();
        }
        
        $a1 = $x1prime*cos($theta) + $y1prime*sin($theta);
        $a2 = $x2prime*cos($theta) + $y2prime*sin($theta);
        $a3 = $x3prime*cos($theta) + $y3prime*sin($theta);
        $a4 = $x4prime*cos($theta) + $y4prime*sin($theta);
        $apt = $xpt_prime*cos($theta) + $ypt_prime*sin($theta);
        $b1 = -1.0*$x1prime*sin($theta) + $y1prime*cos($theta);
        $b2 = -1.0*$x2prime*sin($theta) + $y2prime*cos($theta);
        $b3 = -1.0*$x3prime*sin($theta) + $y3prime*cos($theta);
        $b4 = -1.0*$x4prime*sin($theta) + $y4prime*cos($theta);
        $bpt = -1.0*$xpt_prime*sin($theta) + $ypt_prime*cos($theta);
        
        $x1prime = $x1ion - $x1ion;
        $x2prime = $x2ion - $x1ion;
        $x3prime = $x3ion - $x1ion;
        $x4prime = $x4ion - $x1ion;
        $y1prime = $y1ion - $y1ion;
        $y2prime = $y2ion - $y1ion;
        $y3prime = $y3ion - $y1ion;
        $y4prime = $y4ion - $y1ion;
        
        $thetaion = atan($y3prime/$x3prime);
        if ($y3prime < 0.0 and $x3prime < 0.0) {
            $thetaion = $thetaion + pi();
        }
        if ($y3prime > 0.0 and $x3prime < 0.0) {
            $thetaion = $thetaion + pi();
        }
        
        $a1ion = $x1prime*cos($thetaion) + $y1prime*sin($thetaion);
        $a2ion = $x2prime*cos($thetaion) + $y2prime*sin($thetaion);
        $a3ion = $x3prime*cos($thetaion) + $y3prime*sin($thetaion);
        $a4ion = $x4prime*cos($thetaion) + $y4prime*sin($thetaion);
        $b1ion = -1.0*$x1prime*sin($thetaion) + $y1prime*cos($thetaion);
        $b2ion = -1.0*$x2prime*sin($thetaion) + $y2prime*cos($thetaion);
        $b3ion = -1.0*$x3prime*sin($thetaion) + $y3prime*cos($thetaion);
        $b4ion = -1.0*$x4prime*sin($thetaion) + $y4prime*cos($thetaion);
        
        $aption = $a1ion + ($a3ion-$a1ion)*($apt-$a1)/($a3-$a1);
        $bption = $b1ion + ($b2ion-$b1ion)*($bpt-$b1)/($b2-$b1);
        
        $xption = $aption*cos($thetaion) - $bption*sin($thetaion) + $x1ion;
        $yption = $aption*sin($thetaion) + $bption*cos($thetaion) + $y1ion;
        
        $lat_interpol = rad2deg(asin(sqrt($xption*$xption + $yption*$yption))); // will need to be fixed for southern hemisphere
        $long_interpol = rad2deg(atan($yption/$xption));
        if ($yption < 0.0 and $xption < 0.0) {
            $long_interpol = $long_interpol + 180.;
        }
        if ($yption > 0.0 and $xption < 0.0) {
            $long_interpol = $long_interpol + 180.;
        }
        
        $long_interpol = fmod((360.0 - $long_interpol + 360.0),360.0); // convert to LH
        
        $return_array = array();
        if ($hemisphere == 'south') {
            $return_array[0] = -1.0*(90.0 - $lat_interpol);
        } else {
            $return_array[0] = 90.0 - $lat_interpol;
        }
        $return_array[1] = $long_interpol;
        return $return_array;
    }
    
    







function inside_magnetosphere($r,$ltime)
{
    //r in rj, ltime in hours
    $pexpanded = 0.039;
    $amage = -0.134 + 0.488*pow($pexpanded,-.25);
    $bmage = -0.581 - 0.225*pow($pexpanded,-.25);
    $cmage = -0.186 - 0.016*pow($pexpanded,-.25);
    $dmage = -0.014 + 0.096*$pexpanded;
    $emage = -0.814 - 0.811*$pexpanded;
    $fmage = -0.050 + 0.168*$pexpanded;
    
    $xplot = array();
    $iloop = 0;
    while ($iloop < 10000)
    {
        $xplot[$iloop] = ($iloop-200.0)/120.0;
        $iloop = $iloop + 1.0;
    }
    
    $loctime = $ltime*pi()/12.0;
    $xplot = -1.0*$r*cos($loctime)/120.;
    $y_point = $r*sin($loctime);
    if ($r > 200.0) {
        $r = 200.0;
    }
    
    $xplot = -1.0*$r*cos($loctime)/120.;
    
    $bplot = $dmage + $fmage*$xplot;
    $aplot = $emage;
    $cplot = $amage + $bmage*$xplot + $cmage*($xplot*$xplot);
    $yplotplus =  (-1.*$bplot + pow(($bplot*$bplot - 4.0*$aplot*$cplot),.5))/(2.0*$aplot); // help
    $yplotminus = (-1.*$bplot - pow(($bplot*$bplot - 4.0*$aplot*$cplot),.5))/(2.0*$aplot);
    $yplotplus = -120.*$yplotplus;
    $yplotminus = -120.*$yplotminus;
    
    $is_inside = 0;
    if ($y_point < $yplotplus and $y_point > $yplotminus and ($bplot*$bplot - 4.0*$aplot*$cplot) > 0.0) {
        $is_inside = 1;
    }
    return $is_inside;
}











echo '<h3>Thank you for using our online mapping tool.</h3> <p>';
echo '<p>';

//latpt = latpt_input
//longpt = longpt_input
//rj_input = rj
//loctime_input = loctime
//sslong = sslong2
$sslong = $_POST['sslong'];
$latpt = $_POST['latitude'];
$longpt = $_POST['longitude'];
$rj = $_POST['rj'];
$loctime = $_POST['loctime'];
$username_set = 0;
if ($_POST['username'] != '') {
    $username_set = 1;
}


if ($rj == 15.0) {
    $rj = 15.0001;
}
$sslong = fmod($sslong,360.0);
if ($sslong == 0.0) {
    $sslong = 360.0;
}


$badpt = 0;
//;test if user-specified point is within the model validity range (15-150 Rj in the magnetosphere), local time given in decimal format 0-24 hours,
if ($_POST['mapping_type'] == 'ionosphere' and (abs($latpt) > 90.0 or $longpt > 360. or $longpt < 0.)) { // ionosphere to magnetosphere
    $badpt = 1;
    echo '<b>User Inputs:</b><br>';
    echo 'Mapping requested from the ionosphere to the magnetosphere.';
    echo '<br>';
    echo 'Subsolar longitude was '.$_POST['sslong'];
    echo ' degrees';
    echo '<br>';
    echo 'Input latitude was ' .$_POST['latitude'];
    echo ' degrees';
    echo '<br>';
    echo 'Input longitude was ' .$_POST['longitude'];
    echo ' degrees';
    echo '<p>';
    $message .= "<html>Thank you for using the online mapping tool.";
    $message .= "<p>";
    $message .= "Mapping requested: ionosphere to magnetosphere.<p>";
    $message .= "Inputs:<br>";
    $message .= "Subsolar longitude (degrees) = " .$sslong;
    $message .= "<br>";
    $message .= "Ionospheric latitude (degrees) = " .$_POST['latitude'];
    $message .= "<br>";
    $message .= "Ionospheric longitude (degrees) = " .$_POST['longitude'];
    $message .= "<p>";
    
    echo '<p><b>Point could not be mapped because input values are not valid.</b> <br>';
    echo 'Input latitude should be from -90 to 90 degrees.<br>';
    echo 'Input longitude should be from 0 to 360 degrees.<br>';
    echo 'Input subsolar longitude should be from 0 to 360 degrees.<br>';
    $message .= "Point could not be mapped because input values are not valid.";
    $message .= "<br>";
    $message .= "Input latitude should be from -90 to 90 degrees.";
    $message .= "<br>";
    $message .= "Input longitude should be from 0 to 360 degrees.";
    $message .= "<br>";
    $message .= "Input subsolar longitude should be from 0 to 360 degrees.";
    $message .= "<br>";
} elseif ($_POST['mapping_type'] != 'ionosphere' and ($rj > 150. or $rj < 15. or $loctime < 0. or $loctime > 24. or inside_magnetosphere($rj,$loctime) == 0)) {
    $badpt = 1;
    echo '<b>User Inputs:</b><br>';
    echo 'Mapping requested from the magnetosphere to the ionosphere.';
    echo '<br>';
    echo 'Subsolar longitude was '.$_POST['sslong'];
    echo ' degrees';
    echo '<br>';
    echo 'Input radial distance was ' .$_POST['rj'];
    echo ' Jovian radii';
    echo '<br>';
    echo 'Input local time was ' .$_POST['loctime'];
    echo ' hours';
    echo '<p>';
    $message .= "<html>Thank you for using the online mapping tool.";
    $message .= "<p>";
    $message .= "Mapping requested: magnetosphere to ionosphere.<p>";
    $message .= "Inputs:<br>";
    $message .= "Subsolar longitude (degrees) = " .$sslong;
    $message .= "<br>";
    $message .= "Radial distance (Jovian radii) = " .$_POST['rj'];
    $message .= "<br>";
    $message .= "Local time (hours) = " .$_POST['loctime'];
    $message .= "<p>";
    if (inside_magnetosphere($rj,$loctime) == 0) {
        echo '<p><b>Point could not be mapped because input position is located outside the Joy et al. [2002] expanded magnetopause.</b> <br>';
        $message .= "Point could not be mapped because input position is located outside the Joy et al. [2002] expanded magnetopause.";
        $message .= "<br>";
    } else {
        echo '<p><b>Point could not be mapped because input values are not valid.</b> <br>';
        echo 'Input radial distance should be from 15 to 150 Jovian radii.<br>';
        echo 'Input local time should be from 0 to 24 hours.<br>';
        echo 'Input subsolar longitude should be from 0 to 360 degrees.<br>';
        $message .= "Point could not be mapped because input values are not valid.";
        $message .= "<br>";
        $message .= "Input radial distance should be from 15 to 150 Jovian radii.";
        $message .= "<br>";
        $message .= "Input local time should be from 0 to 24 hours.";
        $message .= "<br>";
        $message .= "Input subsolar longitude should be from 0 to 360 degrees.";
        $message .= "<br>";	}
} else {
    // try mapping function
    if ($_POST['mapping_type'] == 'ionosphere') {
        // do mapping for ionosphere -> magnetosphere
        if (fmod($sslong,10.0) == 0.) {
            //do mapping only once
            if ($latpt > 0) {
                $mapped_point_gam = map_ion_to_mag($latpt,$longpt,$sslong,'gam');
                $mapped_point_gam_tracing = map_ion_to_mag_tracing($latpt,$longpt,$sslong,'gam');
            }
            $mapped_point_vip4 = map_ion_to_mag($latpt,$longpt,$sslong,'vip4');
            $mapped_point_vipal = map_ion_to_mag($latpt,$longpt,$sslong,'vipal');
            $mapped_point_jrm09 = map_ion_to_mag($latpt,$longpt,$sslong,'jrm09');
            $mapped_point_vip4_tracing = map_ion_to_mag_tracing($latpt,$longpt,$sslong,'vip4');
            $mapped_point_vipal_tracing = map_ion_to_mag_tracing($latpt,$longpt,$sslong,'vipal');
            $mapped_point_jrm09_tracing = map_ion_to_mag_tracing($latpt,$longpt,$sslong,'jrm09');
            $mapped_point_kk2009_tracing = map_ion_to_mag_tracing($latpt,$longpt,$sslong,'kk2009');
            $mapped_point_kk2009ext_jrm09int_tracing = map_ion_to_mag_tracing($latpt,$longpt,$sslong,'kk2009ext_jrm09int');
            // need to check if point maps beyond dayside magnetopause
        } else {
            // do mapping and interpolate
            $first_sslong = $sslong - fmod($sslong,10.);
            $second_sslong = $first_sslong + 10.0;
            $sslong_first_interpol = $first_sslong;
            if ($first_sslong == 0.0 or $first_sslong == 360.0) {
                $sslong_first_interpol = 0.;
            }
            if ($latpt > 0) {
                $mapped_point_gam_first = map_ion_to_mag($latpt,$longpt,$first_sslong,'gam');
                $mapped_point_gam_second = map_ion_to_mag($latpt,$longpt,$second_sslong,'gam');
                //				printf("first mapped point %f %f",$mapped_point_gam_first[0],$mapped_point_gam_first[1]);
                //				printf("second mapped point %f %f",$mapped_point_gam_second[0],$mapped_point_gam_second[1]);
                //				echo '<p>';
                $first_x = $mapped_point_gam_first[0]*cos($mapped_point_gam_first[1]*pi()/12.0);
                $first_y = $mapped_point_gam_first[0]*sin($mapped_point_gam_first[1]*pi()/12.0);
                $second_x = $mapped_point_gam_second[0]*cos($mapped_point_gam_second[1]*pi()/12.0);
                $second_y = $mapped_point_gam_second[0]*sin($mapped_point_gam_second[1]*pi()/12.0);
                $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
                $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
                $r_interpol = pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5);
                $ltime_interpol = fmod((atan2($y_interpol,$x_interpol)*12.0/pi() + 48.0),24.0);
                if (abs($mapped_point_gam_first[1]) > 200. or abs($mapped_point_gam_second[0]) > 200.) {
                    $r_interpol = $mapped_point_gam_first[0];
                    $ltime_interpol = $mapped_point_gam_first[1];
                }
                $mapped_point_gam = array();
                $mapped_point_gam[0] = $r_interpol;
                $mapped_point_gam[1] = $ltime_interpol;
                
                $mapped_point_gam_first_tracing = map_ion_to_mag_tracing($latpt,$longpt,$first_sslong,'gam');
                $mapped_point_gam_second_tracing = map_ion_to_mag_tracing($latpt,$longpt,$second_sslong,'gam');
                $first_x = $mapped_point_gam_first_tracing[0]*cos($mapped_point_gam_first_tracing[1]*pi()/12.0);
                $first_y = $mapped_point_gam_first_tracing[0]*sin($mapped_point_gam_first_tracing[1]*pi()/12.0);
                $second_x = $mapped_point_gam_second_tracing[0]*cos($mapped_point_gam_second_tracing[1]*pi()/12.0);
                $second_y = $mapped_point_gam_second_tracing[0]*sin($mapped_point_gam_second_tracing[1]*pi()/12.0);
                $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
                $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
                $r_interpol = pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5);
                $ltime_interpol = fmod((atan2($y_interpol,$x_interpol)*12.0/pi() + 48.0),24.0);
                if (abs($mapped_point_gam_first_tracing[1]) > 200. or abs($mapped_point_gam_second_tracing[0]) > 200.) {
                    $r_interpol = $mapped_point_gam_first_tracing[0];
                    $ltime_interpol = $mapped_point_gam_first_tracing[1];
                }
                $mapped_point_gam_tracing = array();
                $mapped_point_gam_tracing[0] = $r_interpol;
                $mapped_point_gam_tracing[1] = $ltime_interpol;
            }
            
            $mapped_point_vip4_first = map_ion_to_mag($latpt,$longpt,$first_sslong,'vip4');
            $mapped_point_vip4_second = map_ion_to_mag($latpt,$longpt,$second_sslong,'vip4');
            //			printf("first mapped point %f %f",$mapped_point_vip4_first[0],$mapped_point_vip4_first[1]);
            //			printf("second mapped point %f %f",$mapped_point_vip4_second[0],$mapped_point_vip4_second[1]);
            //			echo '<p>';
            $first_x = $mapped_point_vip4_first[0]*cos($mapped_point_vip4_first[1]*pi()/12.0);
            $first_y = $mapped_point_vip4_first[0]*sin($mapped_point_vip4_first[1]*pi()/12.0);
            $second_x = $mapped_point_vip4_second[0]*cos($mapped_point_vip4_second[1]*pi()/12.0);
            $second_y = $mapped_point_vip4_second[0]*sin($mapped_point_vip4_second[1]*pi()/12.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $r_interpol = pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5);
            $ltime_interpol = fmod((atan2($y_interpol,$x_interpol)*12.0/pi() + 48.0),24.0);
            if (abs($mapped_point_vip4_first[1]) > 200. or abs($mapped_point_vip4_second[0]) > 200.) {
                $r_interpol = $mapped_point_vip4_first[0];
                $ltime_interpol = $mapped_point_vip4_first[1];
            }
            $mapped_point_vip4 = array();
            $mapped_point_vip4[0] = $r_interpol;
            $mapped_point_vip4[1] = $ltime_interpol;
            
            $mapped_point_vipal_first = map_ion_to_mag($latpt,$longpt,$first_sslong,'vipal');
            $mapped_point_vipal_second = map_ion_to_mag($latpt,$longpt,$second_sslong,'vipal');
            //			printf("first mapped point %f %f",$mapped_point_vipal_first[0],$mapped_point_vipal_first[1]);
            //			printf("second mapped point %f %f",$mapped_point_vipal_second[0],$mapped_point_vipal_second[1]);
            //			echo '<p>';
            $first_x = $mapped_point_vipal_first[0]*cos($mapped_point_vipal_first[1]*pi()/12.0);
            $first_y = $mapped_point_vipal_first[0]*sin($mapped_point_vipal_first[1]*pi()/12.0);
            $second_x = $mapped_point_vipal_second[0]*cos($mapped_point_vipal_second[1]*pi()/12.0);
            $second_y = $mapped_point_vipal_second[0]*sin($mapped_point_vipal_second[1]*pi()/12.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $r_interpol = pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5);
            $ltime_interpol = fmod((atan2($y_interpol,$x_interpol)*12.0/pi() + 48.0),24.0);
            if (abs($mapped_point_vipal_first[1]) > 200. or abs($mapped_point_vipal_second[0]) > 200.) {
                $r_interpol = $mapped_point_vipal_first[0];
                $ltime_interpol = $mapped_point_vipal_first[1];
            }
            $mapped_point_vipal = array();
            $mapped_point_vipal[0] = $r_interpol;
            $mapped_point_vipal[1] = $ltime_interpol;
            
            $mapped_point_jrm09_first = map_ion_to_mag($latpt,$longpt,$first_sslong,'jrm09');
            $mapped_point_jrm09_second = map_ion_to_mag($latpt,$longpt,$second_sslong,'jrm09');
            //			printf("first mapped point %f %f",$mapped_point_jrm09_first[0],$mapped_point_jrm09_first[1]);
            //			printf("second mapped point %f %f",$mapped_point_jrm09_second[0],$mapped_point_jrm09_second[1]);
            //			echo '<p>';
            $first_x = $mapped_point_jrm09_first[0]*cos($mapped_point_jrm09_first[1]*pi()/12.0);
            $first_y = $mapped_point_jrm09_first[0]*sin($mapped_point_jrm09_first[1]*pi()/12.0);
            $second_x = $mapped_point_jrm09_second[0]*cos($mapped_point_jrm09_second[1]*pi()/12.0);
            $second_y = $mapped_point_jrm09_second[0]*sin($mapped_point_jrm09_second[1]*pi()/12.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $r_interpol = pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5);
            $ltime_interpol = fmod((atan2($y_interpol,$x_interpol)*12.0/pi() + 48.0),24.0);
            if (abs($mapped_point_jrm09_first[1]) > 200. or abs($mapped_point_jrm09_second[0]) > 200.) {
                $r_interpol = $mapped_point_jrm09_first[0];
                $ltime_interpol = $mapped_point_jrm09_first[1];
            }
            $mapped_point_jrm09 = array();
            $mapped_point_jrm09[0] = $r_interpol;
            $mapped_point_jrm09[1] = $ltime_interpol;
            
            
            $mapped_point_vip4_first_tracing = map_ion_to_mag_tracing($latpt,$longpt,$first_sslong,'vip4');
            $mapped_point_vip4_second_tracing = map_ion_to_mag_tracing($latpt,$longpt,$second_sslong,'vip4');
            $first_x = $mapped_point_vip4_first_tracing[0]*cos($mapped_point_vip4_first_tracing[1]*pi()/12.0);
            $first_y = $mapped_point_vip4_first_tracing[0]*sin($mapped_point_vip4_first_tracing[1]*pi()/12.0);
            $second_x = $mapped_point_vip4_second_tracing[0]*cos($mapped_point_vip4_second_tracing[1]*pi()/12.0);
            $second_y = $mapped_point_vip4_second_tracing[0]*sin($mapped_point_vip4_second_tracing[1]*pi()/12.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $r_interpol = pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5);
            $ltime_interpol = fmod((atan2($y_interpol,$x_interpol)*12.0/pi() + 48.0),24.0);
            if (abs($mapped_point_vip4_first_tracing[1]) > 200. or abs($mapped_point_vip4_second_tracing[0]) > 200.) {
                $r_interpol = $mapped_point_vip4_first_tracing[0];
                $ltime_interpol = $mapped_point_vip4_first_tracing[1];
            }
            $mapped_point_vip4_tracing = array();
            $mapped_point_vip4_tracing[0] = $r_interpol;
            $mapped_point_vip4_tracing[1] = $ltime_interpol;
            
            $mapped_point_vipal_first_tracing = map_ion_to_mag_tracing($latpt,$longpt,$first_sslong,'vipal');
            $mapped_point_vipal_second_tracing = map_ion_to_mag_tracing($latpt,$longpt,$second_sslong,'vipal');
            $first_x = $mapped_point_vipal_first_tracing[0]*cos($mapped_point_vipal_first_tracing[1]*pi()/12.0);
            $first_y = $mapped_point_vipal_first_tracing[0]*sin($mapped_point_vipal_first_tracing[1]*pi()/12.0);
            $second_x = $mapped_point_vipal_second_tracing[0]*cos($mapped_point_vipal_second_tracing[1]*pi()/12.0);
            $second_y = $mapped_point_vipal_second_tracing[0]*sin($mapped_point_vipal_second_tracing[1]*pi()/12.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $r_interpol = pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5);
            $ltime_interpol = fmod((atan2($y_interpol,$x_interpol)*12.0/pi() + 48.0),24.0);
            if (abs($mapped_point_vipal_first_tracing[1]) > 200. or abs($mapped_point_vipal_second_tracing[0]) > 200.) {
                $r_interpol = $mapped_point_vipal_first_tracing[0];
                $ltime_interpol = $mapped_point_vipal_first_tracing[1];
            }
            $mapped_point_vipal_tracing = array();
            $mapped_point_vipal_tracing[0] = $r_interpol;
            $mapped_point_vipal_tracing[1] = $ltime_interpol;
            
            $mapped_point_jrm09_first_tracing = map_ion_to_mag_tracing($latpt,$longpt,$first_sslong,'jrm09');
            $mapped_point_jrm09_second_tracing = map_ion_to_mag_tracing($latpt,$longpt,$second_sslong,'jrm09');
            $first_x = $mapped_point_jrm09_first_tracing[0]*cos($mapped_point_jrm09_first_tracing[1]*pi()/12.0);
            $first_y = $mapped_point_jrm09_first_tracing[0]*sin($mapped_point_jrm09_first_tracing[1]*pi()/12.0);
            $second_x = $mapped_point_jrm09_second_tracing[0]*cos($mapped_point_jrm09_second_tracing[1]*pi()/12.0);
            $second_y = $mapped_point_jrm09_second_tracing[0]*sin($mapped_point_jrm09_second_tracing[1]*pi()/12.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $r_interpol = pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5);
            $ltime_interpol = fmod((atan2($y_interpol,$x_interpol)*12.0/pi() + 48.0),24.0);
            if (abs($mapped_point_jrm09_first_tracing[1]) > 200. or abs($mapped_point_jrm09_second_tracing[0]) > 200.) {
                $r_interpol = $mapped_point_jrm09_first_tracing[0];
                $ltime_interpol = $mapped_point_jrm09_first_tracing[1];
            }
            $mapped_point_jrm09_tracing = array();
            $mapped_point_jrm09_tracing[0] = $r_interpol;
            $mapped_point_jrm09_tracing[1] = $ltime_interpol;
            
            $mapped_point_kk2009_first_tracing = map_ion_to_mag_tracing($latpt,$longpt,$first_sslong,'kk2009');
            $mapped_point_kk2009_second_tracing = map_ion_to_mag_tracing($latpt,$longpt,$second_sslong,'kk2009');
            $first_x = $mapped_point_kk2009_first_tracing[0]*cos($mapped_point_kk2009_first_tracing[1]*pi()/12.0);
            $first_y = $mapped_point_kk2009_first_tracing[0]*sin($mapped_point_kk2009_first_tracing[1]*pi()/12.0);
            $second_x = $mapped_point_kk2009_second_tracing[0]*cos($mapped_point_kk2009_second_tracing[1]*pi()/12.0);
            $second_y = $mapped_point_kk2009_second_tracing[0]*sin($mapped_point_kk2009_second_tracing[1]*pi()/12.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $r_interpol = pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5);
            $ltime_interpol = fmod((atan2($y_interpol,$x_interpol)*12.0/pi() + 48.0),24.0);
            if (abs($mapped_point_kk2009_first_tracing[1]) > 200. or abs($mapped_point_kk2009_second_tracing[0]) > 200.) {
                $r_interpol = $mapped_point_kk2009_first_tracing[0];
                $ltime_interpol = $mapped_point_kk2009_first_tracing[1];
            }
            $mapped_point_kk2009_tracing = array();
            $mapped_point_kk2009_tracing[0] = $r_interpol;
            $mapped_point_kk2009_tracing[1] = $ltime_interpol;
            
            $mapped_point_kk2009ext_jrm09int_first_tracing = map_ion_to_mag_tracing($latpt,$longpt,$first_sslong,'kk2009ext_jrm09int');
            $mapped_point_kk2009ext_jrm09int_second_tracing = map_ion_to_mag_tracing($latpt,$longpt,$second_sslong,'kk2009ext_jrm09int');
            $first_x = $mapped_point_kk2009ext_jrm09int_first_tracing[0]*cos($mapped_point_kk2009ext_jrm09int_first_tracing[1]*pi()/12.0);
            $first_y = $mapped_point_kk2009ext_jrm09int_first_tracing[0]*sin($mapped_point_kk2009ext_jrm09int_first_tracing[1]*pi()/12.0);
            $second_x = $mapped_point_kk2009ext_jrm09int_second_tracing[0]*cos($mapped_point_kk2009ext_jrm09int_second_tracing[1]*pi()/12.0);
            $second_y = $mapped_point_kk2009ext_jrm09int_second_tracing[0]*sin($mapped_point_kk2009ext_jrm09int_second_tracing[1]*pi()/12.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $r_interpol = pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5);
            $ltime_interpol = fmod((atan2($y_interpol,$x_interpol)*12.0/pi() + 48.0),24.0);
            if (abs($mapped_point_kk2009ext_jrm09int_first_tracing[1]) > 200. or abs($mapped_point_kk2009ext_jrm09int_second_tracing[0]) > 200.) {
                $r_interpol = $mapped_point_kk2009ext_jrm09int_first_tracing[0];
                $ltime_interpol = $mapped_point_kk2009ext_jrm09int_first_tracing[1];
            }
            $mapped_point_kk2009ext_jrm09int_tracing = array();
            $mapped_point_kk2009ext_jrm09int_tracing[0] = $r_interpol;
            $mapped_point_kk2009ext_jrm09int_tracing[1] = $ltime_interpol;
            
        } // end sslong mod 10 !=0
        
        echo '<b>User Inputs:</b><br>';
        echo 'Mapping requested from the ionosphere to the magnetosphere.';
        echo '<br>';
        echo 'Subsolar longitude was '.$_POST['sslong'];
        echo ' degrees';
        echo '<br>';
        echo 'Input latitude was ' .$_POST['latitude'];
        echo ' degrees';
        echo '<br>';
        echo 'Input longitude was ' .$_POST['longitude'];
        echo ' degrees';
        echo '<p>';
        $message = "<html>Thank you for using the online mapping tool.";
        $message .= "<p>";
        $message .= "Mapping requested: ionosphere to magnetosphere.<p>";
        $message .= "Inputs:<br>";
        $message .= "Subsolar longitude (degrees) = " .$sslong;
        $message .= "<br>";
        $message .= "Ionospheric latitude (degrees) = " .$_POST['latitude'];
        $message .= "<br>";
        $message .= "Ionospheric longitude (degrees) = " .$_POST['longitude'];
        $message .= "<p>";
        
        echo '<p><b>Results using flux equivalence calculation with VIP4:</b> <br>';
        $message .= "Results using flux equivalence calculation with VIP4:";
        $message .= "<br>";
        //				echo 'Radial distance (Jovian radii) = ' .$r_interpol;
        if ($mapped_point_vip4[0] == -998.) {
            printf("Point maps inside of 15 Jovian radii.");
            $message .= "Point maps inside of 15 Jovian radii.";
            $message .= "<p>";
            echo '<p>';
        } elseif (abs($mapped_point_vip4[0]) > 150. or inside_magnetosphere(abs($mapped_point_vip4[0]),$mapped_point_vip4[1]) == 0) {
            printf("Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.");
            $message .= "Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.";
            $message .= "<p>";
            echo '<p>';
        }
        else {
            printf('Radial distance (Jovian radii) = %0.3f', $mapped_point_vip4[0]);
            echo '<br>';
            //				echo 'Local time (hours) = ' .$ltime_interpol;
            printf('Local time (hours) = %0.3f', $mapped_point_vip4[1]);
            echo '<p>';
            $message .= "Radial distance (Jovian radii) = " .$mapped_point_vip4[0];
            $message .= "<br>";
            $message .= "Local time (hours) = " .$mapped_point_vip4[1];
            $message .= "<p>";
        }
        if ($latpt > 0) {
            echo '<p><b>Results using flux equivalence calculation with the Grodent anomaly model:</b> <br>';
            $message .= "Results using flux equivalence calculation with the Grodent anomaly model:";
            $message .= "<br>";
            if ($mapped_point_gam[0] == -998.) {
                printf("Point maps inside of 15 Jovian radii.");
                $message .= "Point maps inside of 15 Jovian radii.";
                $message .= "<p>";
                echo '<p>';
            } elseif (abs($mapped_point_gam[0]) > 150. or inside_magnetosphere(abs($mapped_point_gam[0]),$mapped_point_gam[1]) == 0) {
                printf("Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.");
                $message .= "Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.";
                $message .= "<p>";
                echo '<p>';
            }
            else {
                //				echo 'Radial distance (Jovian radii) = ' .$r_interpol;
                printf('Radial distance (Jovian radii) = %0.3f', $mapped_point_gam[0]);
                echo '<br>';
                //				echo 'Local time (hours) = ' .$ltime_interpol;
                printf('Local time (hours) = %0.3f', $mapped_point_gam[1]);
                echo '<p>';
                $message .= "Radial distance (Jovian radii) = " .$mapped_point_gam[0];
                $message .= "<br>";
                $message .= "Local time (hours) = " .$mapped_point_gam[1];
                $message .= "<p>";
            }
        }
        echo '<p><b>Results using flux equivalence calculation with VIPAL:</b> <br>';
        $message .= "Results using flux equivalence calculation with VIPAL:";
        $message .= "<br>";
        if ($mapped_point_vipal[0] == -998.) {
            printf("Point maps inside of 15 Jovian radii.");
            $message .= "Point maps inside of 15 Jovian radii.";
            $message .= "<p>";
            echo '<p>';
        } elseif (abs($mapped_point_vipal[0]) > 150. or inside_magnetosphere(abs($mapped_point_vipal[0]),$mapped_point_vipal[1]) == 0) {
            printf("Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.");
            $message .= "Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.";
            $message .= "<p>";
            echo '<p>';
        }
        else {
            printf('Radial distance (Jovian radii) = %0.3f', $mapped_point_vipal[0]);
            echo '<br>';
            //				echo 'Local time (hours) = ' .$ltime_interpol;
            printf('Local time (hours) = %0.3f', $mapped_point_vipal[1]);
            echo '<p>';
            $message .= "Radial distance (Jovian radii) = " .$mapped_point_vipal[0];
            $message .= "<br>";
            $message .= "Local time (hours) = " .$mapped_point_vipal[1];
            $message .= "<p>";
        }
        echo '<p><b>Results using flux equivalence calculation with JRM09:</b> <br>';
        $message .= "Results using flux equivalence calculation with JRM09:";
        $message .= "<br>";
        if ($mapped_point_jrm09[0] == -998.) {
            printf("Point maps inside of 15 Jovian radii.");
            $message .= "Point maps inside of 15 Jovian radii.";
            $message .= "<p>";
            echo '<p>';
        } elseif (abs($mapped_point_jrm09[0]) > 150. or inside_magnetosphere(abs($mapped_point_jrm09[0]),$mapped_point_jrm09[1]) == 0) {
            printf("Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.");
            $message .= "Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.";
            $message .= "<p>";
            echo '<p>';
        }
        else {
            printf('Radial distance (Jovian radii) = %0.3f', $mapped_point_jrm09[0]);
            echo '<br>';
            //				echo 'Local time (hours) = ' .$ltime_interpol;
            printf('Local time (hours) = %0.3f', $mapped_point_jrm09[1]);
            echo '<p>';
            $message .= "Radial distance (Jovian radii) = " .$mapped_point_jrm09[0];
            $message .= "<br>";
            $message .= "Local time (hours) = " .$mapped_point_jrm09[1];
            $message .= "<p>";
        }
        
        
        echo '<p><b>Results using fieldline tracing with VIP4:</b> <br>';
        $message .= "Results using fieldline tracing with VIP4:";
        $message .= "<br>";
        //				echo 'Radial distance (Jovian radii) = ' .$r_interpol;
        if ($mapped_point_vip4_tracing[0] == -998.) {
            printf("Point maps inside of 15 Jovian radii.");
            $message .= "Point maps inside of 15 Jovian radii.";
            $message .= "<p>";
            echo '<p>';
        } elseif (abs($mapped_point_vip4_tracing[0]) > 150. or inside_magnetosphere(abs($mapped_point_vip4_tracing[0]),$mapped_point_vip4_tracing[1]) == 0) {
            printf("Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.");
            $message .= "Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.";
            $message .= "<p>";
            echo '<p>';
        }
        else {
            printf('Radial distance (Jovian radii) = %0.3f', $mapped_point_vip4_tracing[0]);
            echo '<br>';
            //				echo 'Local time (hours) = ' .$ltime_interpol;
            printf('Local time (hours) = %0.3f', $mapped_point_vip4_tracing[1]);
            echo '<p>';
            $message .= "Radial distance (Jovian radii) = " .$mapped_point_vip4_tracing[0];
            $message .= "<br>";
            $message .= "Local time (hours) = " .$mapped_point_vip4_tracing[1];
            $message .= "<p>";
        }
        if ($latpt > 0) {
            echo '<p><b>Results using fieldline tracing with the Grodent anomaly model:</b> <br>';
            $message .= "Results using fieldline tracing with the Grodent anomaly model:";
            $message .= "<br>";
            if ($mapped_point_gam_tracing[0] == -998.) {
                printf("Point maps inside of 15 Jovian radii.");
                $message .= "Point maps inside of 15 Jovian radii.";
                $message .= "<p>";
                echo '<p>';
            } elseif (abs($mapped_point_gam_tracing[0]) > 150. or inside_magnetosphere(abs($mapped_point_gam_tracing[0]),$mapped_point_gam_tracing[1]) == 0) {
                printf("Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.");
                $message .= "Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.";
                $message .= "<p>";
                echo '<p>';
            }
            else {
                //				echo 'Radial distance (Jovian radii) = ' .$r_interpol;
                printf('Radial distance (Jovian radii) = %0.3f', $mapped_point_gam_tracing[0]);
                echo '<br>';
                //				echo 'Local time (hours) = ' .$ltime_interpol;
                printf('Local time (hours) = %0.3f', $mapped_point_gam_tracing[1]);
                echo '<p>';
                $message .= "Radial distance (Jovian radii) = " .$mapped_point_gam_tracing[0];
                $message .= "<br>";
                $message .= "Local time (hours) = " .$mapped_point_gam_tracing[1];
                $message .= "<p>";
            }
        }
        echo '<p><b>Results using fieldline tracing with VIPAL:</b> <br>';
        $message .= "Results using fieldline tracing with VIPAL:";
        $message .= "<br>";
        if ($mapped_point_vipal_tracing[0] == -998.) {
            printf("Point maps inside of 15 Jovian radii.");
            $message .= "Point maps inside of 15 Jovian radii.";
            $message .= "<p>";
            echo '<p>';
        } elseif (abs($mapped_point_vipal_tracing[0]) > 150. or inside_magnetosphere(abs($mapped_point_vipal_tracing[0]),$mapped_point_vipal_tracing[1]) == 0) {
            printf("Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.");
            $message .= "Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.";
            $message .= "<p>";
            echo '<p>';
        }
        else {
            printf('Radial distance (Jovian radii) = %0.3f', $mapped_point_vipal_tracing[0]);
            echo '<br>';
            //				echo 'Local time (hours) = ' .$ltime_interpol;
            printf('Local time (hours) = %0.3f', $mapped_point_vipal_tracing[1]);
            echo '<p>';
            $message .= "Radial distance (Jovian radii) = " .$mapped_point_vipal_tracing[0];
            $message .= "<br>";
            $message .= "Local time (hours) = " .$mapped_point_vipal_tracing[1];
            $message .= "<p>";
        }
        echo '<p><b>Results using fieldline tracing with JRM09:</b> <br>';
        $message .= "Results using fieldline tracing with JRM09:";
        $message .= "<br>";
        if ($mapped_point_jrm09_tracing[0] == -998.) {
            printf("Point maps inside of 15 Jovian radii.");
            $message .= "Point maps inside of 15 Jovian radii.";
            $message .= "<p>";
            echo '<p>';
        } elseif (abs($mapped_point_jrm09_tracing[0]) > 150. or inside_magnetosphere(abs($mapped_point_jrm09_tracing[0]),$mapped_point_jrm09_tracing[1]) == 0) {
            printf("Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.");
            $message .= "Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.";
            $message .= "<p>";
            echo '<p>';
        }
        else {
            printf('Radial distance (Jovian radii) = %0.3f', $mapped_point_jrm09_tracing[0]);
            echo '<br>';
            //				echo 'Local time (hours) = ' .$ltime_interpol;
            printf('Local time (hours) = %0.3f', $mapped_point_jrm09_tracing[1]);
            echo '<p>';
            $message .= "Radial distance (Jovian radii) = " .$mapped_point_jrm09_tracing[0];
            $message .= "<br>";
            $message .= "Local time (hours) = " .$mapped_point_jrm09_tracing[1];
            $message .= "<p>";
        }
        echo '<p><b>Results using fieldline tracing with the Khurana model:</b> <br>';
        $message .= "Results using fieldline tracing with the Khurana model:";
        $message .= "<br>";
        if ($mapped_point_kk2009_tracing[0] == -998.) {
            printf("Point maps inside of 15 Jovian radii.");
            $message .= "Point maps inside of 15 Jovian radii.";
            $message .= "<p>";
            echo '<p>';
        } elseif (abs($mapped_point_kk2009_tracing[0]) > 150. or inside_magnetosphere(abs($mapped_point_kk2009_tracing[0]),$mapped_point_kk2009_tracing[1]) == 0) {
            printf("Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.");
            $message .= "Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.";
            $message .= "<p>";
            echo '<p>';
        }
        else {
            printf('Radial distance (Jovian radii) = %0.3f', $mapped_point_kk2009_tracing[0]);
            echo '<br>';
            //				echo 'Local time (hours) = ' .$ltime_interpol;
            printf('Local time (hours) = %0.3f', $mapped_point_kk2009_tracing[1]);
            echo '<p>';
            $message .= "Radial distance (Jovian radii) = " .$mapped_point_kk2009_tracing[0];
            $message .= "<br>";
            $message .= "Local time (hours) = " .$mapped_point_kk2009_tracing[1];
            $message .= "<p>";
        }
        echo '<p><b>Results using fieldline tracing with the Khurana model (current sheet) and JRM09 (internal field):</b> <br>';
        $message .= "Results using fieldline tracing with the Khurana model (current sheet) JRM09 (internal field):";
        $message .= "<br>";
        if ($mapped_point_kk2009ext_jrm09int_tracing[0] == -998.) {
            printf("Point maps inside of 15 Jovian radii.");
            $message .= "Point maps inside of 15 Jovian radii.";
            $message .= "<p>";
            echo '<p>';
        } elseif (abs($mapped_point_kk2009ext_jrm09int_tracing[0]) > 150. or inside_magnetosphere(abs($mapped_point_kk2009ext_jrm09int_tracing[0]),$mapped_point_kk2009ext_jrm09int_tracing[1]) == 0) {
            printf("Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.");
            $message .= "Point maps beyond 150 Jovian radii or beyond the dayside magnetopause.";
            $message .= "<p>";
            echo '<p>';
        }
        else {
            printf('Radial distance (Jovian radii) = %0.3f', $mapped_point_kk2009ext_jrm09int_tracing[0]);
            echo '<br>';
            //				echo 'Local time (hours) = ' .$ltime_interpol;
            printf('Local time (hours) = %0.3f', $mapped_point_kk2009ext_jrm09int_tracing[1]);
            echo '<p>';
            $message .= "Radial distance (Jovian radii) = " .$mapped_point_kk2009ext_jrm09int_tracing[0];
            $message .= "<br>";
            $message .= "Local time (hours) = " .$mapped_point_kk2009ext_jrm09int_tracing[1];
            $message .= "<p>";
        }
        
    } else {
        // do mapping for magnetosphere -> ionosphere
        if (fmod($sslong,10.0) == 0.) {
            //do mapping only once
            //echo 'Code is here';
            $mapped_point_vip4_north = map_mag_to_ion($rj,$loctime,$sslong,'vip4','north');
            $mapped_point_gam = map_mag_to_ion($rj,$loctime,$sslong,'gam','north');
            $mapped_point_vipal_north = map_mag_to_ion($rj,$loctime,$sslong,'vipal','north');
            $mapped_point_jrm09_north = map_mag_to_ion($rj,$loctime,$sslong,'jrm09','north');
            $mapped_point_vip4_south = map_mag_to_ion($rj,$loctime,$sslong,'vip4','south');
            $mapped_point_vipal_south = map_mag_to_ion($rj,$loctime,$sslong,'vipal','south');
            $mapped_point_jrm09_south = map_mag_to_ion($rj,$loctime,$sslong,'jrm09','south');
            
            $mapped_point_vip4_north_tracing = map_mag_to_ion_tracing($rj,$loctime,$sslong,'vip4','north');
            $mapped_point_gam_tracing = map_mag_to_ion_tracing($rj,$loctime,$sslong,'gam','north');
            $mapped_point_vipal_north_tracing = map_mag_to_ion_tracing($rj,$loctime,$sslong,'vipal','north');
            $mapped_point_jrm09_north_tracing = map_mag_to_ion_tracing($rj,$loctime,$sslong,'jrm09','north');
            $mapped_point_kk2009_north_tracing = map_mag_to_ion_tracing($rj,$loctime,$sslong,'kk2009','north');
            $mapped_point_kk2009ext_jrm09int_north_tracing = map_mag_to_ion_tracing($rj,$loctime,$sslong,'kk2009ext_jrm09int','north');
            $mapped_point_vip4_south_tracing = map_mag_to_ion_tracing($rj,$loctime,$sslong,'vip4','south');
            $mapped_point_vipal_south_tracing = map_mag_to_ion_tracing($rj,$loctime,$sslong,'vipal','south');
            $mapped_point_jrm09_south_tracing = map_mag_to_ion_tracing($rj,$loctime,$sslong,'jrm09','south');
            $mapped_point_kk2009_south_tracing = map_mag_to_ion_tracing($rj,$loctime,$sslong,'kk2009','south');
            $mapped_point_kk2009ext_jrm09int_south_tracing = map_mag_to_ion_tracing($rj,$loctime,$sslong,'kk2009ext_jrm09int','south');

        } else {
            // do mapping and interpolate
            $first_sslong = $sslong - fmod($sslong,10.);
            $second_sslong = $first_sslong + 10.0;
            $sslong_first_interpol = $first_sslong;
            if ($first_sslong == 0.0 or $first_sslong == 360.0) {
                $sslong_first_interpol = 0.;
            }
            
            //printf("sslong %f %f",$first_sslong,$second_sslong);
            $mapped_point_gam_first = map_mag_to_ion($rj,$loctime,$first_sslong,'gam','north');
            $mapped_point_gam_second = map_mag_to_ion($rj,$loctime,$second_sslong,'gam','north');
            //printf("first mapped point %f %f",$mapped_point_gam_first[0],$mapped_point_gam_first[1]);
            //printf("second mapped point %f %f",$mapped_point_gam_second[0],$mapped_point_gam_second[1]);
            //echo '<p>';
            $first_x = abs(sin((90.0-$mapped_point_gam_first[0])*pi()/180.0))*cos($mapped_point_gam_first[1]*pi()/180.0);
            $first_y = abs(sin((90.0-$mapped_point_gam_first[0])*pi()/180.0))*sin($mapped_point_gam_first[1]*pi()/180.0);
            $second_x = abs(sin((90.0-$mapped_point_gam_second[0])*pi()/180.0))*cos($mapped_point_gam_second[1]*pi()/180.0);
            $second_y = abs(sin((90.0-$mapped_point_gam_second[0])*pi()/180.0))*sin($mapped_point_gam_second[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = 90.0 - asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi();
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_gam_first[1]) > 400. or abs($mapped_point_gam_second[0]) > 400.) {
                $lat_interpol = $mapped_point_gam_first[0];
                $long_interpol = $mapped_point_gam_first[1];
            }
            $mapped_point_gam = array();
            $mapped_point_gam[0] = $lat_interpol;
            $mapped_point_gam[1] = $long_interpol;
            
            $mapped_point_vip4_first = map_mag_to_ion($rj,$loctime,$first_sslong,'vip4','north');
            $mapped_point_vip4_second = map_mag_to_ion($rj,$loctime,$second_sslong,'vip4','north');
            //				printf("first mapped point %f %f",$mapped_point_gam_first[0],$mapped_point_gam_first[1]);
            //				printf("second mapped point %f %f",$mapped_point_gam_second[0],$mapped_point_gam_second[1]);
            //				echo '<p>';vip4
            $first_x = abs(sin((90.0-$mapped_point_vip4_first[0])*pi()/180.0))*cos($mapped_point_vip4_first[1]*pi()/180.0);
            $first_y = abs(sin((90.0-$mapped_point_vip4_first[0])*pi()/180.0))*sin($mapped_point_vip4_first[1]*pi()/180.0);
            $second_x = abs(sin((90.0-$mapped_point_vip4_second[0])*pi()/180.0))*cos($mapped_point_vip4_second[1]*pi()/180.0);
            $second_y = abs(sin((90.0-$mapped_point_vip4_second[0])*pi()/180.0))*sin($mapped_point_vip4_second[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = 90.0 - asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi();
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_vip4_first[1]) > 400. or abs($mapped_point_vip4_second[0]) > 400.) {
                $lat_interpol = $mapped_point_vip4_first[0];
                $long_interpol = $mapped_point_vip4_first[1];
            }
            $mapped_point_vip4_north = array();
            $mapped_point_vip4_north[0] = $lat_interpol;
            $mapped_point_vip4_north[1] = $long_interpol;
            
            $mapped_point_vipal_first = map_mag_to_ion($rj,$loctime,$first_sslong,'vipal','north');
            $mapped_point_vipal_second = map_mag_to_ion($rj,$loctime,$second_sslong,'vipal','north');
            //				printf("first mapped point %f %f",$mapped_point_gam_first[0],$mapped_point_gam_first[1]);
            //				printf("second mapped point %f %f",$mapped_point_gam_second[0],$mapped_point_gam_second[1]);
            //				echo '<p>';vipal
            $first_x = abs(sin((90.0-$mapped_point_vipal_first[0])*pi()/180.0))*cos($mapped_point_vipal_first[1]*pi()/180.0);
            $first_y = abs(sin((90.0-$mapped_point_vipal_first[0])*pi()/180.0))*sin($mapped_point_vipal_first[1]*pi()/180.0);
            $second_x = abs(sin((90.0-$mapped_point_vipal_second[0])*pi()/180.0))*cos($mapped_point_vipal_second[1]*pi()/180.0);
            $second_y = abs(sin((90.0-$mapped_point_vipal_second[0])*pi()/180.0))*sin($mapped_point_vipal_second[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = 90.0 - asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi();
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_vipal_first[1]) > 400. or abs($mapped_point_vipal_second[0]) > 400.) {
                $lat_interpol = $mapped_point_vipal_first[0];
                $long_interpol = $mapped_point_vipal_first[1];
            }
            $mapped_point_vipal_north = array();
            $mapped_point_vipal_north[0] = $lat_interpol;
            $mapped_point_vipal_north[1] = $long_interpol;
            
            $mapped_point_jrm09_first = map_mag_to_ion($rj,$loctime,$first_sslong,'jrm09','north');
            $mapped_point_jrm09_second = map_mag_to_ion($rj,$loctime,$second_sslong,'jrm09','north');
            //				printf("first mapped point %f %f",$mapped_point_gam_first[0],$mapped_point_gam_first[1]);
            //				printf("second mapped point %f %f",$mapped_point_gam_second[0],$mapped_point_gam_second[1]);
            //				echo '<p>';jrm09
            $first_x = abs(sin((90.0-$mapped_point_jrm09_first[0])*pi()/180.0))*cos($mapped_point_jrm09_first[1]*pi()/180.0);
            $first_y = abs(sin((90.0-$mapped_point_jrm09_first[0])*pi()/180.0))*sin($mapped_point_jrm09_first[1]*pi()/180.0);
            $second_x = abs(sin((90.0-$mapped_point_jrm09_second[0])*pi()/180.0))*cos($mapped_point_jrm09_second[1]*pi()/180.0);
            $second_y = abs(sin((90.0-$mapped_point_jrm09_second[0])*pi()/180.0))*sin($mapped_point_jrm09_second[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = 90.0 - asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi();
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_jrm09_first[1]) > 400. or abs($mapped_point_jrm09_second[0]) > 400.) {
                $lat_interpol = $mapped_point_jrm09_first[0];
                $long_interpol = $mapped_point_jrm09_first[1];
            }
            $mapped_point_jrm09_north = array();
            $mapped_point_jrm09_north[0] = $lat_interpol;
            $mapped_point_jrm09_north[1] = $long_interpol;

            
            $mapped_point_vip4_first = map_mag_to_ion($rj,$loctime,$first_sslong,'vip4','south');
            $mapped_point_vip4_second = map_mag_to_ion($rj,$loctime,$second_sslong,'vip4','south');
            //				printf("first mapped point %f %f",$mapped_point_gam_first[0],$mapped_point_gam_first[1]);
            //				printf("second mapped point %f %f",$mapped_point_gam_second[0],$mapped_point_gam_second[1]);
            //				echo '<p>';vip4
            $first_x = abs(sin((90.0-abs($mapped_point_vip4_first[0]))*pi()/180.0))*cos($mapped_point_vip4_first[1]*pi()/180.0);
            $first_y = abs(sin((90.0-abs($mapped_point_vip4_first[0]))*pi()/180.0))*sin($mapped_point_vip4_first[1]*pi()/180.0);
            $second_x = abs(sin((90.0-abs($mapped_point_vip4_second[0]))*pi()/180.0))*cos($mapped_point_vip4_second[1]*pi()/180.0);
            $second_y = abs(sin((90.0-abs($mapped_point_vip4_second[0]))*pi()/180.0))*sin($mapped_point_vip4_second[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi() - 90.0;
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_vip4_first[1]) > 400. or abs($mapped_point_vip4_second[0]) > 400.) {
                $lat_interpol = $mapped_point_vip4_first[0];
                $long_interpol = $mapped_point_vip4_first[1];
            }
            $mapped_point_vip4_south = array();
            $mapped_point_vip4_south[0] = $lat_interpol;
            $mapped_point_vip4_south[1] = $long_interpol;			
            
            $mapped_point_vipal_first = map_mag_to_ion($rj,$loctime,$first_sslong,'vipal','south');
            $mapped_point_vipal_second = map_mag_to_ion($rj,$loctime,$second_sslong,'vipal','south');
            //				printf("first mapped point %f %f",$mapped_point_gam_first[0],$mapped_point_gam_first[1]);
            //				printf("second mapped point %f %f",$mapped_point_gam_second[0],$mapped_point_gam_second[1]);
            //				echo '<p>';vipal
            $first_x = abs(sin((90.0-abs($mapped_point_vipal_first[0]))*pi()/180.0))*cos($mapped_point_vipal_first[1]*pi()/180.0);
            $first_y = abs(sin((90.0-abs($mapped_point_vipal_first[0]))*pi()/180.0))*sin($mapped_point_vipal_first[1]*pi()/180.0);
            $second_x = abs(sin((90.0-abs($mapped_point_vipal_second[0]))*pi()/180.0))*cos($mapped_point_vipal_second[1]*pi()/180.0);
            $second_y = abs(sin((90.0-abs($mapped_point_vipal_second[0]))*pi()/180.0))*sin($mapped_point_vipal_second[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi() - 90.0;
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_vipal_first[1]) > 400. or abs($mapped_point_vipal_second[0]) > 400.) {
                $lat_interpol = $mapped_point_vipal_first[0];
                $long_interpol = $mapped_point_vipal_first[1];
            }	
            $mapped_point_vipal_south = array();
            $mapped_point_vipal_south[0] = $lat_interpol;
            $mapped_point_vipal_south[1] = $long_interpol;
            
            $mapped_point_jrm09_first = map_mag_to_ion($rj,$loctime,$first_sslong,'jrm09','south');
            $mapped_point_jrm09_second = map_mag_to_ion($rj,$loctime,$second_sslong,'jrm09','south');
            //				printf("first mapped point %f %f",$mapped_point_gam_first[0],$mapped_point_gam_first[1]);
            //				printf("second mapped point %f %f",$mapped_point_gam_second[0],$mapped_point_gam_second[1]);
            //				echo '<p>';jrm09
            $first_x = abs(sin((90.0-abs($mapped_point_jrm09_first[0]))*pi()/180.0))*cos($mapped_point_jrm09_first[1]*pi()/180.0);
            $first_y = abs(sin((90.0-abs($mapped_point_jrm09_first[0]))*pi()/180.0))*sin($mapped_point_jrm09_first[1]*pi()/180.0);
            $second_x = abs(sin((90.0-abs($mapped_point_jrm09_second[0]))*pi()/180.0))*cos($mapped_point_jrm09_second[1]*pi()/180.0);
            $second_y = abs(sin((90.0-abs($mapped_point_jrm09_second[0]))*pi()/180.0))*sin($mapped_point_jrm09_second[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi() - 90.0;
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_jrm09_first[1]) > 400. or abs($mapped_point_jrm09_second[0]) > 400.) {
                $lat_interpol = $mapped_point_jrm09_first[0];
                $long_interpol = $mapped_point_jrm09_first[1];
            }
            $mapped_point_jrm09_south = array();
            $mapped_point_jrm09_south[0] = $lat_interpol;
            $mapped_point_jrm09_south[1] = $long_interpol;

            
            
            
            
            
            $mapped_point_gam_first_tracing = map_mag_to_ion_tracing($rj,$loctime,$first_sslong,'gam','north');
            $mapped_point_gam_second_tracing = map_mag_to_ion_tracing($rj,$loctime,$second_sslong,'gam','north');
            $first_x = abs(sin((90.0-$mapped_point_gam_first_tracing[0])*pi()/180.0))*cos($mapped_point_gam_first_tracing[1]*pi()/180.0);
            $first_y = abs(sin((90.0-$mapped_point_gam_first_tracing[0])*pi()/180.0))*sin($mapped_point_gam_first_tracing[1]*pi()/180.0);
            $second_x = abs(sin((90.0-$mapped_point_gam_second_tracing[0])*pi()/180.0))*cos($mapped_point_gam_second_tracing[1]*pi()/180.0);
            $second_y = abs(sin((90.0-$mapped_point_gam_second_tracing[0])*pi()/180.0))*sin($mapped_point_gam_second_tracing[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = 90.0 - asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi();
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_gam_first_tracing[1]) > 400. or abs($mapped_point_gam_second_tracing[0]) > 400.) {
                $lat_interpol = $mapped_point_gam_first_tracing[0];
                $long_interpol = $mapped_point_gam_first_tracing[1];
            }
            $mapped_point_gam_tracing = array();
            $mapped_point_gam_tracing[0] = $lat_interpol;
            $mapped_point_gam_tracing[1] = $long_interpol;
            
            $mapped_point_vip4_first_tracing = map_mag_to_ion_tracing($rj,$loctime,$first_sslong,'vip4','north');
            $mapped_point_vip4_second_tracing = map_mag_to_ion_tracing($rj,$loctime,$second_sslong,'vip4','north');
            $first_x = abs(sin((90.0-$mapped_point_vip4_first_tracing[0])*pi()/180.0))*cos($mapped_point_vip4_first_tracing[1]*pi()/180.0);
            $first_y = abs(sin((90.0-$mapped_point_vip4_first_tracing[0])*pi()/180.0))*sin($mapped_point_vip4_first_tracing[1]*pi()/180.0);
            $second_x = abs(sin((90.0-$mapped_point_vip4_second_tracing[0])*pi()/180.0))*cos($mapped_point_vip4_second_tracing[1]*pi()/180.0);
            $second_y = abs(sin((90.0-$mapped_point_vip4_second_tracing[0])*pi()/180.0))*sin($mapped_point_vip4_second_tracing[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = 90.0 - asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi();
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_vip4_first_tracing[1]) > 400. or abs($mapped_point_vip4_second_tracing[0]) > 400.) {
                $lat_interpol = $mapped_point_vip4_first_tracing[0];
                $long_interpol = $mapped_point_vip4_first_tracing[1];
            }
            $mapped_point_vip4_north_tracing = array();
            $mapped_point_vip4_north_tracing[0] = $lat_interpol;
            $mapped_point_vip4_north_tracing[1] = $long_interpol;
            
            $mapped_point_vipal_first_tracing = map_mag_to_ion_tracing($rj,$loctime,$first_sslong,'vipal','north');
            $mapped_point_vipal_second_tracing = map_mag_to_ion_tracing($rj,$loctime,$second_sslong,'vipal','north');
            $first_x = abs(sin((90.0-$mapped_point_vipal_first_tracing[0])*pi()/180.0))*cos($mapped_point_vipal_first_tracing[1]*pi()/180.0);
            $first_y = abs(sin((90.0-$mapped_point_vipal_first_tracing[0])*pi()/180.0))*sin($mapped_point_vipal_first_tracing[1]*pi()/180.0);
            $second_x = abs(sin((90.0-$mapped_point_vipal_second_tracing[0])*pi()/180.0))*cos($mapped_point_vipal_second_tracing[1]*pi()/180.0);
            $second_y = abs(sin((90.0-$mapped_point_vipal_second_tracing[0])*pi()/180.0))*sin($mapped_point_vipal_second_tracing[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = 90.0 - asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi();
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_vipal_first_tracing[1]) > 400. or abs($mapped_point_vipal_second_tracing[0]) > 400.) {
                $lat_interpol = $mapped_point_vipal_first_tracing[0];
                $long_interpol = $mapped_point_vipal_first_tracing[1];
            }
            $mapped_point_vipal_north_tracing = array();
            $mapped_point_vipal_north_tracing[0] = $lat_interpol;
            $mapped_point_vipal_north_tracing[1] = $long_interpol;
            
            $mapped_point_jrm09_first_tracing = map_mag_to_ion_tracing($rj,$loctime,$first_sslong,'jrm09','north');
            $mapped_point_jrm09_second_tracing = map_mag_to_ion_tracing($rj,$loctime,$second_sslong,'jrm09','north');
            $first_x = abs(sin((90.0-$mapped_point_jrm09_first_tracing[0])*pi()/180.0))*cos($mapped_point_jrm09_first_tracing[1]*pi()/180.0);
            $first_y = abs(sin((90.0-$mapped_point_jrm09_first_tracing[0])*pi()/180.0))*sin($mapped_point_jrm09_first_tracing[1]*pi()/180.0);
            $second_x = abs(sin((90.0-$mapped_point_jrm09_second_tracing[0])*pi()/180.0))*cos($mapped_point_jrm09_second_tracing[1]*pi()/180.0);
            $second_y = abs(sin((90.0-$mapped_point_jrm09_second_tracing[0])*pi()/180.0))*sin($mapped_point_jrm09_second_tracing[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = 90.0 - asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi();
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_jrm09_first_tracing[1]) > 400. or abs($mapped_point_jrm09_second_tracing[0]) > 400.) {
                $lat_interpol = $mapped_point_jrm09_first_tracing[0];
                $long_interpol = $mapped_point_jrm09_first_tracing[1];
            }
            $mapped_point_jrm09_north_tracing = array();
            $mapped_point_jrm09_north_tracing[0] = $lat_interpol;
            $mapped_point_jrm09_north_tracing[1] = $long_interpol;
            
            $mapped_point_kk2009_first_tracing = map_mag_to_ion_tracing($rj,$loctime,$first_sslong,'kk2009','north');
            $mapped_point_kk2009_second_tracing = map_mag_to_ion_tracing($rj,$loctime,$second_sslong,'kk2009','north');
            $first_x = abs(sin((90.0-$mapped_point_kk2009_first_tracing[0])*pi()/180.0))*cos($mapped_point_kk2009_first_tracing[1]*pi()/180.0);
            $first_y = abs(sin((90.0-$mapped_point_kk2009_first_tracing[0])*pi()/180.0))*sin($mapped_point_kk2009_first_tracing[1]*pi()/180.0);
            $second_x = abs(sin((90.0-$mapped_point_kk2009_second_tracing[0])*pi()/180.0))*cos($mapped_point_kk2009_second_tracing[1]*pi()/180.0);
            $second_y = abs(sin((90.0-$mapped_point_kk2009_second_tracing[0])*pi()/180.0))*sin($mapped_point_kk2009_second_tracing[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = 90.0 - asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi();
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_kk2009_first_tracing[1]) > 400. or abs($mapped_point_kk2009_second_tracing[0]) > 400.) {
                $lat_interpol = $mapped_point_kk2009_first_tracing[0];
                $long_interpol = $mapped_point_kk2009_first_tracing[1];
            }
            $mapped_point_kk2009_north_tracing = array();
            $mapped_point_kk2009_north_tracing[0] = $lat_interpol;
            $mapped_point_kk2009_north_tracing[1] = $long_interpol;
            
            $mapped_point_kk2009ext_jrm09int_first_tracing = map_mag_to_ion_tracing($rj,$loctime,$first_sslong,'kk2009ext_jrm09int','north');
            $mapped_point_kk2009ext_jrm09int_second_tracing = map_mag_to_ion_tracing($rj,$loctime,$second_sslong,'kk2009ext_jrm09int','north');
            $first_x = abs(sin((90.0-$mapped_point_kk2009ext_jrm09int_first_tracing[0])*pi()/180.0))*cos($mapped_point_kk2009ext_jrm09int_first_tracing[1]*pi()/180.0);
            $first_y = abs(sin((90.0-$mapped_point_kk2009ext_jrm09int_first_tracing[0])*pi()/180.0))*sin($mapped_point_kk2009ext_jrm09int_first_tracing[1]*pi()/180.0);
            $second_x = abs(sin((90.0-$mapped_point_kk2009ext_jrm09int_second_tracing[0])*pi()/180.0))*cos($mapped_point_kk2009ext_jrm09int_second_tracing[1]*pi()/180.0);
            $second_y = abs(sin((90.0-$mapped_point_kk2009ext_jrm09int_second_tracing[0])*pi()/180.0))*sin($mapped_point_kk2009ext_jrm09int_second_tracing[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = 90.0 - asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi();
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_kk2009ext_jrm09int_first_tracing[1]) > 400. or abs($mapped_point_kk2009ext_jrm09int_second_tracing[0]) > 400.) {
                $lat_interpol = $mapped_point_kk2009ext_jrm09int_first_tracing[0];
                $long_interpol = $mapped_point_kk2009ext_jrm09int_first_tracing[1];
            }
            $mapped_point_kk2009ext_jrm09int_north_tracing = array();
            $mapped_point_kk2009ext_jrm09int_north_tracing[0] = $lat_interpol;
            $mapped_point_kk2009ext_jrm09int_north_tracing[1] = $long_interpol;
            
            
            $mapped_point_vip4_first_tracing = map_mag_to_ion_tracing($rj,$loctime,$first_sslong,'vip4','south');
            $mapped_point_vip4_second_tracing = map_mag_to_ion_tracing($rj,$loctime,$second_sslong,'vip4','south');
            $first_x = abs(sin((90.0-abs($mapped_point_vip4_first_tracing[0]))*pi()/180.0))*cos($mapped_point_vip4_first_tracing[1]*pi()/180.0);
            $first_y = abs(sin((90.0-abs($mapped_point_vip4_first_tracing[0]))*pi()/180.0))*sin($mapped_point_vip4_first_tracing[1]*pi()/180.0);
            $second_x = abs(sin((90.0-abs($mapped_point_vip4_second_tracing[0]))*pi()/180.0))*cos($mapped_point_vip4_second_tracing[1]*pi()/180.0);
            $second_y = abs(sin((90.0-abs($mapped_point_vip4_second_tracing[0]))*pi()/180.0))*sin($mapped_point_vip4_second_tracing[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi() - 90.0;
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_vip4_first_tracing[1]) > 400. or abs($mapped_point_vip4_second_tracing[0]) > 400.) {
                $lat_interpol = $mapped_point_vip4_first_tracing[0];
                $long_interpol = $mapped_point_vip4_first_tracing[1];
            }
            $mapped_point_vip4_south_tracing = array();
            $mapped_point_vip4_south_tracing[0] = $lat_interpol;
            $mapped_point_vip4_south_tracing[1] = $long_interpol;
            
            $mapped_point_vipal_first_tracing = map_mag_to_ion_tracing($rj,$loctime,$first_sslong,'vipal','south');
            $mapped_point_vipal_second_tracing = map_mag_to_ion_tracing($rj,$loctime,$second_sslong,'vipal','south');
            $first_x = abs(sin((90.0-abs($mapped_point_vipal_first_tracing[0]))*pi()/180.0))*cos($mapped_point_vipal_first_tracing[1]*pi()/180.0);
            $first_y = abs(sin((90.0-abs($mapped_point_vipal_first_tracing[0]))*pi()/180.0))*sin($mapped_point_vipal_first_tracing[1]*pi()/180.0);
            $second_x = abs(sin((90.0-abs($mapped_point_vipal_second_tracing[0]))*pi()/180.0))*cos($mapped_point_vipal_second_tracing[1]*pi()/180.0);
            $second_y = abs(sin((90.0-abs($mapped_point_vipal_second_tracing[0]))*pi()/180.0))*sin($mapped_point_vipal_second_tracing[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi() - 90.0;
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_vipal_first_tracing[1]) > 400. or abs($mapped_point_vipal_second_tracing[0]) > 400.) {
                $lat_interpol = $mapped_point_vipal_first_tracing[0];
                $long_interpol = $mapped_point_vipal_first_tracing[1];
            }
            $mapped_point_vipal_south_tracing = array();
            $mapped_point_vipal_south_tracing[0] = $lat_interpol;
            $mapped_point_vipal_south_tracing[1] = $long_interpol;
            
            $mapped_point_jrm09_first_tracing = map_mag_to_ion_tracing($rj,$loctime,$first_sslong,'jrm09','south');
            $mapped_point_jrm09_second_tracing = map_mag_to_ion_tracing($rj,$loctime,$second_sslong,'jrm09','south');
            $first_x = abs(sin((90.0-abs($mapped_point_jrm09_first_tracing[0]))*pi()/180.0))*cos($mapped_point_jrm09_first_tracing[1]*pi()/180.0);
            $first_y = abs(sin((90.0-abs($mapped_point_jrm09_first_tracing[0]))*pi()/180.0))*sin($mapped_point_jrm09_first_tracing[1]*pi()/180.0);
            $second_x = abs(sin((90.0-abs($mapped_point_jrm09_second_tracing[0]))*pi()/180.0))*cos($mapped_point_jrm09_second_tracing[1]*pi()/180.0);
            $second_y = abs(sin((90.0-abs($mapped_point_jrm09_second_tracing[0]))*pi()/180.0))*sin($mapped_point_jrm09_second_tracing[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi() - 90.0;
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_jrm09_first_tracing[1]) > 400. or abs($mapped_point_jrm09_second_tracing[0]) > 400.) {
                $lat_interpol = $mapped_point_jrm09_first_tracing[0];
                $long_interpol = $mapped_point_jrm09_first_tracing[1];
            }
            $mapped_point_jrm09_south_tracing = array();
            $mapped_point_jrm09_south_tracing[0] = $lat_interpol;
            $mapped_point_jrm09_south_tracing[1] = $long_interpol;
            
            $mapped_point_kk2009_first_tracing = map_mag_to_ion_tracing($rj,$loctime,$first_sslong,'kk2009','south');
            $mapped_point_kk2009_second_tracing = map_mag_to_ion_tracing($rj,$loctime,$second_sslong,'kk2009','south');
            $first_x = abs(sin((90.0-abs($mapped_point_kk2009_first_tracing[0]))*pi()/180.0))*cos($mapped_point_kk2009_first_tracing[1]*pi()/180.0);
            $first_y = abs(sin((90.0-abs($mapped_point_kk2009_first_tracing[0]))*pi()/180.0))*sin($mapped_point_kk2009_first_tracing[1]*pi()/180.0);
            $second_x = abs(sin((90.0-abs($mapped_point_kk2009_second_tracing[0]))*pi()/180.0))*cos($mapped_point_kk2009_second_tracing[1]*pi()/180.0);
            $second_y = abs(sin((90.0-abs($mapped_point_kk2009_second_tracing[0]))*pi()/180.0))*sin($mapped_point_kk2009_second_tracing[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi() - 90.0;
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_kk2009_first_tracing[1]) > 400. or abs($mapped_point_kk2009_second_tracing[0]) > 400.) {
                $lat_interpol = $mapped_point_kk2009_first_tracing[0];
                $long_interpol = $mapped_point_kk2009_first_tracing[1];
            }
            $mapped_point_kk2009_south_tracing = array();
            $mapped_point_kk2009_south_tracing[0] = $lat_interpol;
            $mapped_point_kk2009_south_tracing[1] = $long_interpol;
            
            $mapped_point_kk2009ext_jrm09int_first_tracing = map_mag_to_ion_tracing($rj,$loctime,$first_sslong,'kk2009ext_jrm09int','south');
            $mapped_point_kk2009ext_jrm09int_second_tracing = map_mag_to_ion_tracing($rj,$loctime,$second_sslong,'kk2009ext_jrm09int','south');
            $first_x = abs(sin((90.0-abs($mapped_point_kk2009ext_jrm09int_first_tracing[0]))*pi()/180.0))*cos($mapped_point_kk2009ext_jrm09int_first_tracing[1]*pi()/180.0);
            $first_y = abs(sin((90.0-abs($mapped_point_kk2009ext_jrm09int_first_tracing[0]))*pi()/180.0))*sin($mapped_point_kk2009ext_jrm09int_first_tracing[1]*pi()/180.0);
            $second_x = abs(sin((90.0-abs($mapped_point_kk2009ext_jrm09int_second_tracing[0]))*pi()/180.0))*cos($mapped_point_kk2009ext_jrm09int_second_tracing[1]*pi()/180.0);
            $second_y = abs(sin((90.0-abs($mapped_point_kk2009ext_jrm09int_second_tracing[0]))*pi()/180.0))*sin($mapped_point_kk2009ext_jrm09int_second_tracing[1]*pi()/180.0);
            $x_interpol = $first_x + ($second_x-$first_x)*(($sslong)-$sslong_first_interpol)/10.0;
            $y_interpol = $first_y + ($second_y-$first_y)*(($sslong)-$sslong_first_interpol)/10.0;
            $lat_interpol = asin(pow(($x_interpol*$x_interpol + $y_interpol*$y_interpol),0.5))*180.0/pi() - 90.0;
            $long_interpol = fmod((atan2($y_interpol,$x_interpol)*180.0/pi() + 720.0),360.);
            if (abs($mapped_point_kk2009ext_jrm09int_first_tracing[1]) > 400. or abs($mapped_point_kk2009ext_jrm09int_second_tracing[0]) > 400.) {
                $lat_interpol = $mapped_point_kk2009ext_jrm09int_first_tracing[0];
                $long_interpol = $mapped_point_kk2009ext_jrm09int_first_tracing[1];
            }
            $mapped_point_kk2009ext_jrm09int_south_tracing = array();
            $mapped_point_kk2009ext_jrm09int_south_tracing[0] = $lat_interpol;
            $mapped_point_kk2009ext_jrm09int_south_tracing[1] = $long_interpol;

        }
        
        if ($rj > 65.) {
            $mapped_point_gam_tracing[0] = -999.;
            $mapped_point_gam_tracing[1] = -999.;
        }
        if ($rj > 85.) {
            $mapped_point_vip4_north_tracing[0] = -999.;
            $mapped_point_vip4_north_tracing[1] = -999.;
            $mapped_point_vip4_south_tracing[0] = -999.;
            $mapped_point_vip4_south_tracing[1] = -999.;
            $mapped_point_vipal_north_tracing[0] = -999.;
            $mapped_point_vipal_north_tracing[1] = -999.;
            $mapped_point_vipal_south_tracing[0] = -999.;
            $mapped_point_vipal_south_tracing[1] = -999.;
            $mapped_point_jrm09_north_tracing[0] = -999.;
            $mapped_point_jrm09_north_tracing[1] = -999.;
            $mapped_point_jrm09_south_tracing[0] = -999.;
            $mapped_point_jrm09_south_tracing[1] = -999.;
            $mapped_point_kk2009_north_tracing[0] = -999.;
            $mapped_point_kk2009_north_tracing[1] = -999.;
            $mapped_point_kk2009_south_tracing[0] = -999.;
            $mapped_point_kk2009_south_tracing[1] = -999.;
            $mapped_point_kk2009ext_jrm09int_north_tracing[0] = -999.;
            $mapped_point_kk2009ext_jrm09int_north_tracing[1] = -999.;
            $mapped_point_kk2009ext_jrm09int_south_tracing[0] = -999.;
            $mapped_point_kk2009ext_jrm09int_south_tracing[1] = -999.;
        }
        
        
        echo '<b>User Inputs:</b><br>';
        echo 'Mapping requested from the magnetosphere to the ionosphere.';
        echo '<br>';
        echo 'Subsolar longitude was '.$_POST['sslong'];
        echo ' degrees';
        echo '<br>';
        echo 'Input radial distance was ' .$_POST['rj'];
        echo ' Jovian radii';
        echo '<br>';
        echo 'Input local time was ' .$_POST['loctime'];
        echo ' hours';
        echo '<p>';
        $message = "<html>Thank you for using the online mapping tool.";
        $message .= "<p>";
        $message .= "Mapping requested: magnetosphere to ionosphere.<p>";
        $message .= "Inputs:<br>";
        $message .= "Subsolar longitude (degrees) = " .$sslong;
        $message .= "<br>";
        $message .= "Radial distance (Jovian radii) = " .$_POST['rj'];
        $message .= "<br>";
        $message .= "Local time (hours) = " .$_POST['loctime'];
        $message .= "<p>";
        
        echo '<p><b>Results using flux equivalence calculation with VIP4:</b> <br>';
        $message .= "Results using flux equivalence calculation with VIP4:"; 
        $message .= "<br>";
        //				echo 'Radial distance (Jovian radii) = ' .$r_interpol;
        printf('Northern ionospheric latitude (degrees) = %0.3f', $mapped_point_vip4_north[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Northern ionospheric longitude (degrees) = %0.3f', $mapped_point_vip4_north[1]);
        echo '<br>';
        $message .= "Northern ionospheric latitude (degrees) = " .$mapped_point_vip4_north[0];
        $message .= "<br>";
        $message .= "Northern ionospheric longitude (degrees) = " .$mapped_point_vip4_north[1];
        $message .= "<br>";
        printf('Southern ionospheric latitude (degrees) = %0.3f', $mapped_point_vip4_south[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Southern ionospheric longitude (degrees) = %0.3f', $mapped_point_vip4_south[1]);
        echo '<p>';
        $message .= "Southern ionospheric latitude (degrees) = " .$mapped_point_vip4_south[0];
        $message .= "<br>";
        $message .= "Southern ionospheric longitude (degrees) = " .$mapped_point_vip4_south[1];
        $message .= "<p>";
        
        echo '<p><b>Results using flux equivalence calculation with the Grodent anomaly model:</b> <br>';
        $message .= "Results using flux equivalence calculation with the Grodent anomaly model:"; 
        $message .= "<br>";
        //				echo 'Radial distance (Jovian radii) = ' .$r_interpol;
        printf('Northern ionospheric latitude (degrees) = %0.3f', $mapped_point_gam[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Northern ionospheric longitude (degrees) = %0.3f', $mapped_point_gam[1]);
        echo '<p>';
        $message .= "Northern ionospheric latitude (degrees) = " .$mapped_point_gam[0];
        $message .= "<br>";
        $message .= "Northern ionospheric longitude (degrees) = " .$mapped_point_gam[1];
        $message .= "<p>";
        
        echo '<p><b>Results using flux equivalence calculation with VIPAL:</b> <br>';
        $message .= "Results using flux equivalence calculation with VIPAL:"; 
        $message .= "<br>";
        //				echo 'Radial distance (Jovian radii) = ' .$r_interpol;
        printf('Northern ionospheric latitude (degrees) = %0.3f', $mapped_point_vipal_north[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Northern ionospheric longitude (degrees) = %0.3f', $mapped_point_vipal_north[1]);
        echo '<br>';
        $message .= "Northern ionospheric latitude (degrees) = " .$mapped_point_vipal_north[0];
        $message .= "<br>";
        $message .= "Northern ionospheric longitude (degrees) = " .$mapped_point_vipal_north[1];
        $message .= "<br>";
        printf('Southern ionospheric latitude (degrees) = %0.3f', $mapped_point_vipal_south[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Southern ionospheric longitude (degrees) = %0.3f', $mapped_point_vipal_south[1]);
        echo '<p>';
        $message .= "Southern ionospheric latitude (degrees) = " .$mapped_point_vipal_south[0];
        $message .= "<br>";
        $message .= "Southern ionospheric longitude (degrees) = " .$mapped_point_vipal_south[1];
        $message .= "<p>";
        
        echo '<p><b>Results using flux equivalence calculation with JRM09:</b> <br>';
        $message .= "Results using flux equivalence calculation with JRM09:";
        $message .= "<br>";
        //				echo 'Radial distance (Jovian radii) = ' .$r_interpol;
        printf('Northern ionospheric latitude (degrees) = %0.3f', $mapped_point_jrm09_north[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Northern ionospheric longitude (degrees) = %0.3f', $mapped_point_jrm09_north[1]);
        echo '<br>';
        $message .= "Northern ionospheric latitude (degrees) = " .$mapped_point_jrm09_north[0];
        $message .= "<br>";
        $message .= "Northern ionospheric longitude (degrees) = " .$mapped_point_jrm09_north[1];
        $message .= "<br>";
        printf('Southern ionospheric latitude (degrees) = %0.3f', $mapped_point_jrm09_south[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Southern ionospheric longitude (degrees) = %0.3f', $mapped_point_jrm09_south[1]);
        echo '<p>';
        $message .= "Southern ionospheric latitude (degrees) = " .$mapped_point_jrm09_south[0];
        $message .= "<br>";
        $message .= "Southern ionospheric longitude (degrees) = " .$mapped_point_jrm09_south[1];
        $message .= "<p>";
        
        
        
        
        
        
        
        
        echo '<p><b>Results using fieldline tracing with VIP4:</b> <br>';
        $message .= "Results using fieldline tracing with VIP4:";
        $message .= "<br>";
        //				echo 'Radial distance (Jovian radii) = ' .$r_interpol;
        printf('Northern ionospheric latitude (degrees) = %0.3f', $mapped_point_vip4_north_tracing[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Northern ionospheric longitude (degrees) = %0.3f', $mapped_point_vip4_north_tracing[1]);
        echo '<br>';
        $message .= "Northern ionospheric latitude (degrees) = " .$mapped_point_vip4_north_tracing[0];
        $message .= "<br>";
        $message .= "Northern ionospheric longitude (degrees) = " .$mapped_point_vip4_north_tracing[1];
        $message .= "<br>";
        printf('Southern ionospheric latitude (degrees) = %0.3f', $mapped_point_vip4_south_tracing[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Southern ionospheric longitude (degrees) = %0.3f', $mapped_point_vip4_south_tracing[1]);
        echo '<p>';
        $message .= "Southern ionospheric latitude (degrees) = " .$mapped_point_vip4_south_tracing[0];
        $message .= "<br>";
        $message .= "Southern ionospheric longitude (degrees) = " .$mapped_point_vip4_south_tracing[1];
        $message .= "<p>";
        
        echo '<p><b>Results using fieldline tracing with the Grodent anomaly model:</b> <br>';
        $message .= "Results using fieldline tracing with the Grodent anomaly model:";
        $message .= "<br>";
        //				echo 'Radial distance (Jovian radii) = ' .$r_interpol;
        printf('Northern ionospheric latitude (degrees) = %0.3f', $mapped_point_gam_tracing[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Northern ionospheric longitude (degrees) = %0.3f', $mapped_point_gam_tracing[1]);
        echo '<p>';
        $message .= "Northern ionospheric latitude (degrees) = " .$mapped_point_gam_tracing[0];
        $message .= "<br>";
        $message .= "Northern ionospheric longitude (degrees) = " .$mapped_point_gam_tracing[1];
        $message .= "<p>";
        
        echo '<p><b>Results using fieldline tracing with VIPAL:</b> <br>';
        $message .= "Results using fieldline tracing with VIPAL:";
        $message .= "<br>";
        //				echo 'Radial distance (Jovian radii) = ' .$r_interpol;
        printf('Northern ionospheric latitude (degrees) = %0.3f', $mapped_point_vipal_north_tracing[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Northern ionospheric longitude (degrees) = %0.3f', $mapped_point_vipal_north_tracing[1]);
        echo '<br>';
        $message .= "Northern ionospheric latitude (degrees) = " .$mapped_point_vipal_north_tracing[0];
        $message .= "<br>";
        $message .= "Northern ionospheric longitude (degrees) = " .$mapped_point_vipal_north_tracing[1];
        $message .= "<br>";
        printf('Southern ionospheric latitude (degrees) = %0.3f', $mapped_point_vipal_south_tracing[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Southern ionospheric longitude (degrees) = %0.3f', $mapped_point_vipal_south_tracing[1]);
        echo '<p>';
        $message .= "Southern ionospheric latitude (degrees) = " .$mapped_point_vipal_south_tracing[0];
        $message .= "<br>";
        $message .= "Southern ionospheric longitude (degrees) = " .$mapped_point_vipal_south_tracing[1];
        $message .= "<p>";
        
        echo '<p><b>Results using fieldline tracing with JRM09:</b> <br>';
        $message .= "Results using fieldline tracing with JRM09:";
        $message .= "<br>";
        //				echo 'Radial distance (Jovian radii) = ' .$r_interpol;
        printf('Northern ionospheric latitude (degrees) = %0.3f', $mapped_point_jrm09_north_tracing[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Northern ionospheric longitude (degrees) = %0.3f', $mapped_point_jrm09_north_tracing[1]);
        echo '<br>';
        $message .= "Northern ionospheric latitude (degrees) = " .$mapped_point_jrm09_north_tracing[0];
        $message .= "<br>";
        $message .= "Northern ionospheric longitude (degrees) = " .$mapped_point_jrm09_north_tracing[1];
        $message .= "<br>";
        printf('Southern ionospheric latitude (degrees) = %0.3f', $mapped_point_jrm09_south_tracing[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Southern ionospheric longitude (degrees) = %0.3f', $mapped_point_jrm09_south_tracing[1]);
        echo '<p>';
        $message .= "Southern ionospheric latitude (degrees) = " .$mapped_point_jrm09_south_tracing[0];
        $message .= "<br>";
        $message .= "Southern ionospheric longitude (degrees) = " .$mapped_point_jrm09_south_tracing[1];
        $message .= "<p>";
        
        echo '<p><b>Results using fieldline tracing with the Khurana model:</b> <br>';
        $message .= "Results using fieldline tracing with the Khurana model:";
        $message .= "<br>";
        //				echo 'Radial distance (Jovian radii) = ' .$r_interpol;
        printf('Northern ionospheric latitude (degrees) = %0.3f', $mapped_point_kk2009_north_tracing[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Northern ionospheric longitude (degrees) = %0.3f', $mapped_point_kk2009_north_tracing[1]);
        echo '<br>';
        $message .= "Northern ionospheric latitude (degrees) = " .$mapped_point_kk2009_north_tracing[0];
        $message .= "<br>";
        $message .= "Northern ionospheric longitude (degrees) = " .$mapped_point_kk2009_north_tracing[1];
        $message .= "<br>";
        printf('Southern ionospheric latitude (degrees) = %0.3f', $mapped_point_kk2009_south_tracing[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Southern ionospheric longitude (degrees) = %0.3f', $mapped_point_kk2009_south_tracing[1]);
        echo '<p>';
        $message .= "Southern ionospheric latitude (degrees) = " .$mapped_point_kk2009_south_tracing[0];
        $message .= "<br>";
        $message .= "Southern ionospheric longitude (degrees) = " .$mapped_point_kk2009_south_tracing[1];
        $message .= "<p>";

        echo '<p><b>Results using fieldline tracing with the Khurana model (current sheet) and JRM09 (internal field):</b> <br>';
        $message .= "Results using fieldline tracing with the Khurana model (current sheet) and JRM09 (internal field):";
        $message .= "<br>";
        //				echo 'Radial distance (Jovian radii) = ' .$r_interpol;
        printf('Northern ionospheric latitude (degrees) = %0.3f', $mapped_point_kk2009ext_jrm09int_north_tracing[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Northern ionospheric longitude (degrees) = %0.3f', $mapped_point_kk2009ext_jrm09int_north_tracing[1]);
        echo '<br>';
        $message .= "Northern ionospheric latitude (degrees) = " .$mapped_point_kk2009ext_jrm09int_north_tracing[0];
        $message .= "<br>";
        $message .= "Northern ionospheric longitude (degrees) = " .$mapped_point_kk2009ext_jrm09int_north_tracing[1];
        $message .= "<br>";
        printf('Southern ionospheric latitude (degrees) = %0.3f', $mapped_point_kk2009ext_jrm09int_south_tracing[0]);
        echo '<br>';
        //				echo 'Local time (hours) = ' .$ltime_interpol;
        printf('Southern ionospheric longitude (degrees) = %0.3f', $mapped_point_kk2009ext_jrm09int_south_tracing[1]);
        echo '<p>';
        $message .= "Southern ionospheric latitude (degrees) = " .$mapped_point_kk2009ext_jrm09int_south_tracing[0];
        $message .= "<br>";
        $message .= "Southern ionospheric longitude (degrees) = " .$mapped_point_kk2009ext_jrm09int_south_tracing[1];
        $message .= "<p>";

    }
}
// to check
// make sure sslong 0 is input as sslong 360


if ($username_set == 1) {
    $subject = 'Jupiter ionosphere/magnetosphere online mapping tool summary for ' .$_POST['username'];
}
else
{
    $subject = 'Jupiter ionosphere/magnetosphere online mapping tool summary';
}

if ($username_set == 1) {
    $message .= "<p>Mapping requested by " .$_POST['username'];
}
if (isset($_POST['email_contact'])) {
    $message .= "<p>E-mail address provided: " .$_POST['email_contact'];
}
if (isset($_POST['send_updates'])) {
    $message .= "<p>User has requested to receive e-mail updates about the mapping tool.";
    $message .= "<p>";
}
$message .= "<p>Please send additional comments or questions to marissav@ucla.edu";


// Send
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: marissav@ucla.edu <marissav@ucla.edu>' . "\r\n";
$headers .= 'Reply-To: marissav@ucla.edu' . "\r\n";
$headers .= 'X-Mailer: PHP/' . phpversion();

$message .= "<p>";
$message .= "Reminder: The decimal precision provided here is merely computational and does not reflect the model accuracy. ";
$message .= "While the actual error has not been quantified, it is likely on the order of a few Rj in radial distance and ~1 hour in local time.<p>";
    
$message .= "Note that field line tracing results are only valid to ~85 Rj. For the Grodent anomaly model the field line tracing results are only valid to ~65 Rj.<br>";
$message .= "Fieldline tracing results of -999 indicate that the input radial distance is outside the region of model validity.<br>";
$message .= "All field line tracing results use the Connerney et al. (1981, 1998) current sheet except the Khurana model and Khurana_jrm09 (which uses the Khurana current sheet model and JRM09 internal field).<p>";
    
$message .= "When using these results in a presentation or publication, please cite: <br>";
$message .= "Vogt, Marissa F., Margaret G. Kivelson, Krishan K. Khurana, Raymond J. Walker, Bertrand Bonfond, Denis Grodent, and Aikaterini Radioti (2010), Improved mapping of Jupiters auroral features to magnetospheric sources, J. Geophys. Res., 116, A03220, doi:10.1029/2010JA016148.<br>";
$message .= "and<br>";
$message .= "Vogt, Marissa F., Emma J. Bunce, Margaret G. Kivelson, Krishan K. Khurana, Raymond J. Walker, Aikaterini Radioti, Bertrand Bonfond, and Denis Grodent (2015), Magnetosphere-ionosphere mapping at Jupiter: Quantifying the effects of using different internal field models, J. Geophys. Res. Space Physics, doi:10.1002/2014JA020729, in press.<p>";

    mail('marissav@ucla.edu', $subject, $message, $headers);
    if ($_POST['email_contact'] != '' and isset($_POST['send_results']))
    {
        mail($_POST['email_contact'], $subject, $message, $headers);
        echo '<p>These results have also been sent by e-mail to ';
        echo $_POST['email_contact'];
    }
    
    echo '<p>';
    echo 'Please note that the decimal precision provided here is merely computational and does not reflect the model accuracy. ';
    echo 'While the actual error has not been quantified, it is likely on the order of a few Rj in radial distance and ~1 hour in local time.';
    echo '<p>';
    echo 'Note that field line tracing results are only valid to ~85 Rj. For the Grodent anomaly model the field line tracing results are only valid to ~65 Rj.';
    echo '<br>';
    echo 'Fieldline tracing results of -999 indicate that the input radial distance is outside the region of model validity.';
    echo '<br>';
    echo 'All field line tracing results use the Connerney et al. (1981, 1998) current sheet except the Khurana model and Khurana_jrm09 (which uses the Khurana current sheet model and JRM09 internal field).';
    echo '<p>';
    echo 'When using these results in a presentation or publication, please cite: ';
    echo '<br>';
    echo '<a href="http://onlinelibrary.wiley.com/doi/10.1029/2010JA016148/abstract">Vogt, Marissa F., Margaret G. Kivelson, Krishan K. Khurana, Raymond J. Walker, Bertrand Bonfond, Denis Grodent, and Aikaterini Radioti (2011), Improved mapping of Jupiter’s auroral features to magnetospheric sources, <i>J. Geophys. Res.</i>, <i>116</i>, A03220, doi:10.1029/2010JA016148</a><br>';
    echo 'and<br>';
    echo '<a href="http://onlinelibrary.wiley.com/doi/10.1002/2014JA020729/abstract">Vogt, Marissa F., Emma J. Bunce, Margaret G. Kivelson, Krishan K. Khurana, Raymond J. Walker, Aikaterini Radioti, Bertrand Bonfond, and Denis Grodent (2015), Magnetosphere-ionosphere mapping at Jupiter: Quantifying the effects of using different internal field models, <i>J. Geophys. Res. Space Physics</i>, doi:10.1002/2014JA020729, in press</a><p>';
    echo '<p>';
    echo '<p><a href="index.html">Back to mapping form</a>';
    echo '<br>';
    echo '<a href="http://sites.bu.edu/marissavogt/">Back to Marissa Vogts homepage</a>';


} else {
    echo 'Please provide your name and email address to receive mapping results. Mapping results will be automatically sent to the e-mail provided and will also be shown here. A name and email address are now required so that users of this mapping form can receive updates when new features are added or other changes are made (i.e. bug fixes).';
    echo '<p>';
    echo '<p><a href="index.html">Back to mapping form</a>';
    echo '<br>';
    echo '<a href="http://sites.bu.edu/marissavogt/">Back to Marissa Vogts homepage</a>';
}

?>
