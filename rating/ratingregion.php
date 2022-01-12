<?php // $Id: ratingregion.php,v 1.7 2012/11/30 10:49:04 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
	require_once('../../mou_ege/lib_ege.php');    
	require_once('../lib_excel.php');
    require_once('lib_rating.php');
    require_once('lib_report.php');        

    $rid = optional_param('rid', 0, PARAM_INT);
    $sid = optional_param('sid', 0, PARAM_INT);            // School id
	// $nm  = optional_param('nm', date('n'), PARAM_INT);       // Month number
	$nm = 9;
    $yid = optional_param('yid', 0, PARAM_INT);       	// Year id
    $stype = optional_param('stype', 0, PARAM_INT);     // School type
    $seat = optional_param('seat', 0, PARAM_INT);     // Mesto (town/village)
    

    require_login();

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }
    init_rating_parameters($yid, $shortname, $select, $order);    

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
    $rayon_operator_is = false;
	if (!$admin_is && !$region_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'), "$CFG->wwwroot/login/index.php");
	}

	$action   = optional_param('action', '');
    if ($action == 'excel') 	{
		$fnum = '';
		switch ($shortname)	{
			case 'rating_1': $fnum = 1;
			break;
			case 'rating_2': $fnum = 2;
			break;		
			case 'rating_9': $fnum = 9;
			break;
		}

		$table = table_ratingregion($yid, $shortname, $fnum, $stype, $seat);
		// print_r($table);
		// print_color_table($table);
  		print_table_to_excel($table);
        exit();
	}

