<?php // $Id: lib_rating.php,v 1.26 2012/12/06 12:30:25 shtifanov Exp $
/*


update  mdl_monit_rating_listforms set status=3
where id>2473  and rayonid=19

*/



define('NEW_CRITERIA_YEARID', 5);
define('NEW_REPORT_YEARID', 6);


function get_summa_field_for_all_rayon($rid, $datefrom, $shortname, $fldname)    
{
  	global $CFG;
      
    $strsql = "SELECT id FROM {$CFG->prefix}monit_rating_listforms
  	   		   WHERE (rayonid=$rid) and (shortname='$shortname') and (datemodified=$datefrom)";
    $sql = "SELECT id, $fldname FROM {$CFG->prefix}monit_form_{$shortname} WHERE listformid in ($strsql)";
    // echo $sql . '<hr>';        
    $summa = 0;     
    if ($dfs = get_records_sql($sql))	{
        // print_object ($dfs);
        foreach ($dfs as $df)   {
            // $summa += $df->fo_1;
            $summa += $df->{$fldname};
        }
    }    
        
    return $summa;        
}


// Print tabs years with auto generation link to school
function print_tabs_years_rating($link = '', $rid = 0, $sid = 0, $yid = 1, $isprint=true)
{
	$toprow1 = array();
    $schoolids = array();

    if ($yid <=3) $yid = 4;
    
	$uniqueconstcode = 0;
   	if ($rid != 0 && $sid != 0)	{
   		if ($school = get_record_select('monit_school', "rayonid = $rid AND id = $sid AND yearid = $yid", 'id, uniqueconstcode'))		{
			$uniqueconstcode = $school->uniqueconstcode;   			
   		}
   	} 

    if ($years = get_records_select('monit_years', 'id>3', '', 'id, name'))  {
    	foreach ($years as $year)	{
    		$fulllink = $link . "&rid=$rid&sid=$sid&yid=" . $year->id;
	    	if ($uniqueconstcode != 0)	{
				if ($school = get_record_select('monit_school', "uniqueconstcode=$uniqueconstcode AND yearid = {$year->id}", 'id, rayonid'))	{
					$fulllink = $link . "&rid={$school->rayonid}&sid={$school->id}&yid={$year->id}";
                    $schoolids[$year->id] = $school->id;
				}	
	    	}
            
  			$ayears = explode("/", $year->name);
   			$toprow1[] = new tabobject($year->id, $fulllink, get_string('civilyear', 'block_monitoring', $ayears[0]));    			
	    }
  	}
    $tabs1 = array($toprow1);

    //  print_heading(get_string('terms','block_dean'), 'center', 4);
    if ($isprint)   {
	   print_tabs($tabs1, $yid, NULL, NULL);
    }   
    
    return $schoolids;
}


function init_rating_parameters($yid, &$shortname, &$select, &$order, $level = 'school')
{
    /*
    if ($yid < NEW_CRITERIA_YEARID)   {
        $shortname = optional_param('sn', 'rating_1');       // Shortname form: rating_1 | rating_2
    } else {
        if ($level == 'school' || $level == 'region') {
            $shortname = optional_param('sn', 'rating_n');       // Shortname form: rating_n | rating_o .....
        } else if ($level == 'rayon')   {
            $shortname = optional_param('sn', 'rating_r');       
        }    
    } 
    */   
    $order = 'id';
    
    if ($yid >= 9)  {
        $a = explode('_', $shortname);
        $gl = end($a);
        $select = "yearid=$yid AND gradelevel='$gl'";
        // print "$shortname, $select, $order";
    } else {
        
    	switch ($shortname)	{
    		case 'rating_1': $select = "yearid = 4 AND number LIKE '1.%'";
                             $order = 'number';
    		break;
    		case 'rating_2': $select = "yearid = 4 AND number LIKE '2.%'";
                             $order = 'number';
    		break;
    		case 'rating_n': $select = "yearid = 6 AND gradelevel = 1";
    		break;
    		case 'rating_o': $select = "yearid = 6 AND gradelevel = 2";
    		break;
    		case 'rating_s': $select = "yearid = 6 AND gradelevel = 3";
    		break;
    		case 'rating_k': $select = "yearid = 6 AND gradelevel = 4";
    		break;
    		case 'rating_r': $select = "yearid = 6 AND gradelevel = 5";
    		break;
        }
    }
}

function init_region_criteria($yid)
{
	global $REGIONCRITERIA;
	
	$year = get_record('monit_years', 'id', $yid);
	$ayears = explode("/", $year->name);
	$plugin = 'rating'.$ayears[0];
    
	if ($indicators = get_records('config_plugins', 'plugin', $plugin))	{
		foreach ($indicators as $indicator)	{
			$name = trim($indicator->name);
			$parts = explode('#', $indicator->value);
	        $REGIONCRITERIA->{$name} = trim($parts[0]); 
	    }    
	}
}					

