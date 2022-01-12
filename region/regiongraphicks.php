<?php // $Id: regiongraphicks.php,v 1.1 2009/03/17 08:48:06 Shtifanov Exp $

	require_once("../../../config.php");
	require_once('../lib.php');
	require_once('../lib_excel.php');

    $nm  = optional_param('nm', date('n'), PARAM_INT);       // Month number
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

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }
    $datefrom = get_date_from_month_year($nm, $yid);

    $rkps = array('rkp_prr_ro', 'rkp_prr_eks', 'rkp_ege', 'rkp_d');

    $strrayons = get_string('rayons', 'block_monitoring');
	$strreportregion = get_string('reportregion', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	$breadcrumbs .= " -> $strreportregion";
    print_header_mou("$site->shortname: $strreportregion", $site->fullname, $breadcrumbs);

    print_simple_box_start("center");

	print_tabs_typeforms('region', 'regiongraphicks', $yid, $nm);

	print_simple_box_start("center");

    $strimglink = "graph_teachers.php";
    echo "<center><a target=_blank href=\"$strimglink\"> <img src=\"$strimglink\" alt=graph /> </a> </center>";
    // echo "<center><a target=_blank href=\"$strimglink\">  graph_teachers </a> </center>";
/*  	echo '<center><img src="'.$CFG->wwwroot.'/blocks/monitoring/school/bkp_zp_graph.php?fid='.$fid.'" alt=graph /></center>';
  	    echo '<center><a target=_blank href="'.$CFG->wwwroot.'/blocks/monitoring/school/bkp_zp_graph.php?fid='.$fid.'"></a></center>';
*/
   	print_simple_box_end();

    print_footer();

?>
