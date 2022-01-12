<?php // $Id: lib.php,v 1.65 2013/02/25 06:17:19 shtifanov Exp $

require_once('lib_auth.php');

define("MAX_SYMBOLS_LISTBOX", 120);
	
/// Check for timed out sessions
    if (!empty($SESSION->has_timed_out)) {
        $session_has_timed_out = true;
        $SESSION->has_timed_out = false;
    } else {
        $session_has_timed_out = false;
    }

    if ($session_has_timed_out) {
        $errormsg = get_string('sessionerroruser', 'error');
        echo '<div class="loginerrors">';
        formerr($errormsg);
        echo '</div>';
    }

// Print tabs months
function print_tabs_typeforms($levelmonit, $currenttab, $nm, $yid, $rid=0, $sid=0 )
{
   global $CFG;

   if (empty($levelmonit) || empty($currenttab)) return false;

	switch ($levelmonit)	{
		case 'region': $link = 'region/region.php';
		break;
		case 'rayon':  $link = 'rayon/listrayonforms.php';
		break;
		case 'school': $link = 'school/listforms.php';
		break;
    }
   $toprow = array();
/*   
   if ($levelmonit == 'region')	{
	   $toprow[] = new tabobject('regionrating', $CFG->wwwroot."/blocks/monitoring/region/regionrating.php?yid=$yid",
 	               get_string('regionrating', 'block_monitoring'));
   }	 	               
*/

   $toprow[] = new tabobject('monthreport', $CFG->wwwroot."/blocks/monitoring/".$link."?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid",
                get_string('monthreport', 'block_monitoring'));
   if ($levelmonit != 'school')	{                
   		$toprow[] = new tabobject('yeareport', $CFG->wwwroot."/blocks/monitoring/bkp/bkpmain.php?level=$levelmonit&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;nm=1",
        				          get_string('yeareport', 'block_monitoring'));
   }     				          
   if ($levelmonit == 'region')	{
   	/*
	   $toprow[] = new tabobject('financereport', $CFG->wwwroot."/blocks/monitoring/finance/financereport.php?level=$levelmonit&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;nm=$nm",
 	               get_string('financereport', 'block_monitoring'));
    */ 	               
	   $toprow[] = new tabobject('regiongraphicks', $CFG->wwwroot."/blocks/monitoring/region/regiongraphicks.php?level=$levelmonit&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;nm=$nm",
 	               get_string('regiongraphicks', 'block_monitoring'));

		if ($context = get_record_select('context', "contextlevel =" . CONTEXT_REGION . " AND instanceid = 1", 'id')) {
		   $toprow[] = new tabobject('assignrole', $CFG->wwwroot."/blocks/mou_school/roles/assign.php?contextid={$context->id}",
 	            		  get_string('assignroles','role'));
 	    }        		  
            
   }
   $tabs = array($toprow);
   print_tabs($tabs, $currenttab, NULL, NULL);
}


// Find input error in forms
function find_form_errors(&$rec, &$err, $tablename)
{
	global $CFG, $db;

    if ($metacolumns = $db->MetaColumns($CFG->prefix . $tablename)) {
   	 	 // print_r($metacolumns);
         $a = array();
         foreach($metacolumns as $metacolumn) {
            $a[$metacolumn->name] = new stdClass();
         	$a[$metacolumn->name]->type = $metacolumn->type;
        }
    }
    foreach($rec as $key => $value) {
   		// print $key. ':' . $t.' <=> '. $a[$key]->type . '<br>';
	    if (!empty($value))	{
      		// $sym = substr($key, 0, 1);
      		if (isset($a[$key]))   {
//		    if ($sym == 'f' && $key != 'fid')   {
		        if ($a[$key]->type == 'int' || $a[$key]->type == 'double' || $a[$key]->type == 'float unsigned') {
            		$t = is_numeric($value);
            		if (!$t) {
   			       		$err[$key]=0;
   			       	}
   			    }
   			}
	    }
    }

    return count($err);
}


function is_date($strdate, $format='ru')
{
   if (empty($strdate)) return false;

   $rez = false;
   if ($format == 'ru')	{
	   if (!strpos($strdate, '.')) return false;
	   $strdate .= '..';
	   $day = $month = $year = 0;
	   list($day, $month, $year) = explode(".", $strdate);
	   $rez = checkdate($month, $day, $year);
   } else if ($format == 'en')	{
	   if (!strpos($strdate, '-')) return false;
	   $strdate .= '--';
	   $day = $month = $year = 0;
	   list($year, $month, $day) = explode("-", $strdate);
	   $rez = checkdate($month, $day, $year);
   }
   return $rez;
}

