<?php // $Id: changestatus.php,v 1.3 2008/09/12 08:44:27 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $level = optional_param('level', PARAM_ALPHA);       // Level
    $fid = optional_param('fid', PARAM_INT);       // Form id
    $rid = required_param('rid', PARAM_INT);       // Rayon id
    $sid = optional_param('sid', PARAM_INT);       // School id
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $id = required_param('id', PARAM_INT);       		// id
    $nm = required_param('nm', PARAM_INT);       		// Mounth id
	$confirm = optional_param('confirm', 0, PARAM_INT);
	$status = optional_param('status', 0, PARAM_INT);

	$frm = data_submitted(); /// load up any submitted data

    if($frm->status3) { $status = 3; }
    if($frm->status4) { $status = 4; }
    if($frm->status5) { $status = 5; }
    if($frm->status6) { $status = 6; }

    if (!$site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('school', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

	if($sid != 0) {
		if (!$school = get_record('monit_school', 'id', $sid, 'yearid', $yid)) {
		    error(get_string('errorschool', 'block_monitoring'), "schools.php?rid=$rid");
		}
	}

    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
    $strschool = get_string('school', 'block_monitoring');
    $strschools = get_string('schools', 'block_monitoring');
    $strreports = get_string('reportschool', 'block_monitoring');
    $strrep = get_string('reports', 'block_monitoring');
	$strformname = get_record_sql("select name from {$CFG->prefix}monit_form where id=$fid");
	$strformname = $strformname->name;

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';

	switch ($levelmonit)	{
		case 'region':
		break;
		case 'rayon':
			if ($admin_is  || $region_operator_is || $rayon_operator_is || $school_operator_is)  {
				$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
			}
			$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/school/listforms.php?rid=$rid&amp;sid=$sid&amp;yid=$yid\">$school->name</a>";
			$breadcrumbs .= " -> $strformname";
		break;
		case 'school':
			if ($admin_is  || $region_operator_is || $rayon_operator_is || $school_operator_is)  {
				$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
				$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/school/schools.php?rid=$rid&amp;yid=$yid\">$strschools</a>";
			}
			$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/school/listforms.php?rid=$rid&amp;sid=$sid&amp;yid=$yid\">$school->name</a>";
			$breadcrumbs .= " -> $strformname";
		break;
	}

    print_header_mou("$site->shortname: $strformname", $site->fullname, $breadcrumbs);

	$bkpstatus = get_record_sql("select * from {$CFG->prefix}monit_form_file where (rayonid=$rid)and(schoolid=$sid)and(formid=$fid)and(yearid=$yid)");
	
	if ($confirm == 1) {
		$datemodified = get_date_from_month_year($nm, $yid);
		$data->id = $bkpstatus->id;
		$data->status = $status;
		$data->timemodified = $datemodified;
		update_record('monit_form_file', $data); 	
				
		redirect("$CFG->wwwroot/blocks/monitoring/bkp/bkpmain.php?level=$level&amp;rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid&amp;id=$id", get_string('succesupdatedata','block_monitoring'), 1);
	}

	$s1 = get_string('changestatuscoordination', 'block_monitoring', $strformname);
	notice_yesno($s1, "changestatus.php?level=$level&amp;rid=$rid&amp;sid=$sid&amp;fid=$fid&amp;nm=$nm&amp;yid=$yid&amp;status=$status&amp;id=$id&amp;confirm=1", "listforms.php?rid=$rid&amp;sid=$sid");

	print_footer();
?>