// $formula = f_r1_01/f_r1_02*100%
// $indicator = 50~55~59~69~100#0~1~2~3~4
function func_proc($formula, $indicator, &$arr_df, $ordering = 0)	
{
	global $itogmark;
	
	$itogmark = 0;
	
	$operands = explode('/', $formula);
	$o1 = $operands[0];
	$formula2 = $operands[1];
	$operands2 = explode('*', $formula2);
	$o2 = $operands2[0];
	
	/// echo $o1 . '   ' . $o2 . '<br>'; 
	$color = 'red';// get_string('status1color', 'block_monitoring');
	/// $strmark = "<b><font color=\"$color\">-</font></b>";
	$strmark = '-';
	if (!empty($arr_df[$o1]) && !empty($arr_df[$o2]))	{
	    $drob = (double)$arr_df[$o1]/(double)$arr_df[$o2];
		$rez_proc =  $drob*100.0;

        $color = 'green';// get_string('status7color', 'block_monitoring');
                
        if ($indicator == 'null') {
   			$itogmark = round ($drob, 4);
            if ($ordering == 1) {   
                $color = 'red';
                $itogmark *= -1;   
            }
   			$itogproc = '';			
        } else {
            
    		$two = explode ('#', $indicator);
    		$procents = explode('~', $two[0]);
    		$marks = explode('~', $two[1]);
    		// print_r($procents); echo '<hr>';
    		// print_r($marks); echo '<hr>';
    		$itogmark = $itogproc = 0;
    		foreach($procents as $key => $procent)	{
    			if ($rez_proc <= $procent)	{
    				$itogmark = $marks[$key];
    				$itogproc = ' <= ' . $procent; 
    				break;
    			}
    		}
    		
    		if ($rez_proc > 100)	{
    			$itogmark = end($marks);
    			$itogproc = ' > ' . 100 . '%';			
    		}
        }
		$dolja = number_format($rez_proc, 2, ',', '');
		$dolja .= '%';
	
		$strmark = "<b><font color=\"$color\">$itogmark</font></b>";
		$strmark .= "<br><small>($arr_df[$o1]/$arr_df[$o2]=$dolja $itogproc)</small>";		
		
	}
	return 	$strmark;
}

//  $formula  this is for example: f_r1_07
//  $indicator for example: math4#0~1~2
//  func_one#f_r1_19
//  math4#0~1~2
function func_one($formula, $indicator, &$arr_df, $ordering = 0)	
{
	global $REGIONCRITERIA, $itogmark;
	// print_r($arr_df); echo '<hr>';
/*		
	$REGIONCRITERIA->{$name}
	
	^
	|
	|
	_
	
	$math4 = 4.2;
	$russ4 = 3.9;
	$russ9 = 35.27;
	$alg9  = 17.54;
	$sregemath= 47.2;
	$sregeruss= 58.9;
	$srregegephiz= 51.8;
	$srregegehim= 59.3;
	$srregegeinf= 65.1;
	$srregegebiol= 57.4;
	$srregegehist= 53.9;
	$srregegegeog= 55.9;
	$srregegeengl= 54.4;
	$srregegegerm= 32.5;
	$srregegefren= 74.1;
	$srregegeobch= 56.6;
	$srregegelite= 59.7;
*/

	$itogmark = 0;	
	if (empty($arr_df[$formula])) return '-';

	$curr_indicator = $arr_df[$formula]; // example value  $arr_df[f_r1_07] 
	
    if ($indicator == 'null') {
        $itogmark = $curr_indicator;
        $strmark = '';
    } else {    
    	$array_indicator = explode ('#', $indicator);
    	$name = $array_indicator[0];
    	$region_indicator = $REGIONCRITERIA->{$name}; // $$array_indicator[0]; // example value $math4
    
    	// echo '<br>$array_indicator[0]='  . $array_indicator[0];
    	// echo '<br>$$array_indicator[0]=' . $$array_indicator[0];
     
    	$marks = explode('~', $array_indicator[1]);  // example value 0~1~2
    	if ($curr_indicator < $region_indicator)	{
    		$itogmark = $marks[0];
    		$strmark = "<br><small>($curr_indicator < $region_indicator)</small>";		
    	} else if ($curr_indicator == $region_indicator)	{
    		$itogmark = $marks[1];
    		$strmark = "<br><small>($curr_indicator = $region_indicator)</small>";
    	} else {
    		$itogmark = $marks[2];
    		$strmark = "<br><small>($curr_indicator > $region_indicator)</small>";
    	}
    }     
    $color = 'green';// get_string('status7color', 'block_monitoring');
    if ($ordering == 1) { 
        $itogmark *= -1;	
        $color = 'red';// get_string('status7color', 'block_monitoring');
	}
	
	$strmark = "<b><font color=\"$color\">$itogmark</font></b>" . $strmark;

	return 	$strmark;
 
}