function convert_date($strdate, $from='ru', $to='en')
{
   if ($from=='ru' && $to=='en')  {
   	   if (!is_date($strdate, 'ru')) {
   	   	  $newfdate = $strdate;
   	   } else {
		   list($day, $month, $year) = explode(".", $strdate);
		   $newfdate = $year.'-'.$month.'-'.$day;
	   	   if (!is_date($newfdate, 'en')) {
 	  	   	  $newfdate = $strdate;
  	 	   }
	   }
   } else if ($from=='en' && $to=='ru')  {
   	   if (!is_date($strdate, 'en')) {
   	   	  $newfdate = $strdate;
   	   } else {
		  list($year, $month, $day) = explode("-", $strdate);
	 	  $newfdate = $day.'.'.$month.'.'.$year;
	   	   if (!is_date($newfdate, 'ru')) {
 	  	   	  $newfdate = $strdate;
  	 	   }
	   }
   }
   return $newfdate;
}


function get_rus_format_date($d, $format='short')
{
 $arrdate = usergetdate($d);

 if (strlen($arrdate['mday']) == 1) $arrdate['mday'] = '0' . $arrdate['mday'];
 if (strlen($arrdate['mon']) == 1)  $arrdate['mon'] = '0' . $arrdate['mon'];

 $str = $arrdate['mday'];
 if ($format == 'short')	{
	$str .= '.' . $arrdate['mon'] .  '.';
 } else if ($format == 'full') {
	 $str .= ' ' . get_string('lm_'.$arrdate['mon'], 'block_monitoring') . ' ';
 }
 $str .= $arrdate['year']. ' г.';
 return $str;
}


// Create date with month and date
function get_timestamp_from_date($d, $m, $y)
{
    $t = make_timestamp($y, $m, $d, 12);
    return $t;
}


// Create date with month and date
/*
function get_date_from_month_year($nm)
{
	$year = date("Y"); // get from config
    $dateret = make_timestamp($year, $nm, 1, 12);
    return $dateret;
}
*/


function get_date_from_month_year($nm, $nyear)
{
   	$year = date("Y");
   	if ($eduyear = get_record_select('monit_years', "id = $nyear", 'id, name'))  {
   		$ayears = explode("/", $eduyear->name);
   		if ($nm >= 9 && $nm <= 12) $year = $ayears[0];
   		else if ($nm >= 1 && $nm <= 8)  $year = $ayears[1];
   	}
    $datefrom = make_timestamp($year, $nm, 1, 12);
    return $datefrom;
}


function get_current_edu_year_id()
{
	$cey = current_edu_year();
	if ($year = get_record_select('monit_years', "name = '$cey'", 'id'))	{
  		return $year->id;
	} else {
  		return 9;
	}
}


function current_edu_year()
{
    $year = date("Y");
    $m = date("n");
    if(($m >= 1) && ($m <= 5)) {  /// !!!!!!!!
		$y = $year-1;
    } else {
		$y = $year;
		$year = $year+1;
    }

	$year = "$y/$year";
	return $year;
}


// Translit number of field table
function translitfield($strname)
{
  $newss = '';
  if ($strname[0] == 'f')   {
     if ($strname[1] == '-')   { $bkp=0; }
     else  { $bkp=1; }
  	 $len = strlen($strname);
  	 for ($i=$bkp; $i<$len; $i++)	{
  	 	switch ($strname[$i])	{
  	 	       case '^': $newss .= ' - '; break;
			   case '-': $newss .= '.'; break;
               case '_': $newss .= '.'; break;
               case 'm': $newss .= 'м'; break;
               case 'r': $newss .= 'р'; break;
               case 'g': $newss .= 'г'; break;
               case 'u': $newss .= 'у'; break;
               case 'd': $newss .= 'д'; break;
               case 'f': $newss .= 'ф'; break;
               case 'z': $newss .= 'з'; break;
               case 'p': $newss .= 'п'; break;
               case 'c': $newss .= 'с'; break;
               case 'n': $newss .= 'н'; break;
               case 'o': $newss .= 'о'; break;
               case 's': $newss .= 'с'; break;
			   default:  $newss .= $strname[$i]; break;
  	 	}
  	 }
  }

 return $newss;
}

// Print tabs years
function print_tabs_years($nyear = 1, $link = '', $civil_year = false)
{
	$toprow1 = array();

    if ($years = get_records_select('monit_years', '', '', 'id, name'))  {
    	foreach ($years as $year)	{
    		if ($civil_year)	{
    			$ayears = explode("/", $year->name);
    			$toprow1[] = new tabobject($year->id, $link.$year->id, get_string('civilyear', 'block_monitoring', $ayears[0]));    			
    		} else  {
    			$toprow1[] = new tabobject($year->id, $link.$year->id, get_string('uchyear', 'block_monitoring', $year->name));
    		}	
	    }
  	}
    $tabs1 = array($toprow1);

   //  print_heading(get_string('terms','block_dean'), 'center', 4);
	print_tabs($tabs1, $nyear, NULL, NULL);
}


