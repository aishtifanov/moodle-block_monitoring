<?PHP // $Id: delremark.php,v 1.2 2009/04/13 05:55:47 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../../monitoring/lib.php');

    $rid = required_param('rid', PARAM_INT);   // Rayon id
    $sid = required_param('sid', PARAM_INT);	// School id
	$yid = required_param('yid', PARAM_INT);			// Year id
	$mid = optional_param('mid', 0, PARAM_INT);			// Remark id
	$confirm = optional_param('confirm');

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('staff');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('staffaccess', 'block_mou_att'));
	}

	if (isregionviewoperator())  {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $straccreditation = get_string('accreditation', 'block_monitoring');
    $strremarks = get_string('remarks', 'block_monitoring');
   	$straddremark = get_string('delremark','block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= "-> <a href=\"accreditation.php?rid=$rid&amp;yid=$yid&amp;sid=$sid\">$straccreditation</a>";
	$breadcrumbs .= "-> $strremarks";
	$breadcrumbs .= "-> $straddremark";
    print_header("$site->shortname: $straddremark", $site->fullname, $breadcrumbs);

	if (isset($confirm)) {
		delete_records('monit_accr_remark', 'id', $mid);
		//  add_to_log(1, 'school', 'Discipline deleted', 'deldiscipline.php', $USER->lastname.' '.$USER->firstname);
		redirect("accreditation.php?tab=schoolreport&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;ataba=remark", get_string('remarkdeleted','block_monitoring'));
	}

	$remark = get_record("monit_accr_remark", "id", $mid);

	print_heading($straddremark .' :: ' .$remark->name);

    // $str = get_string('disciplinelow', 'block_mou_ege') . ' ' . "'$adiscipl->name'";
    $str = "'$remark->name'";

	notice_yesno(get_string('deletecheckfull', '', $str),
               "delremark.php?rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;mid=$mid&amp;confirm=1",
               "accreditation.php?tab=schoolreport&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;ataba=remark");

	print_footer();
?>
