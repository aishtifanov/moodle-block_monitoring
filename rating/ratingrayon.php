<?php // $Id: ratingrayon.php,v 1.8 2012/10/18 10:40:41 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
	require_once('../lib_excel.php');
    require_once('lib_rating.php');

    $rid = required_param('rid', PARAM_INT);            // Rayon id
    $sid = optional_param('sid', 0, PARAM_INT);            // School id
    $yid = optional_param('yid', 0, PARAM_INT);       		// Year id
    // $nm  = optional_param('nm', 9, PARAM_INT);  // Month number
    $criteriaid = optional_param('cid', 0);       // Shortname form
    $shortname = optional_param('sn', '');
    $nm = 9;

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is) { //  && !$rayon_operator_is ) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }

    init_rating_parameters($yid, $shortname, $select, $order);    
    $select .=  " AND edizm <> 'null'";

	$strtitle = get_string('ratingrayon', 'block_monitoring');
	
    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	if ($admin_is  || $region_operator_is || $rayon_operator_is)  {
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	}
	$breadcrumbs .= " -> $strtitle";	
    print_header_mou("$SITE->shortname: $strtitle", $SITE->fullname, $breadcrumbs);

    // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);

    $currenttab = 'ratingrayon';
    include('tabs.php');

	if ($admin_is  || $region_operator_is) {
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		listbox_rayons("ratingrayon.php?sid=0&amp;yid=$yid&amp;rid=", $rid);
		echo '</table>';
	}

	if ($rid == 0) {
	    print_footer();
	 	exit();
	}

	if ($rayon_operator_is && $rayon_operator_is != $rid)  {
		notify(get_string('selectownrayon', 'block_monitoring'));
	    print_footer();
		exit();
	}


    print_tabs_years_rating("ratingrayon.php?a=0", $rid, $sid, $yid);
    echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
	listbox_rating_level("ratingrayon.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=", $shortname, $yid);
    echo '<br>';
    init_rating_parameters($yid, $shortname, $select, $order);    
    $select .=  " AND edizm <> 'null'";
	listbox_rating_criteria("ratingrayon.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$shortname&amp;cid=", $shortname, $select, $criteriaid, $order);
    echo '</table>';
	echo '<p>';
	
	if ($criteriaid <> 0)	{	
		$table = table_ratingrayon($rid, $sid, $yid, $nm, $shortname, $select, $criteriaid);
   	   	 print_color_table($table);

		$options = array('action'=> 'excel', 'rid' => $rid, 'sid' => $sid, 'yid' => $yid, 
						  'nm' => $nm,  'sesskey' => $USER->sesskey);
	   	echo '<center>';
	    print_single_button("listcriteria.php", $options, get_string('downloadexcel'));
	    echo '</center>';
	}

	// print_string('remarkyear', 'block_monitoring');
    print_footer();


function table_ratingrayon($rid, $sid, $yid, $nm, $shortname, $select, $criteriaid)	
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
	$table->class = 'moutable';

	$datefrom = get_date_from_month_year($nm, $yid);
	// $curryid = get_current_edu_year_id();
    $curryid = $yid;

	$strsql =  "SELECT id, name  FROM {$CFG->prefix}monit_school
				WHERE rayonid = $rid AND isclosing=0 AND yearid=$curryid
				ORDER BY number";	

	$color = 'red';
	if ($schools = get_records_sql($strsql))	{
		
        $schoolsarray = array();
        $schoolsname = array();
        $schoolsmark = array();
	    foreach ($schools as $sa)  {
	        $schoolsarray[] = $sa->id;
	        $schoolsname[$sa->id] = $sa->name;
	        $schoolsmark[$sa->id] = -1;
	    }
	    $schoolslist = implode(',', $schoolsarray);

		$strsql = "SELECT id, schoolid, mark FROM {$CFG->prefix}monit_rating_school
		 		   WHERE (schoolid in ($schoolslist)) AND criteriaid=$criteriaid AND yearid=$yid";
	    if ($ratschools = get_records_sql($strsql)) 	{
		    foreach ($ratschools as $rs)  {
		        $schoolsmark[$rs->schoolid] = $rs->mark;
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
			$schoolname = "<strong>$schoolname</strong></a>&nbsp;";
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