// Print tabs years with auto generation link to school
function print_tabs_years_link($link = '', $rid = 0, $sid = 0, $yid = 1, $civil_year = false)
{
	$toprow1 = array();

	$uniqueconstcode = 0;
   	if ($rid != 0 && $sid != 0)	{
   		if ($school = get_record_select('monit_school', "rayonid = $rid AND id = $sid AND yearid = $yid", 'id, uniqueconstcode'))		{
			$uniqueconstcode = $school->uniqueconstcode;   			
   		}
   	} 

    if ($years = get_records_select('monit_years', '', '', 'id, name'))  {
    	foreach ($years as $year)	{
    		$fulllink = $link . "&amp;rid=$rid&amp;sid=$sid&amp;yid=" . $year->id;
	    	if ($uniqueconstcode != 0)	{
				if ($school = get_record_select('monit_school', "uniqueconstcode=$uniqueconstcode AND yearid = {$year->id}", 'id, rayonid'))	{
					$fulllink = $link . "&amp;rid={$school->rayonid}&amp;sid={$school->id}&amp;yid={$year->id}";
				}	
	    	}
            
    		if ($civil_year)	{
    			$ayears = explode("/", $year->name);
    			$toprow1[] = new tabobject($year->id, $fulllink, get_string('civilyear', 'block_monitoring', $ayears[0]));    			
    		} else  {
    			$toprow1[] = new tabobject($year->id, $fulllink, get_string('uchyear', 'block_monitoring', $year->name));
    		}	
            
 	        // $toprow1[] = new tabobject($year->id, $fulllink, get_string('uchyear', 'block_monitoring', $year->name));
	    }
  	}
    $tabs1 = array($toprow1);

   //  print_heading(get_string('terms','block_dean'), 'center', 4);
	print_tabs($tabs1, $yid, NULL, NULL);
}


// Print tabs months
function print_tabs_all_months(&$nmonth, $link = '', $isinactive=true)
{
    if ($isinactive)    {
        $INACTIVE_MONTH = '1,2,3,4,5,7,8,10,11';
        $inactive = explode(',', $INACTIVE_MONTH);
        if ($nmonth >= 1 && $nmonth<=5) {
            $nmonth = 12;
        } else if ($nmonth >= 7 && $nmonth <= 8) {
            $nmonth = 6;
        } else if ($nmonth >= 10 && $nmonth <= 11) {
            $nmonth = 9;
        }
       // print_object($inactive);
    } else {
        $inactive = NULL;
    }
    
	$toprow4 = array();

    for ($i=9; $i<=12; $i++)   {
       $stri = get_string('nm_'.$i, 'block_monitoring');
       if ($i == $nmonth) {
          $stri = "<b>$stri</b>";
       } 
       $toprow4[] = new tabobject($i, $link.$i, $stri);
  	}

    for ($i=1; $i<=8; $i++)   {
       $stri = get_string('nm_'.$i, 'block_monitoring');
       if ($i == $nmonth) {
          $stri = "<b>$stri</b>";
       } 
       $toprow4[] = new tabobject($i, $link.$i, $stri);
  	}
    $tabs4 = array($toprow4);

   //  print_heading(get_string('terms','block_monitoring'), 'center', 4);
	print_tabs($tabs4, $nmonth, $inactive, NULL);
}

// Print tabs months
function print_tabs_months($nquarter = 1, $nmonth = 1, $link = '')
{
	// formula -> 3-m/q
 	if ((3-$nmonth/$nquarter) < 0) 	{
 		error (get_string('erroinquarteormonth','block_monitoring'));
 	}

    $endq = $nquarter*3;

    for ($i=$endq-2; $i<=$endq; $i++)   {
       $toprow4[] = new tabobject($i, $link.$i, get_string('nm_'.$i, 'block_monitoring'));
  	}
    $tabs4 = array($toprow4);

   //  print_heading(get_string('terms','block_monitoring'), 'center', 4);
	print_tabs($tabs4, $nmonth, NULL, NULL);
}

// Print tabs quarters
function print_tabs_quarters($nquarter = 1, $link = '')
{
    for ($i=1; $i<=4; $i++)   {
       $toprow3[] = new tabobject($i, $link.$i, get_string('quarter'.$i, 'block_monitoring'));
  	}
    $tabs3 = array($toprow3);

   //  print_heading(get_string('terms','block_dean'), 'center', 4);
	print_tabs($tabs3, $nquarter, NULL, NULL);
}

// Display list rayons as popup_form
function listbox_rayons($scriptname, $rid, $isqueue = false)
{
  global $CFG;

  $rayonmenu = array();
  $rayonmenu[0] = get_string('selectarayon', 'block_monitoring').'...';

  if ($isqueue) {
        $strsql = "SELECT id, name FROM {$CFG->prefix}monit_rayon WHERE isqueue=1 ORDER BY number";    
  }  else {
        $strsql = "SELECT id, name FROM {$CFG->prefix}monit_rayon ORDER BY number";     
  } 
  if($allrayons = get_records_sql($strsql))   {
 	 foreach ($allrayons as $rayon) 	{
      	$rayonmenu[$rayon->id] = $rayon->name;
  	 }
  }

  echo '<tr> <td>'.get_string('rayon', 'block_monitoring').': </td><td>';
  popup_form($scriptname, $rayonmenu, 'switchrayon', $rid, '', '', '', false);
  echo '</td></tr>';
  return 1;
}