// func_proc_dolja#f_r1_13/f_r1_14*100%
// dolya9educ#0~1~2
function func_proc_dolja($formula, $indicator, &$arr_df, $ordering = 0)	
{
	global $REGIONCRITERIA, $itogmark;
	
	$itogmark = 0;
	// print_r($arr_df); echo '<hr>';
	/*
	$dolya9educ = 59;
	$dolya9npo = 22.54;

	$dolya11vuz = 65.36;
	$dolya11npo = 20.82;
	*/
	
	$operands = explode('/', $formula);
	$o1 = $operands[0];
	$formula2 = $operands[1];
	$operands2 = explode('*', $formula2);
	$o2 = $operands2[0];
	
	/// echo $o1 . '   ' . $o2 . '<br>'; 
	$color = 'red';// get_string('status1color', 'block_monitoring');
	// $strmark = "<b><font color=\"$color\">0</font></b>";
	$strmark = '-';
	if (!empty($arr_df[$o1]) && !empty($arr_df[$o2]))	{
		$curr_indicator =  (double)$arr_df[$o1]/(double)$arr_df[$o2]*100.0;
		$array_indicator = explode ('#', $indicator);
		$name = $array_indicator[0];
		$region_indicator = $REGIONCRITERIA->{$name}; // $region_indicator = $$array_indicator[0]; 
		$marks = explode('~', $array_indicator[1]);
		// print_r($procents); echo '<hr>';
		// print_r($marks); echo '<hr>';
		$itogmark = 0;
		if ($curr_indicator < $region_indicator)	{
			$itogmark = $marks[0];
			$dolja = number_format($curr_indicator, 2, ',', '');
			$dolja .= '%';
			$strmark = "<br><small>($arr_df[$o1]/$arr_df[$o2] = $dolja < $region_indicator%)</small>";		
		} else if ($curr_indicator == $region_indicator)	{
			$itogmark = $marks[1];
			$dolja = number_format($curr_indicator, 2, ',', '');
			$dolja .= '%';
			$strmark = "<br><small>($arr_df[$o1]/$arr_df[$o2] = $dolja = $region_indicator%)</small>";
		} else {
			$itogmark = $marks[2];
			$dolja = number_format($curr_indicator, 2, ',', '');
			$dolja .= '%';
			$strmark = "<br><small>($arr_df[$o1]/$arr_df[$o2] = $dolja > $region_indicator%)</small>";
		} 	


		$color = 'green';
		$strmark = "<b><font color=\"$color\">$itogmark</font></b>" . $strmark;		
	}
	return 	$strmark;
}


// func_1_20#0-(f_r1_42)
function func_shtraf($formula, $indicator, &$arr_df, $ordering = 0)	
{
	global $itogmark;
	
	$itogmark = 0;

	$parts = explode('#', $indicator);	
	$field = $parts[0];
	$mark  = $parts[1];
	
	//$strmark = "<br><small>";
		
	if (!empty($arr_df[$field]))	{
		$itogmark -= $arr_df[$field];
	}

	//$strmark .= "</br></small>";	
	if ($itogmark == 0) {
		$color = 'green';// get_string('status1color', 'block_monitoring');
		$itogmark = $mark;
	} else {
		$color = 'red';// get_string('status7color', 'block_monitoring');
	}
	
	$strmark = "<b><font color=\"$color\">$itogmark</font></b>";		
	return 	$strmark;	
}



// func_one_dolja#f_r1_43
// 80~85~90~95~100#0~1~2~3~4
function func_one_dolja($formula, $indicator, &$arr_df, $ordering = 0)	
{
	global $itogmark;
	
	$itogmark = $itogproc = 0;
	
	// $color = 'red';// get_string('status1color', 'block_monitoring');
	// $strmark = "<b><font color=\"$color\">0</font></b>";
	$strmark = '-';
	
	if (empty($arr_df[$formula]))  return $strmark;
	 
	$rez_proc = $arr_df[$formula]; // example value  $arr_df[f_r1_07] 
	$two = explode ('#', $indicator);
	$procents = explode('~', $two[0]);
	$marks = explode('~', $two[1]);

	foreach($procents as $key => $procent)	{
		if ($rez_proc <= $procent)	{
			$itogmark = $marks[$key];
			$itogproc = $procent;
			break;
		}
	}

	$dolja = number_format($rez_proc, 2, ',', '');
	$dolja .= '%';
	
	$color = 'green';// get_string('status7color', 'block_monitoring');
	$strmark = "<b><font color=\"$color\">$itogmark</font></b>";
	$strmark .= "<br><small>($dolja <= $itogproc%)</small>";		
		
	return 	$strmark;
}