/*	if (isregionviewoperator() || israyonviewoperator())  {
        error(get_string('adminaccess', 'block_monitoring'));
	}
*/

    $strrayons = get_string('rayons', 'block_monitoring');
	$strtitle  = get_string('ratingregion', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> $strtitle";
    print_header_mou("$SITE->shortname: $strtitle", $SITE->fullname, $breadcrumbs);

	$redirlink = 'ratingregion.php?yid='.$yid;
	
    $currenttab = 'ratingregion';
    include('tabs.php');
	
	// print_tabs_years($yid, "ratingregion.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=", true);
    // print_tabs_years_link("ratingregion.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=", $rid, $sid, $yid, true);
    print_tabs_years_rating("ratingregion.php?a=0", $rid, $sid, $yid);

	$fnum = '';
	switch ($shortname)	{
		case 'rating_1': $fnum = 1;
		break;
		case 'rating_2': $fnum = 2;
		break;		
		case 'rating_9': $fnum = 9;
		break;
	}

	echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
	listbox_rating_level("ratingregion.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;seat=$seat&amp;stype=$stype&amp;sn=", $shortname, $yid, true);
	listbox_seat("ratingregion.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$shortname&amp;stype=$stype&amp;seat=", $seat);
	listbox_type_school("ratingregion.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$shortname&amp;seat=$seat&amp;stype=", $stype);
	echo '</table><p>';	

	if ($shortname != 'rating_0' && $stype != 0 && $seat != 0)	{	
		$table = table_ratingregion($yid, $shortname, $fnum, $stype, $seat);
		
   	   	 print_color_table($table);

		$options = array('action'=> 'excel', 'rid' => $rid, 'sid' => $sid, 'yid' => $yid, 
						  'nm' => $nm, 'sn' => $shortname, 'seat' => $seat, 'stype' => $stype, 
						  'sesskey' => $USER->sesskey);
	   	echo '<center>';
	    print_single_button("ratingregion.php", $options, get_string('downloadexcel'));
	    echo '</center>';
	}

	// print_string('remarkyear', 'block_monitoring');
    print_footer();


function table_ratingregion($yid, $shortname, $fnum, $stype, $seat)	
{
	global $CFG, $admin_is, $region_operator_is, $rayon_operator_is;

    $numberf = get_string('ratingnum', 'block_monitoring');
    $strname = get_string('school', 'block_monitoring');
    $valueofpokazatel = get_string('valueofpokazatel', 'block_monitoring');

    $table = new stdClass();
    $table->head  = array ($numberf, $strname, $valueofpokazatel);
    $table->align = array ("center", "left", "center");
	$table->width = '90%';
    $table->size = array ('5%', '90%', '5%');
    $table->columnwidth = array (7, 100, 15);
	$table->class = 'moutable';
	
   	$table->titlesrows = array(30, 30, 30, 30);
    $table->titles = array();
    $table->titles[] = get_string('ratingregion', 'block_monitoring'); 
	$table->titles[] = get_string('name_'.$shortname, 'block_monitoring');
	$table->titles[] = get_string('typesettlement'.$seat, 'block_monitoring');
	  $type_schools = array(); // get_records_sql("select id, name from {$CFG->prefix}monit_school_type");
	  $type_schools[5] = 'Начальная общеобразовательная школа';
	  $type_schools[6] = 'Основная общеобразовательная школа';
	  $type_schools[1] = 'Средняя общеобразовательная школа';
	  $type_schools[99] = 'Школа «повышенного» статуса (гимназия, лицей, школа с УИОП и т.п.)';  
	$table->titles[] = $type_schools[$stype];
    $table->downloadfilename = "ratingregion_{$shortname}_{$seat}_{$stype}";
    $table->worksheetname = 'ratingregion';
	
	if ($stype == 99)	{
		$strwhere1 = 'stateinstitution in (2,3,4)';
	} else {
		$strwhere1 = 'stateinstitution = ' . $stype;
	}

	if ($seat == 2) { 
		$strwhere2 = 'typesettlement > 1';
	} else {
		$strwhere2 = 'typesettlement = 1';
	}	
	
	// $datefrom = get_date_from_month_year($nm, $yid);
	// $curryid = get_current_edu_year_id();
    $curryid = $yid;
	
	$strsql =  "SELECT id, name  FROM {$CFG->prefix}monit_school
				WHERE $strwhere1 AND $strwhere2 AND yearid=$curryid";	

	if ($schools = get_records_sql($strsql))	{
		
        $schoolsarray = array();
        $schoolsname = array();
        $schoolsmark = array();
	    foreach ($schools as $sa)  {
	        $schoolsarray[] = $sa->id;
	        $schoolsname[$sa->id] = $sa->name;
	        $schoolsmark[$sa->id] = 0;
	    }
	    $schoolslist = implode(',', $schoolsarray);

		$strsql = "SELECT id, rayonid, schoolid, rating_1, rating_2 
				   FROM {$CFG->prefix}monit_rating_total
				   WHERE schoolid in ($schoolslist) AND yearid=$yid";

	    if ($ratschools = get_records_sql($strsql)) 	{
		    foreach ($ratschools as $rs)  {
		    	switch ($fnum)	{
		    		case 1: $schoolsmark[$rs->schoolid] = $rs->rating_1;
		    		break;
		    		case 2: $schoolsmark[$rs->schoolid] = $rs->rating_2;
		    		break;
		    		case 9: $schoolsmark[$rs->schoolid] = $rs->rating_1 + $rs->rating_2;
		    		break;
		    	}
		    }
			arsort($schoolsmark);
		}
		
		reset($schoolsmark);
		$maxmark = current($schoolsmark);
		// echo $maxsm; 
		$placerating = array();
		$mesto = 1;
		foreach ($schoolsmark as $schoolid => $schoolmark) {
			if ($schoolmark > 0) {
				if ($schoolmark == $maxmark)	{
					$placerating[$schoolid] = $mesto;
				} else {
					$placerating[$schoolid] = ++$mesto;
					$maxmark = $schoolmark; 
				}	 
			} else {
				$placerating[$schoolid] = '-';
			}
		}	
			
 	
		foreach ($schoolsmark as $schoolid => $schoolmark) {
			$schoolname = $schoolsname[$schoolid];
			$schoolname = "<strong>$schoolname</strong></a>";
			// $mesto = $placerating[$schoolid];
			$mesto = '<b><i>'.$placerating[$schoolid] . '</i></b>';
			if ($schoolmark >= 0)	{
			   $strmark = "<b><font color=green>$schoolmark</font></b>";	
			} else {
			   $strmark = "<b><font color=red>-</font></b>";	
			}
		 	
		    $table->data[] = array ($mesto, $schoolname , $strmark);
		}    
	}
	
	return $table;
}

?>