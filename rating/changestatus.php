<?php // $Id: changestatus.php,v 1.2 2010/10/29 11:58:25 Oleg Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $rid = required_param('rid', PARAM_INT);       // Rayon id
    $sid = required_param('sid', PARAM_INT);       // School id
    $fid = required_param('fid', PARAM_INT);       // Form id
    $yid = required_param('yid', PARAM_INT);       // Year id
    $nm = required_param('nm', PARAM_INT);         // Month
    $shortname = required_param('sn');       // Shortname form

	$confirm = optional_param('confirm', 0, PARAM_INT);
    $status = optional_param('status', 0, PARAM_INT);

    if ($confirm == 0 && $status == 0)  {
	    $status3 = optional_param('status3', '');
        if (!empty($status3)) $status = 3;
        else {
		    $status5 = optional_param('status5', '');
        	if (!empty($status5)) $status = 5;
	        else {
			    $status6 = optional_param('status6', '');
 		       	if (!empty($status6)) $status = 6;
 		    }
        }
	}

	$redirlink = "$CFG->wwwroot/blocks/monitoring/rating/listforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid";
	// $rec = data_submitted();
	// print_r($rec);

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('school', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

	if (isregionviewoperator() || israyonviewoperator())  {
        error(get_string('adminaccess', 'block_monitoring'));
	}

	if (!$school = get_record('monit_school', 'id', $sid)) {
	    error(get_string('errorschool', 'block_monitoring'), "schools.php?rid=$rid");
	}

    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
    $strschool = get_string('school', 'block_monitoring');
    $strschools = get_string('schools', 'block_monitoring');
    $strreports = get_string('reportschool', 'block_monitoring');
    $strrep = get_string('reports', 'block_monitoring');
	$strformname = get_string('name_'.$shortname,'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
    if ($admin_is  || $region_operator_is || $rayon_operator_is)  {
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/school/schools.php?rid=$rid\">$strschools</a>";
	}
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rating/listforms.php?rid=$rid&amp;sid=$sid\">$school->name</a>";
	$breadcrumbs .= " -> $strformname";
    print_header_mou("$SITE->shortname: $strformname", $SITE->fullname, $breadcrumbs);

	if ($fid == 0) {
		notice(get_string('changestatusnew','block_monitoring'), $redirlink);
	}

	if ($confirm == 1) {
	    $datefrom = get_date_from_month_year($nm, $yid);
	    $strsql = "SELECT * FROM {$CFG->prefix}monit_rating_listforms
 		   		   WHERE (schoolid=$sid) and (shortname='$shortname') and (datemodified=$datefrom)";
	    if ($rec = get_record_sql($strsql))	{
             // print_r($rec);
           $rec->status = $status;
	       if (!update_record('monit_rating_listforms', $rec))	{
				error(get_string('errorinupdatingform','block_monitoring'), $redirlink);
		   }
		   redirect($redirlink, get_string('succesupdatedata','block_monitoring'), 1);
		}
	}


    if ($status == 4)	{
		$s1 = get_string('changestatuscoordination', 'block_monitoring', $strformname);
    } else {
		print_heading(get_string('changestatus', 'block_monitoring') .' :: ' .$strformname);
		//  $s1 = get_string('changestatuscheckfull', 'block_monitoring', ' школе &laquo;'. $school->name.'&raquo;');
		$s1 = get_string('changestatuscheckfull', 'block_monitoring'). ' ' . $strformname . " на '". get_string('status'.$status, 'block_monitoring') . "'?";
	}

	notice_yesno($s1, "changestatus.php?rid=$rid&amp;sid=$sid&amp;fid=$fid&amp;nm=$nm&amp;yid=$yid&amp;sn=$shortname&amp;status=$status&amp;confirm=1", $redirlink);

	print_footer();


?>