// func_items#f_r2_01*1
// indicator empty
function func_items($formula, $indicator, &$arr_df, $ordering = 0)	
{
	global $itogmark;
	
	$itogmark = 0;
	
	// $color = 'red'; 
	// $strmark = "<b><font color=\"$color\">0</font></b>";
	$strmark = '-';

	$operands = explode ('*', $formula);
	$field_name = trim($operands[0]);
	$koeff = trim($operands[1]);
	 
	if (empty($arr_df[$field_name]))  return $strmark;
	
	$dolja = $arr_df[$field_name];
	if ($dolja < 0) $dolja = 0;
	$itogmark = $dolja * $koeff;
	
	$color = 'green';// get_string('status7color', 'block_monitoring');
	$strmark = "<b><font color=\"$color\">$itogmark</font></b>";
	

	$strmark .= "<br><small>($dolja*$koeff)</small>";		
		
	return 	$strmark;
}

/*
town_high_pedagog~vilg_high_pedagog#0~2~4	func_cenz#f_r2_02/f_r2_05*100%
town_base_pedagog~ vilg_base_pedagog#0~1~3	func_cenz#f_r2_03/f_r2_05*100%
town_kval_pedagog~vilg_kval_pedagog#0~1~3	func_cenz#f_r2_04/f_r2_05*100%
town_kurs_pedagog~vilg_kurs_pedagog#0~1~3	func_cenz#f_r2_06/f_r2_07*100%
*/
function func_cenz($formula, $indicator, &$arr_df, $ordering = 0)	
{
	global $REGIONCRITERIA, $itogmark, $TYPESETTLEMENT;
	
	$itogmark = 0;
/*	
	$town_high_pedagog = 91.7; // vischee j,rozovanir v gorode
	$vilg_high_pedagog = 84.4; // vischee j,rozovanir v derevne

	$town_base_pedagog = 90.3; // vischee j,rozovanir v gorode
	$vilg_base_pedagog = 87.2; // vischee j,rozovanir v derevne
	
	$town_kval_pedagog = 38.3; // vischee j,rozovanir v gorode
	$vilg_kval_pedagog = 16.9; // vischee j,rozovanir v derevne

	$town_kurs_pedagog = 90.3; // vischee j,rozovanir v gorode
	$vilg_kurs_pedagog = 86; // vischee j,rozovanir v derevne
*/
	$operands = explode('/', $formula);
	$o1 = $operands[0];
	$formula2 = $operands[1];
	$operands2 = explode('*', $formula2);
	$o2 = $operands2[0];

	$strmark = '-';
	if (!empty($arr_df[$o1]) && !empty($arr_df[$o2]))	{

		$array_indicator = explode ('#', $indicator);
		$region_indicators = explode('~', $array_indicator[0]);
		$name_town = $region_indicators[0];
		$name_vilg = $region_indicators[1];
		/* 
		$region_indicator_town = $$region_indicators[0];  
		$region_indicator_vilg = $$region_indicators[1];
		*/
		$region_indicator_town = $REGIONCRITERIA->{$name_town};  
		$region_indicator_vilg = $REGIONCRITERIA->{$name_vilg};

		$marks = explode('~', $array_indicator[1]);

		if ($TYPESETTLEMENT == 1)  {
			$region_indicator = $region_indicator_vilg;
		} else {
			$region_indicator = $region_indicator_town;			
		}	
	
		$curr_indicator  =  (double)$arr_df[$o1]/(double)$arr_df[$o2]*100.0;

		if ($curr_indicator < $region_indicator)	{
			$itogmark = $marks[0];
			$dolja = number_format($curr_indicator, 2, ',', '');
			$dolja .= '%';
			$strmark = "<br><small>($arr_df[$o1]/$arr_df[$o2] = $dolja < $region_indicator%)</small>";		
		} else if ($curr_indicator == $region_indicator)	{
			$itogmark = $marks[1];
			$dolja = number_format($curr_indicator, 2, ',', '');
			$dolja .= '%';
			$strmark = "<br><small>($arr_df[$o1]/$arr_df[$o2] = $dolja = $region_indicator%)</small>";
		} else {
			$itogmark = $marks[2];
			$dolja = number_format($curr_indicator, 2, ',', '');
			$dolja .= '%';
			$strmark = "<br><small>($arr_df[$o1]/$arr_df[$o2] = $dolja > $region_indicator%)</small>";
		} 	


		$color = 'green';
		$strmark = "<b><font color=\"$color\">$itogmark</font></b>" . $strmark;		
	}	
		
	return 	$strmark;
}