// Display list schools as popup_form
function listbox_schools($scriptname, $rid, $sid, $yid)
{
  global $CFG;

  $schoolmenu = array();
  $schoolmenu[0] = get_string('selectaschool','block_monitoring').' ...';

  if ($rid != 0)  {
    if ($arr_schools =  get_records_sql("SELECT id, name  FROM {$CFG->prefix}monit_school
					     				WHERE rayonid = $rid AND isclosing=0 AND yearid=$yid
					     				ORDER BY number"))	{
  		foreach ($arr_schools as $school) {
			$len = strlen ($school->name);
			if ($len > 200)  {
				// $school->name = substr($school->name, 0, 200) . ' ...';
				$school->name = substr($school->name,0,strrpos(substr($school->name,0, 210),' ')) . ' ...';
			}
			$schoolmenu[$school->id] =$school->name;
		}
	}
  }

  echo '<tr><td>'.get_string('school', 'block_monitoring').':</td><td>';
  popup_form($scriptname, $schoolmenu, 'switchschool', $sid, '', '', '', false);
  echo '</td></tr>';
  return 1;
}

// Display list colleges as popup_form
function listbox_colleges($scriptname, $rid, $sid, $yid)
{
  global $CFG;

  $collegemenu = array();
  $collegemenu[0] = get_string('selectacollege','block_monitoring').' ...';

  if ($rid != 0)  {
    if ($arr_colleges =  get_records_sql("SELECT id, name  FROM {$CFG->prefix}monit_college
					     				WHERE rayonid = $rid AND isclosing=0 AND yearid=$yid
					     				ORDER BY number"))	{
  		foreach ($arr_colleges as $college) {
			$len = strlen ($college->name);
			if ($len > 200)  {
				// $college->name = substr($college->name, 0, 200) . ' ...';
				$college->name = substr($college->name,0,strrpos(substr($college->name,0, 210),' ')) . ' ...';
			}
			$collegemenu[$college->id] =$college->name;
		}
	}
  }

  echo '<tr><td>'.get_string('college', 'block_monitoring').':</td><td>';
  popup_form($scriptname, $collegemenu, 'switchcollege', $sid, '', '', '', false);
  echo '</td></tr>';
  return 1;
}


// Display list colleges as popup_form
function listbox_udods($scriptname, $rid, $did, $yid)
{
  global $CFG;

  $udodmenu = array();
  $udodmenu[0] = get_string('selectaudod','block_monitoring').' ...';

  if ($rid != 0)  {
    if ($arr_udods =  get_records_sql("SELECT id, name  FROM {$CFG->prefix}monit_udod
					     				WHERE rayonid = $rid AND isclosing=0 AND yearid=$yid
					     				ORDER BY number"))	{
  		foreach ($arr_udods as $udod) 	{
			$len = strlen ($udod->name);
			if ($len > 200)  {
				// $college->name = substr($college->name, 0, 200) . ' ...';
				$udod->name = substr($udod->name,0,strrpos(substr($udod->name,0, 210),' ')) . ' ...';
			}
			$udodmenu[$udod->id] = $udod->name;
		}
	}
  }

  echo '<tr><td>'.get_string('udod', 'block_monitoring').':</td><td>';
  popup_form($scriptname, $udodmenu, 'switchudod', $did, '', '', '', false);
  echo '</td></tr>';
  return 1;
}


// Display list dous as popup_form
function listbox_dous($scriptname, $rid, $did, $yid)
{
  global $CFG;

  $udodmenu = array();
  $doumenu[0] = get_string('selectadou','block_mou_att').' ...';

  if ($rid != 0)  {
    if ($arr_dous =  get_records_sql("SELECT id, name  FROM {$CFG->prefix}monit_education
					     				WHERE rayonid = $rid AND isclosing=0 AND yearid=$yid AND typeeducation=1
					     				ORDER BY number"))	{
  		foreach ($arr_dous as $dou) 	{
			$len = strlen ($dou->name);
			if ($len > 200)  {
				// $college->name = substr($college->name, 0, 200) . ' ...';
				$dou->name = substr($dou->name,0,strrpos(substr($dou->name,0, 210),' ')) . ' ...';
			}
			$doumenu[$dou->id] = $dou->name;
		}
	}
  }

  echo '<tr><td>'.get_string('dou', 'block_mou_att').':</td><td>';
  popup_form($scriptname, $doumenu, 'switchdou', $did, '', '', '', false);
  echo '</td></tr>';
  return 1;
}


// Display list schools as popup_form
function listbox_levelmonit($scriptname, $lid)
{
  $levelmenu = array();
  $levemenu[0] = get_string('levelregion','block_monitoring');
  $levemenu[1] = get_string('levelrayon','block_monitoring');
  $levemenu[2] = get_string('levelschool','block_monitoring');

  popup_form($scriptname, $levemenu, 'switchlevel', $lid, '', '', '', false);
  return 1;
}



// Prints form items with the names $day, $month and $year
function print_date_monitoring($day, $month, $year, $currenttime=0, $howold=10, $disabled=false) {

    if (!$currenttime) {
        // $currenttime = time();
        $days[0] = '-';
        $months[0] = '-';
        $years[0] = '-';
        $currentdate['mday'] = 0;
        $currentdate['mon'] = 0;
        $currentdate['year'] = 0;

    }  else {
	    $currentdate = usergetdate($currenttime);
	}

    for ($i=1; $i<=31; $i++) {
        $days[$i] = $i;
    }
    for ($i=1; $i<=9; $i++) {
        $months[$i] = get_string('lm_0'.$i, 'block_monitoring'); // userdate(gmmktime(12,0,0,$i,1,2000), "%B");
    }
    for ($i=10; $i<=12; $i++) {
        $months[$i] = get_string('lm_'.$i, 'block_monitoring');
    }
    $curryear = date("Y");
    for ($i=($curryear-$howold); $i<=($curryear+40); $i++) {
        $years[$i] = $i;
    }
    choose_from_menu($days,   $day,   $currentdate['mday'], '', '', 0, false, $disabled);
    choose_from_menu($months, $month, $currentdate['mon'],  '', '', 0, false, $disabled);
    choose_from_menu($years,  $year,  $currentdate['year'], '', '', 0, false, $disabled);
}


/**
 * Print a nicely formatted COLOR table.
 *
 * @param array $table is an object with several properties.
 *     <ul<li>$table->head - An array of heading names.
 *     <li>$table->align - An array of column alignments
 *     <li>$table->size  - An array of column sizes
 *     <li>$table->wrap - An array of "nowrap"s or nothing
 *     <li>$table->data[] - An array of arrays containing the data.
 *     <li>$table->width  - A percentage of the page
 *     <li>$table->cellpadding  - Padding on each cell
 *     <li>$table->cellspacing  - Spacing between cells
 * new!!!! $table->bgcolor[] - An array of TD colors
 * new!!!! $table->wraphead
 * new!!!! $table->border
 * new!!!! $table->tablealign  - Align the whole table
 * new!!!! $table->class
 * new!!!! $table->class
 * new!!!! $table->dblhead->head1 - An array of first row heading
 * new!!!! $table->dblhead->span1 - An array of first row spaning (example, rowspan=2 or colspan=11)
 * new!!!! $table->dblhead->head1 - An array of second row heading
 * </ul>
 * @return boolean
 * @todo Finish documenting this function
 */
function print_color_table($table) {

    if (isset($table->align)) {
        foreach ($table->align as $key => $aa) {
            if ($aa && $aa != 'left') {
                $align[$key] = ' align='. $aa;
            } else {
                $align[$key] = '';
            }
        }
    }
    if (isset($table->size)) {
        foreach ($table->size as $key => $ss) {
            if ($ss) {
                $size[$key] = ' width="'. $ss .'"';
            } else {
                $size[$key] = '';
            }
        }
    }
    if (isset($table->wrap)) {
        foreach ($table->wrap as $key => $ww) {
            if ($ww) {
                $wrap[$key] = ' nowrap ';
            } else {
                $wrap[$key] = '';
            }
        }
    }

    if (empty($table->width)) {
        $table->width = '80%';
    }

    if (empty($table->tablealign)) {
        $table->tablealign = 'center';
    }

    if (empty($table->cellpadding)) {
        $table->cellpadding = '5';
    }

    if (empty($table->cellspacing)) {
        $table->cellspacing = '1';
    }

    if (empty($table->class)) {
        $table->class = 'generaltable';
    }

    if (empty($table->headerstyle)) {
        $table->headerstyle = 'header';
    }

    if (empty($table->border)) {
        $table->border = '1';
    }

    $tableid = empty($table->id) ? '' : 'id="'.$table->id.'"';

    print_simple_box_start_old('center', $table->width, '#ffffff', 0);
	// echo '<table width="'.$table->width.' border='.$table->border;
    // echo " cellpadding=\"$table->cellpadding\" cellspacing=\"$table->cellspacing\" class=\"$table->class boxalign$table->tablealign\" $tableid>\n";
    echo '<table width="100%" border=1 align=center ';
    echo " cellpadding=\"$table->cellpadding\" cellspacing=\"$table->cellspacing\" $tableid class=\"$table->class\">\n"; //bordercolor=gray

    $countcols = 0;

    if (!empty($table->head)) {
        $countcols = count($table->head);
        echo '<tr>';
        foreach ($table->head as $key => $heading) {

            if (!isset($size[$key])) {
                $size[$key] = '';
            }
            if (!isset($align[$key])) {
                $align[$key] = '';
            }
            if (isset($table->wraphead) && $table->wraphead == 'nowrap') {
            	$headwrap = ' nowrap="nowrap" ';
            } else 	{
            	$headwrap = '';
            }
            echo '<th '. $align[$key].$size[$key] . $headwrap . " class=\"$table->headerstyle\">". $heading .'</th>'; // class="header c'.$key.'
			// $output .= '<th style="vertical-align:top;'. $align[$key].$size[$key] .';white-space:nowrap;" class="header c'.$key.'" scope="col">'. $heading .'</th>';
        }
        echo '</tr>'."\n";
    }

    if (!empty($table->dblhead)) {
        $countcols = count($table->dblhead->head1);
        echo '<tr>';
        foreach ($table->dblhead->head1 as $key => $heading) {

            if (isset($table->dblhead->size[$key])) {
                $size[$key] = $table->dblhead->size[$key];
            } else {
                $size[$key] = '';
            }

            if (!isset($align[$key])) {
                $align[$key] = '';
            }
            if (isset($table->wraphead) && $table->wraphead == 'nowrap') {
            	$headwrap = ' nowrap="nowrap" ';
            } else 	{
            	$headwrap = '';
            }

            if (isset($table->dblhead->span1[$key])) {
            	$span1 = $table->dblhead->span1[$key];
            } else 	{
            	$span1 = '';
            }

            echo "<th $span1 ". $align[$key].$size[$key] . $headwrap . " class=\"$table->headerstyle\">". $heading .'</th>'; // class="header c'.$key.'
			// $output .= '<th style="vertical-align:top;'. $align[$key].$size[$key] .';white-space:nowrap;" class="header c'.$key.'" scope="col">'. $heading .'</th>';
        }
        echo '</tr>'."\n";

        $countcols = count($table->dblhead->head2);
        echo '<tr>';
        foreach ($table->dblhead->head2 as $key => $heading) {

            if (!isset($size[$key])) {
                $size[$key] = '';
            }
            if (!isset($align[$key])) {
                $align[$key] = '';
            }
            if (isset($table->wraphead) && $table->wraphead == 'nowrap') {
            	$headwrap = ' nowrap="nowrap" ';
            } else 	{
            	$headwrap = '';
            }

            echo '<th '. $align[$key].$size[$key] . $headwrap . " class=\"$table->headerstyle\">". $heading .'</th>'; // class="header c'.$key.'
			// $output .= '<th style="vertical-align:top;'. $align[$key].$size[$key] .';white-space:nowrap;" class="header c'.$key.'" scope="col">'. $heading .'</th>';
        }
        echo '</tr>'."\n";
    }

    if (!empty($table->data)) {
        $oddeven = 1;
        foreach ($table->data as $keyrow => $row) {
            $oddeven = $oddeven ? 0 : 1;
            //echo "<tr class=\"$table->class\">"."\n";
            echo "<tr>"."\n";
            if (is_string($row)) {
                $dd = explode('|', $row); 
                if ($dd[0] == 'hr' and $countcols) {
                    echo '<td colspan="'. $countcols .'"><div class="tabledivider"></div></td>';
                } else if ($dd[0] == 'dr' and $countcols) {
                    echo '<td align=center colspan="'. $countcols .'">'.$dd[1].'</td>';
                }    
            } else {  /// it's a normal row of data
                foreach ($row as $key => $item) {
                    if (!isset($size[$key])) {
                        $size[$key] = '';
                    }
                    if (!isset($align[$key])) {
                        $align[$key] = '';
                    }
                    if (!isset($wrap[$key])) {
                        $wrap[$key] = '';
                    }
                    if (!empty($table->bgcolor[$keyrow][$key])) {
                    	$tdbgcolor = ' bgcolor="#'.$table->bgcolor[$keyrow][$key].'"';
                    }
                    else {
                    	$tdbgcolor = '';
                    }
                    echo '<td '. $align[$key].$size[$key].$wrap[$key].$tdbgcolor. '>'. $item .'</td>'; //  class="'.$table->class.'"
                }
            }
            echo '</tr>'."\n";
        }
    }
    echo '</table>'."\n";
    print_simple_box_end_old();

    return true;
}

/**
 * Update a record in a table
 *
 * $dataobject is an object containing needed data
 * Relies on $dataobject having a variable "id" to
 * specify the record to update
 *
 * @uses $CFG
 * @uses $db
 * @param string $table The database table to be checked against.
 * @param array $dataobject An object with contents equal to fieldname=>fieldvalue. Must have an entry for 'id' to map to the table specified.
 * @return boolean
 * @todo Finish documenting this function. Dataobject is actually an associateive array, correct?
 */
function update_monit_record($table, $dataobject, $setnull=false) {

    global $db, $CFG;

    if (! isset($dataobject->id) ) {
        return false;
    }

    // Determine all the fields in the table
    if (!$columns = $db->MetaColumns($CFG->prefix . $table)) {
        return false;
    }

    // echo '$columns<hr>';
 //   print_r($columns);


    $data = (array)$dataobject;

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    $count = 0;
    $update = '';

    if ($setnull == false) {
	    // Pull out data matching these fields
	    foreach ($columns as $column) {
	        if ($column->name <> 'id' and isset($data[$column->name]) )  {
	  		   // echo $column->name.' '.$column->type.'<br>';
			   if ($column->type == 'int' || $column->type == 'double' || $column->type == 'float unsigned') {
	           		$t = is_numeric($data[$column->name]);
	           		if (!$t) {
						$data[$column->name] = 0;
				    }
	   		   }
	           $ddd[$column->name] = $data[$column->name];
	        }
	    }

	    // echo 'data<hr>';
	    // print_r($ddd);

	    // Construct SQL queries
	    $numddd = count($ddd);

	    foreach ($ddd as $key => $value) {
	        $count++;
	        $update .= $key .' = \''. $value .'\'';   // All incoming data is already quoted
	        if ($count < $numddd) {
	            $update .= ', ';
	        }
	    }
	} else {
	    foreach ($columns as $column) {
			if ($column->name <> 'id')  {
				$ddd[$column->name] = $data[$column->name];
	        }
	    }

	    // Construct SQL queries
		$numddd = count($ddd);

	    foreach ($ddd as $key => $value) {

	        $count++;

			if (empty($value))  {
	            $update .= $key .' = NULL ';   // SET NULL
			} else {
	            $update .= $key .' = \''. $value .'\'';   // All incoming data is already quoted
	        }

	        if ($count < $numddd) {
	            $update .= ', ';
	        }
	    }
	}

    if ($rs = $db->Execute('UPDATE '. $CFG->prefix . $table .' SET '. $update .' WHERE id = \''. $dataobject->id .'\'')) {
		// notify($db->ErrorMsg() .'<br /><br />UPDATE '. $CFG->prefix . $table .' SET '. $update .' WHERE id = \''. $dataobject->id .'\'');
        return true;
    } else {
        if (isset($CFG->debug) and $CFG->debug > 7) {
            notify($db->ErrorMsg() .'<br /><br />UPDATE '. $CFG->prefix . $table .' SET '. $update .' WHERE id = \''. $dataobject->id .'\'');
        }
        if (!empty($CFG->dblogerror)) {
            $debug=array_shift(debug_backtrace());
            error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  UPDATE $CFG->prefix$table SET $update WHERE id = '$dataobject->id'");
        }
        return false;
    }
}


/**
 * Print a standard header FOR MONITORING PAGES
 */
function print_header_mou ($title='', $heading='', $navigation='', $focus='',
                       $meta='', $cache=true, $button='&nbsp;', $menu='',
                       $usexml=false, $bodytags='', $return=false, $is_mou_page=true) {

    global $USER, $CFG, $THEME, $SESSION, $ME, $SITE, $COURSE;

	// print_r($THEME); echo '<hr>';

    // unset ($THEME->standardsheets);
    // $THEME->parent = 'pegasmou';
    // $THEME->parentsheets = array('styles_layout_mou', 'styles_fonts_mou', 'styles_color_mou');
    // $THEME->standardsheets = array('styles_layout_mou', 'styles_fonts_mou', 'styles_color_mou');
    // print_r($THEME); echo '<hr>';
    
    print_header($title, $heading, $navigation, $focus, $meta, $cache, $button, $menu, $usexml, $bodytags, $return);//  $is_mou_page);
                 

}


function switch_edizm (&$rfld, $value, $i=0, $ret=false)
{
    if ($value == '-') return $value;

	if (isset($rfld->edizm))	{
		switch($rfld->edizm) {
			case 'man': case'item': case 'unit': case 'trub': case 'rub':
			     if (!$ret)	$value .= ' '. get_string($rfld->edizm, 'block_monitoring');
			break;
			case 'bool':
			    if ($value == 1) $value = get_string('yes');
			    else if($value == -1) $value = get_string('no');
			    else $value = 'x';
			break;
			case 'gets':
			    if ($rfld->name_field == 'f0_8u') {
					$value = get_string('townvillage'.$value, 'block_monitoring');
				} else {
					$value = get_string($value, 'block_monitoring');
				}
			break;
			case 'data':
				$value = get_rus_format_date($value);
			break;
			case 'text':
			    $j = $i + 1;
				$rfld->name .= "<br>$j. <i>$value</i>";
				if (!$ret) $value = get_string('yes');

			break;
			case 'link':
			    $j = $i + 1;
				$rfld->name .= "<br>$j. <i><a href=\"$value\">$value</a></i>";
				if (!$ret) $value = get_string('yes');
			break;

			case 'expl':
				// list($fd_fact,$fd_rekv,$fd_link) = explode("|", $arrec[$rfld->name_field]);
			break;
			case 'null':
				$value = '';
			break;
		}
	}
	return $value;
}


/**
 * Print the top portion of a standard themed box.
 *
 * @param string $align ?
 * @param string $width ?
 * @param string $color ?
 * @param int $padding ?
 * @param string $class ?
 * @todo Finish documenting this function
 */
function print_simple_box_start_old($align='', $width='', $color='', $padding=5, $class='generalbox', $id='') {

    if ($color) {
        $color = 'bgcolor="'. $color .'"';
    }
    if ($align) {
        $align = 'align="'. $align .'"';
    }
    if ($width) {
        $width = 'width="'. $width .'"';
    }
    if ($id) {
        $id = 'id="'. $id .'"';
    }
    echo "<table $align $width $id class=\"$class\" border=\"0\" cellpadding=\"$padding\" cellspacing=\"0\">".
         "<tr><td $color class=\"$class"."content\">";
}

/**
 * Print the end portion of a standard themed box.
 */
function print_simple_box_end_old() {
    echo '</td></tr></table>';
}


/**
 * Given an object containing firstname and lastname and secondname
 * values, this function returns a string with the
 * full name of the person.
 * @param object $user A {@link $USER} object to get full name of
  */
function fullname_mou($user) {
	
	$fullname = '';
	if (isset($user->lastname))	{
		$fullname .= $user->lastname; 
	}

	if (isset($user->firstname))	{
		$fullname .= ' ' . $user->firstname; 
	}

	if (isset($user->secondname))	{
		$fullname .= ' ' . $user->secondname; 
	}
	
	return $fullname;
}
/*
function print_inside_table($table) {

    print_simple_box_start_old('center', '', '#ffffff', 0);
	// echo '<table width="'.$table->width.' border='.$table->border;
    // echo " cellpadding=\"$table->cellpadding\" cellspacing=\"$table->cellspacing\" class=\"$table->class boxalign$table->tablealign\" $tableid>\n";
  
    $countcols = 0;

    if (!empty($table->head)) {
        $countcols = count($table->head);
        echo '<tr>';
        foreach ($table->head as $key => $heading) {

            if (!isset($size[$key])) {
                $size[$key] = '';
            }
            if (!isset($align[$key])) {
                $align[$key] = '';
            }
            if (isset($table->wraphead) && $table->wraphead == 'nowrap') {
            	$headwrap = ' nowrap="nowrap" ';
            } else 	{
            	$headwrap = '';
            }
            echo '<th '. $align[$key].$size[$key] . $headwrap . " class=\"$table->headerstyle\">". $heading .'</th>'; // class="header c'.$key.'
			// $output .= '<th style="vertical-align:top;'. $align[$key].$size[$key] .';white-space:nowrap;" class="header c'.$key.'" scope="col">'. $heading .'</th>';
        }
        echo '</tr>'."\n";
    }

    if (!empty($table->data)) {
        $oddeven = 1;
        foreach ($table->data as $keyrow => $row) {
            $oddeven = $oddeven ? 0 : 1;
            //echo "<tr class=\"$table->class\">"."\n";
            echo "<tr>"."\n";
            if ($row == 'hr' and $countcols) {
                echo '<td colspan="'. $countcols .'"><div class="tabledivider"></div></td>';
            } else {  /// it's a normal row of data
                foreach ($row as $key => $item) {
                    if (!isset($size[$key])) {
                        $size[$key] = '';
                    }
                    if (!isset($align[$key])) {
                        $align[$key] = '';
                    }
                    if (!isset($wrap[$key])) {
                        $wrap[$key] = '';
                    }
                    if (isset($table->bgcolor[$keyrow][$key])) {
                    	$tdbgcolor = ' bgcolor="#'.$table->bgcolor[$keyrow][$key].'"';
                    }
                    else {
                    	$tdbgcolor = '';
                    }
                    echo '<td '. $align[$key].$size[$key].$wrap[$key].$tdbgcolor. '>'. $item .'</td>'; //  class="'.$table->class.'"
                }
            }
            echo '</tr>'."\n";
        }
    }
    echo '</table>'."\n";
    print_simple_box_end_old();

    return true;
}
*/


// Display list schools as popup_form
function listbox_schools_lastyear($scriptname, $rid, $sid, $yid=4)
{
  global $CFG;

  $yid = get_current_edu_year_id();
  
  $schoolmenu = array();
  $schoolmenu[0] = get_string('selectaschool','block_monitoring').' ...';

  if ($rid != 0)  {
    if ($arr_schools =  get_records_sql("SELECT id, name  FROM {$CFG->prefix}monit_school
					     				WHERE rayonid = $rid AND isclosing=0 AND yearid=$yid
					     				ORDER BY number"))	{
  		foreach ($arr_schools as $school) {
			$len = strlen ($school->name);
			if ($len > 200)  {
				// $school->name = substr($school->name, 0, 200) . ' ...';
				$school->name = substr($school->name,0,strrpos(substr($school->name,0, 210),' ')) . ' ...';
			}
			$schoolmenu[$school->id] =$school->name;
		}
	}
  }

  echo '<tr><td>'.get_string('school', 'block_monitoring').':</td><td>';
  popup_form($scriptname, $schoolmenu, 'switchschool', $sid, '', '', '', false);
  echo '</td></tr>';
  return 1;
}


function  get_name_otchet_year ($yid, &$a, &$b)
{	
  $yearedus = get_records('monit_years');
  $arryearedus[-1] = '2005/2006';
  $arryearedus[0]  = '2006/2007';
  foreach ($yearedus as $yearedu)	{
  	$arryearedus[$yearedu->id] = $yearedu->name;
  }
  $a = $arryearedus[$yid-1];
  $b = $arryearedus[$yid-2];
}



?>
