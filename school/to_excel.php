<?php // $Id: to_excel.php,v 1.5 2008/10/16 10:25:08 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
	require_once('../lib_excel.php');

    $rid = required_param('rid', PARAM_INT);          // Rayon id
    $sid = required_param('sid', PARAM_INT);          // School id
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $rkp = optional_param('sn', '');       				// Shortname
    $nm  = optional_param('nm', date('n'), PARAM_INT);       // Month number
	$action   = optional_param('action', '');
    $levelmonit  = optional_param('level', 'school');

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('school', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    if ($action == 'excel') {        $datefrom = get_date_from_month_year($nm, $yid);
        print_excel_header($levelmonit.'_'.$sid.'_'.$nm.'_'.$rkp);
		create_excel_workbook();
		print_excel_form($rkp, $datefrom, $levelmonit, $rid, $sid);
		close_excel_workbook();
        exit();
	}

?>