/*
2;1#f_r2_08;f_r2_09							func_summa#f_r2_08*2+f_r2_09*1
3;2;1;0.5#f_r2_10;f_r2_11;f_r2_12;f_r2_13	func_summa#f_r2_10*3+f_r2_11*2+<br>f_r2_12*1+f_r2_13*0.5
4;3;2#f_r2_14;f_r2_15;f_r2_16				func_summa#f_r2_14*4+f_r2_15*3+f_r2_16*2
4;3;1#f_r2_17;f_r2_18;f_r2_19				func_summa#f_r2_17*4+f_r2_18*3+f_r2_19*1
*/
function func_summa($formula, $indicator, &$arr_df, $ordering = 0)	
{
	global $itogmark;
	
	$itogmark = 0;
	
	$parts = explode('#', $indicator);	
	$marks  = explode('~', $parts[0]);
	$fields = explode('~', $parts[1]);

	$strmark = "<br><small>";
		
	foreach ($fields as $key => $fld)	{
		
		if (isset($arr_df[$fld]))	{
			$itogmark += $arr_df[$fld]*$marks[$key];
			$strmark .= "+$arr_df[$fld]*$marks[$key]";
		}
		
	}
	$strmark .= "</br></small>";
		
	if ($itogmark == 0) {
		// $color = 'red';// get_string('status1color', 'block_monitoring');
		// $strmark = "<b><font color=\"$color\">0</font></b>";
		$strmark = '-';
	} else {
		$color = 'green';// get_string('status7color', 'block_monitoring');
		$strmark = "<b><font color=\"$color\">$itogmark</font></b>" . $strmark;		
	}	

	return 	$strmark;
}

// town_avg_pupil~vilg_avg_pupil#0~1~2	func_cenz_one#f_r2_54
// $formula = f_r2_54
// $indicator = town_avg_pupil~vilg_avg_pupil#0~1~2
function func_cenz_one($formula, $indicator, &$arr_df, $ordering = 0)	
{
	global $REGIONCRITERIA, $itogmark, $TYPESETTLEMENT;
	
	$itogmark = 0;
/*	
	$town_avg_pupil = 20.3;
	$vilg_avg_pupil = 10.6;
*/
	
	$strmark = '-';
	if (!empty($arr_df[$formula]))	{

		$array_indicator = explode ('#', $indicator);
		$region_indicators = explode('~', $array_indicator[0]);

		$name_town = $region_indicators[0];
		$name_vilg = $region_indicators[1];
		/* 
		$region_indicator_town = $$region_indicators[0];  
		$region_indicator_vilg = $$region_indicators[1];
		*/
		$region_indicator_town = $REGIONCRITERIA->{$name_town};  
		$region_indicator_vilg = $REGIONCRITERIA->{$name_vilg};

		$marks = explode('~', $array_indicator[1]);

		if ($TYPESETTLEMENT == 1)  {
			$region_indicator = $region_indicator_vilg;
		} else {
			$region_indicator = $region_indicator_town;
		}	
	
		$curr_indicator  =  $arr_df[$formula];

		if ($curr_indicator < $region_indicator)	{
			$itogmark = $marks[0];
			$dolja = number_format($curr_indicator, 2, ',', '');
			$dolja .= '%';
			$strmark = "<br><small>($dolja < $region_indicator%)</small>";		
		} else if ($curr_indicator == $region_indicator)	{
			$itogmark = $marks[1];
			$dolja = number_format($curr_indicator, 2, ',', '');
			$dolja .= '%';
			$strmark = "<br><small>($dolja = $region_indicator%)</small>";
		} else {
			$itogmark = $marks[2];
			$dolja = number_format($curr_indicator, 2, ',', '');
			$dolja .= '%';
			$strmark = "<br><small>($dolja > $region_indicator%)</small>";
		} 	


		$color = 'green';
		$strmark = "<b><font color=\"$color\">$itogmark</font></b>" . $strmark;		
	}	
		
	return 	$strmark;
}


// func_items#fk_28
// indicator empty
// Наличие: коэффициент 1,1 - первый год, 1.2 - второй год; 
//          отсутствие: -0,9 - первый год,  -0,8 - второй год.
function func_koeff($formula, $indicator, &$arr_df, $ordering = 0)	
{
	global $itogmark;
	
	$itogmark = 0;
	
	// $color = 'red'; 
	// $strmark = "<b><font color=\"$color\">0</font></b>";
	$strmark = '-';
  
  	if (empty($arr_df[$formula])) return '-';  
	$koeff = $arr_df[$formula]; // example value  $arr_df[f_r1_07]
	
    /* 
	if ($koeff == 0) $itogmark  = -0.8;
    else if ($koeff == 1) $itogmark  = 1.1;
    else $itogmark  = 1.2;
	*/
    
	if ($koeff > 0) $itogmark  = 1;
    else $itogmark  = 0;

	$color = 'green';// get_string('status7color', 'block_monitoring');
	$strmark = "<b><font color=\"$color\">$itogmark</font></b>";

	$strmark .= "<br><small>($koeff)</small>";		
		
	return 	$strmark;
}


function func_zp($formula, $indicator, &$arr_df, $ordering = 0)	
{
	global $itogmark;
	
	$itogmark = 0;
    
    // echo $formula . '<hr>';
    // print_object($arr_df);

	$operands = explode('/', $formula);
    // print_object($operands);
	$o1 = $operands[0]; // fk_25
	$o2 = (double)$operands[1]; // 12 
	$o3 = $operands[2]; // fk_07    
    

	$strmark = '-';
	if (!empty($arr_df[$o1]) && !empty($arr_df[$o3]))	{
	   $itogmark = (double)round ($arr_df[$o1]/$o2/$arr_df[$o3], 4);

     	$color = 'green';
    	$strmark = "<b><font color=\"$color\">$itogmark</font></b>";
        $strmark .= "<br><small>($arr_df[$o1]/12/$arr_df[$o3] = $itogmark)</small>";
	}
    // echo $strmark . '<br />';
    	
	return 	$strmark;
}


