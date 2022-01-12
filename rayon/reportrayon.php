<?php // $Id: reportrayon.php,v 1.5 2009/02/25 08:23:51 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $rid = required_param('rid', PARAM_INT);          // Rayon id
    $nm  = optional_param('nm', '1', PARAM_INT);       // Month number
    $yid = optional_param('yid', '0', PARAM_INT);       // Year id
    $fid = optional_param('fid', '0', PARAM_INT);       // Form id

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	if (!$admin_is && !$region_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
    $strreports = get_string('reportrayon', 'block_monitoring');


    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	$breadcrumbs .= " -> $strrayon";
    print_header_mou("$site->shortname: $strrayon", $site->fullname, $breadcrumbs);

    // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);

	echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
	listbox_rayons("reportrayon.php?yid=$yid&amp;rid=", $rid);
	echo '</table>';

	if ($rid == 0) exit();

    $rayon = get_record('monit_rayon', 'id', $rid);
	print_heading($strreports.': '.$rayon->name, "center", 3);

	print_tabs_all_months($nm, "reportrayon.php?rid=$rid&amp;yid=$yid&amp;nm=");
/*
	print_tabs_quarters(1, '#');
    print_tabs_months(1, 1, '#');
*/

    $strstatus = get_string('status', 'block_monitoring');
	$strtable = get_string('table','block_monitoring');
 	$strperiod = get_string('period','block_monitoring');
	$straction = get_string("action","block_monitoring");

    $table->head  = array ($strstatus, $strtable, $strperiod, $straction);
    $table->align = array ("center", "center", "center",  "center");

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }
    $datefrom = get_date_from_month_year($nm, $yid);

    $rkps = array('rkp_prm_mo', 'rkp_prm_eks');

    foreach($rkps as $rkp)	{
	    $strsql = "SELECT * FROM {$CFG->prefix}monit_list_$rkp
 		   		   WHERE (rayonid=$rid) and (datemodified=$datefrom)";
	    add_rkp_to_table($table, $strsql, 'name_'.$rkp, $rkp.".php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;fid=");
	}

   	print_table($table);

    print_footer();

?>