function func_p126($formula, $indicator, &$arr_df, $ordering = 0)	
{
	global $itogmark;
	
	$itogmark = 0;
	
	// $strmark = "<b><font color=\"$color\">0</font></b>";
	$strmark = '-';
	$color = 'green';  
  
	 
	if ($arr_df['fk_28'] == 1) $itogmark  = 1.1;
    else if ($arr_df['fk_28'] >= 2) $itogmark  = 1.2;
    else if ($arr_df['fk_29'] == 1 || $arr_df['fk_29'] >= 2) $itogmark  = 1.0;
    else if ($arr_df['fk_30'] == 1) $itogmark  = -0.9;
    else if ($arr_df['fk_30'] >= 2) $itogmark  = -0.8;
    else { 
        $color = 'red';
        $itogmark  = 0.0;
    }
	

	$strmark = "<b><font color=\"$color\">$itogmark</font></b>";

/*
    echo $itogmark . '<hr>';
    print_object($arr_df);
	echo $strmark;		
*/		
	return 	$strmark;
}

function func_proc2($formula, $indicator, &$arr_df, $ordering = 0)	
{
	global $itogmark;
	
	$itogmark = 0;
    
//    echo $formula . '<hr>';
//    print_object($arr_df);

	$operands = explode('/', $formula);
	$o1 = $operands[0]; // fr_01 || fr_06
    $o2 = $arr_df['fr_02'];
    $o3 = $arr_df['fr_03'];
    $sub = $o2 - $o3; 

	$strmark = '-';
	if (!empty($arr_df[$o1]) && $sub != 0)	{
        $itogmark = round($arr_df[$o1]/$sub, 4);
		$rez_proc =  $itogmark*100.0;
        
        if ($ordering == 1)  {
            $color = 'red';
            $itogmark *= -1;    
        } else {
            $color = 'green';
        }
                
		$dolja = number_format($rez_proc, 2, ',', '');
		$dolja .= '%';
	
		$strmark = "<b><font color=\"$color\">$itogmark</font></b>";
		$strmark .= "<br><small>($arr_df[$o1]/($o2 - $o3)=$dolja)</small>";		

	}
    	
	return 	$strmark;
}




function func_div($formula, $indicator, &$arr_df, $ordering = 0)	
{
	global $itogmark;
	
	$itogmark = 0;
    
    // echo $formula . '<hr>';
    // print_object($arr_df);

	$operands = explode('/', $formula);
	$o1 = $operands[0]; // fk_25
	$o2 = $operands[1]; // fk_07    

	$strmark = '-';
	if (!empty($arr_df[$o1]) && !empty($arr_df[$o2]))	{
	   $itogmark = round ($arr_df[$o1]/$arr_df[$o2], 4);

        if ($ordering == 1)  {
            $color = 'red';
            $itogmark *= -1;
        } else {
            $color = 'green';
        }

    	$strmark = "<b><font color=\"$color\">$itogmark</font></b>";
        $strmark .= "<br><small>($arr_df[$o1]/$arr_df[$o2] = $itogmark)</small>";
	}
    	
	return 	$strmark;
}


function func_p140($formula, $indicator, &$arr_df, $ordering = 0)	
{
	global $itogmark;
	
    $color = 'red';
    $itogmark = $arr_df['fr_20'];
    /*
	$itogmark = 0;
	
	// $strmark = "<b><font color=\"$color\">0</font></b>";
	$strmark = '-';
	$color = 'green';  
    
    if ($arr_df['fr_20'] > 0) $itogmark  = 'есть';
    else { 
        $itogmark  = 'нет';
        $color = 'red';
    }
    */
  
    /*	 
	if ($arr_df['fr_20'] == 1) $itogmark  = 1.1;
    else if ($arr_df['fr_20'] >= 2) $itogmark  = 1.2;
    else if ($arr_df['fr_23'] == 1) $itogmark  = -0.8;
    else if ($arr_df['fr_23'] >= 2) $itogmark  = -0.9;
    else { 
        $color = 'red';
        $itogmark  = 0;
    }
    */	

	$strmark = "<b><font color=\"$color\">$itogmark</font></b>";

/*
    echo $itogmark . '<hr>';
    print_object($arr_df);
	echo $strmark;		
*/		
	return 	$strmark;
}


function func_p136($o1, &$arr_df)	
{
	global $itogmark;
	
	$itogmark = 0;
	
	// $strmark = "<b><font color=\"$color\">0</font></b>";
	$strmark = '-';
	$color = 'green';  
    
    $o0 =  $arr_df['fr_16'];    
	$strmark = '-';
	if (!empty($arr_df['fr_16']) && $o1 > 0)	{
	    $itogmark = round ($o0/$o1, 4);
		$rez_proc =  $itogmark*100.0;

        $color = 'green';// get_string('status7color', 'block_monitoring');
                
		$dolja = number_format($rez_proc, 2, ',', '');
		$dolja .= '%';
	
		$strmark = "<b><font color=\"$color\">$itogmark</font></b>";
		$strmark .= "<br><small>($o0/$o1 = $dolja)</small>";		

   } else {
		 $strmark .= "<br><small>($o0/$o1 = деление на ноль)</small>";
   }
	 
	return 	$strmark;
}


function func_p137($o1, &$arr_df)	
{
	global $itogmark;
	
	$itogmark = 0;
	
	// $strmark = "<b><font color=\"$color\">0</font></b>";
	$strmark = '-';
	$color = 'green';  
    
    $o0 =  $arr_df['fr_17'];    
	if (!empty($arr_df['fr_17']) && $o1 > 0)	{
	    $itogmark = (double)round ($o0/$o1, 4);
		$rez_proc =  $itogmark*100.0;

        $color = 'green';// get_string('status7color', 'block_monitoring');
                
		$dolja = number_format($rez_proc, 2, ',', '');
		$dolja .= '%';
	
		$strmark = "<b><font color=\"$color\">$itogmark</font></b>";
		$strmark .= "<br><small>($o0/$o1 = $dolja)</small>";		
   } else {
		 $strmark .= "<br><small>($o0/$o1 = деление на ноль)</small>";
   }
	 
	return 	$strmark;
}


function  update_rating_total($yid, $rid, $sid, $shortname, $totalmark, $exclude='')
{  
    global $CFG;
    
    if ($exclude == '') {
        $table = 'monit_rating_total';
    } else {
        $table = 'monit_rating_total_ex';
    }
    
	if ($markschooltotal = get_record_sql("SELECT id FROM {$CFG->prefix}{$table}
	 								       WHERE schoolid=$sid and yearid=$yid")) {
	 	set_field($table, $shortname, $totalmark, 'id', $markschooltotal->id);							  	
    } else {
        $markschooltotal = new stdClass();
   		$markschooltotal->yearid = $yid;
   		$markschooltotal->rayonid = $rid;
        $markschooltotal->schoolid = $sid;
		$markschooltotal->{$shortname} = $totalmark;
		if (!insert_record($table, $markschooltotal))	{
			error('Not insert rating total.', "listforms.php?rid=$rid&amp;yid=$yid&amp;sid=$sid");
		}
    }      
}


function calculating_rating_school($yid, $rid, $sid, $shortname, $arr_df, $criterias)
{
    global $db, $CFG, $itogmark;
    
	$totalmark = 0;    
	foreach($criterias as $criteria)	{
		$itogmark = 0;
		if ($criteria->formula == 'null')	continue;
		$operands = explode('#', $criteria->formula);
		$o1 = trim($operands[0]);
		$o2 = trim($operands[1]);
       	if (function_exists($o1))   {
			if (!empty($arr_df))	{
			    if ($o1 == 'func_p136') {
			        $fo_1 = get_summa_field_for_all_rayon($rid, $datefrom, 'rating_o', 'fo_1');
                    $fs_9 = get_summa_field_for_all_rayon($rid, $datefrom, 'rating_s', 'fs_9');
               		$strmark = func_p136($fo_1+$fs_9, $arr_df);
			    } else if ($o1 == 'func_p137') {
                    $fs_9 = get_summa_field_for_all_rayon($rid, $datefrom, 'rating_s', 'fs_9');
               		$strmark = func_p137($fs_9, $arr_df);
                } else if (function_exists($o1)) {
               		$namefunc = $o1;
               		$strmark = $namefunc($o2, $criteria->indicator, $arr_df, $criteria->ordering);
				}
			} else {
					$strmark = '-';
			}	
            /*
            if ($criteria->ordering == 1)   {
                $itogmark *= -1;    
            }
            */
            
       		$totalmark += $itogmark;
            // echo "$totalsum += $itogmark;<br>" . $strmark . '<br>';
            // echo "$totalsum<hr>";
            // if ($criteria->id == 411) {
            //echo $namefunc . ' = ' . $criteria->id . ' = ' . $itogmark . '<hr>';
            // }     
			if ($markschool = get_record_sql("SELECT id, mark 
											  FROM {$CFG->prefix}monit_rating_school
			 								  WHERE yearid=$yid AND schoolid=$sid AND criteriaid=$criteria->id")) {
			 	set_field('monit_rating_school', 'mark', $itogmark, 'id', $markschool->id);							  	
		   } else {
		        $markschool = new stdClass();
		   		$markschool->yearid = $yid;
		   		$markschool->rayonid = $rid;
		        $markschool->schoolid = $sid;
				$markschool->ratingcategoryid = 1;
				$markschool->criteriaid = $criteria->id;
				$markschool->mark = $itogmark;
				$markschool->rationum = 0;
				if (!insert_record('monit_rating_school', $markschool))	{
					error('Not insert rating school.', "listforms.php?rid=$rid&amp;yid=$yid&amp;sid=$sid");
				}
		   }      
		}
	} // foreach criterias	   
    return 	$totalmark;
}

function func_proc_NOCALC($formula, $indicator, &$arr_df, $ordering = 0)	
{
	global $itogmark;
	
	$itogmark = 0;
    
    return '-';
}    


function correct_dynamic_value_in_criteriaP115($yid, $uniqueconstcode, $dynamic_value, $criteriaid=517)
{

    $prevyid = $yid - 1;
    $curryid = $yid;
    
    $prevsid = get_field_select('monit_school', 'id', "uniqueconstcode=$uniqueconstcode AND yearid=$prevyid");
    $currsid = get_field_select('monit_school', 'id', "uniqueconstcode=$uniqueconstcode AND yearid=$curryid");
    
    if ($prevmark = get_field_select('monit_rating_school', 'mark', "schoolid=$prevsid and criteriaid=$criteriaid")) {
        if ($currmark = get_field_select('monit_rating_school', 'mark', "schoolid=$currsid and criteriaid=$criteriaid")) {
            $deltamark = $prevmark - $currmark;
            $dynamic_value += 2*$deltamark;  
        }    
    }
        
    return $dynamic_value;
}


function get_listnameforms($yid, $level='school')
{
    global $CFG;
    
    $sql = "SELECT  id, shortname 
            FROM mdl_monit_razdel
            WHERE formid=50 and yearid like '%$yid%' and level='$level'
            ORDER BY id"; // group_concat(shortname order by id) as rkps 
    if (!$nameforms = get_records_sql_menu($sql))   {
        $nameforms = array();
        // $nameforms[] = 'rating_r';
    }
    return $nameforms; 
} 


// Display list rating level as popup_form
function listbox_rating_level($scriptname, &$shortname, $yid, $is_all = false, $level = 'school')
{
	global $CFG;

  	$levelmenu = array();
  	
	if ($is_all && $level == 'school')	{
		$levelmenu['rating_0'] = get_string('selectasummary', 'block_monitoring').' ...'; 
	} 
	
    $rkps = get_listnameforms($yid, $level);
     
  	foreach ($rkps as $rkp)	{
  		$razdel = get_record_select ('monit_razdel', "shortname = '$rkp'", 'id, name');
  		// $levelmenu[$rkp] = get_string('name_'.$rkp, 'block_monitoring');
        if (empty($shortname)) $shortname = $rkp; 
		$levelmenu[$rkp] = $razdel->name;  	
  	}

	if ($is_all && $level == 'school')	{
		$levelmenu['rating_9'] = get_string('summaryallcriteria', 'block_monitoring');  	 
	} 
  	
  	if ($is_all && $level == 'school') {
  	  	echo '<tr><td>'.get_string('rgroupcriteria', 'block_monitoring').':</td><td>';  		
  	} else {
  		echo '<div align=center>';
  	}	
    popup_form($scriptname, $levelmenu, 'switchlevel', $shortname, '', '', '', false);
  	if ($is_all && $level == 'school') {
  		echo '</td></tr>';  		
  	} else {	
  		echo '</div>';
  	}	

	return 1;
}



// Display list rating level as popup_form
function listbox_rating_criteria($scriptname, $shortname, $select, $criteriaid, $order = 'id', $maxsymbol = MAX_SYMBOLS_LISTBOX)
{
	global $CFG, $yid;

	get_name_otchet_year ($yid, $a, $b);
	
	$criteriamenu = array();
 	$criteriamenu[0] = get_string('selectacriteria','block_monitoring').' ...';

	$strsql = "SELECT id, number, name FROM {$CFG->prefix}monit_rating_criteria
   			   WHERE $select
 		   	   ORDER BY $order";
	if ($criterias = get_records_sql($strsql)) 	{			   
   		foreach($criterias as $criteria)	{
   			$criteriamenu[$criteria->id] = $criteria->number . '. ';
   			eval("\$criterianame = \"$criteria->name\";");
			if (mb_strlen($criterianame, 'UTF-8') > $maxsymbol)	{
				$criteriamenu[$criteria->id] .= mb_substr($criterianame, 0,  $maxsymbol, 'UTF-8') . ' ...'; 
			}  else {
	  			$criteriamenu[$criteria->id] .= $criterianame;	
			}
	  	}
	}  	
  	
  	echo '<div align=center>';
    popup_form($scriptname, $criteriamenu, 'switchcrit', $criteriaid, '', '', '', false);
  	echo '</div>';

  return 1;
}
