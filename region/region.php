<?php // $Id: region.php,v 1.16 2010/11/11 14:31:12 Shtifanov Exp $

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

    // $rkps = array('rkp_prr_ro', 'rkp_prr_eks', 'rkp_ege', 'rkp_d');
    $rkps = array('rkp_prr_ro', 'rkp_ege');

	$action   = optional_param('action', '');
    if ($action == 'excel') {
        print_excel_header('region_1_'.$nm);
		create_excel_workbook();
	    foreach($rkps as $rkp)	{
			// print_excel_form('rkp_prr_ro', $datefrom);
			print_excel_form($rkp, $datefrom, 'region');
		}
		close_excel_workbook();
        exit();
	}

    $strrayons = get_string('rayons', 'block_monitoring');
	$strreportregion = get_string('reportregion', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	$breadcrumbs .= " -> $strreportregion";
    print_header_mou("$site->shortname: $strreportregion", $site->fullname, $breadcrumbs);

    print_simple_box_start("center");

	print_tabs_typeforms('region', 'monthreport', $nm, $yid);

	print_tabs_years($yid, "region.php?yid=");

	print_tabs_all_months($nm, "region.php?yid=$yid&amp;nm=");

	$strstatus = get_string('status', 'block_monitoring');
    // $strname = get_string('territory', 'block_monitoring');
	$strtable = get_string('table','block_monitoring');
 	$strperiod = get_string('period','block_monitoring');
	$straction = get_string("action","block_monitoring");

    $table->head  = array ($strstatus, $strtable, $straction);
    $table->align = array ("center", "center", "center");
	$table->width = '60%';
    $table->size = array ('5%', '10%', '5%');
    $table->class = 'feedbackbox';

    foreach($rkps as $rkp)	{
		$strsql = "SELECT * FROM {$CFG->prefix}monit_region_listforms
  		 		   WHERE (shortname='$rkp') and (datemodified=$datefrom)";
//	    add_rkp_to_table($table, $strsql, 'name_'.$rkp, "htmlregionforms.php?nm=$nm&amp;sn=$rkp&amp;fid=");
	    if ($rec = get_record_sql($strsql))	{
	    	$fid = $rec->id;
			$strformrkpu_status = get_string('status'.$rec->status, "block_monitoring");
			$strcolor = get_string('status'.$rec->status.'color',"block_monitoring");
			$strformrkpu = $rec->shortrusname;// get_string($rec->name,"block_monitoring");
			$currstatus = $rec->status;
		} else {
	      	$fid = 0;
	    	$strformrkpu_status = get_string("status1","block_monitoring");
	    	$strcolor = get_string("status1color","block_monitoring");
			$strformrkpu = get_string('name_'.$rkp, "block_monitoring");
			$currstatus = 1;
	    }

	 		$links['edit']->url = "htmlregionforms.php?nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;fid=";
 			$links['edit']->title = get_string('editschool','block_monitoring');
	 		$links['edit']->pixpath = "{$CFG->pixpath}/i/edit.gif";

/*
		if ($currstatus != 1 && $currstatus != 4 || ($admin_is  || $region_operator_is))  {
	 		$links['status4']->url = "changestatus.php?rid=$rid&amp;nm=$nm&amp;sn=$rkp&amp;status=4&amp;fid=";
	 		$links['status4']->title = get_string('sendtocoordination', 'block_monitoring');
	 		$links['status4']->pixpath = "{$CFG->pixpath}/s/yes.gif";
        }


	 		$links['status6']->url = "changestatus.php?rid=$rid&amp;nm=$nm&amp;sn=$rkp&amp;status6=6&amp;fid=";
	 		$links['status6']->title = get_string('status6', 'block_monitoring');
	 		$links['status6']->pixpath = "{$CFG->pixpath}/i/tick_green_big.gif";

	 		$links['status3']->url = "changestatus.php?rid=$rid&amp;nm=$nm&amp;sn=$rkp&amp;status3=3&amp;fid=";
	 		$links['status3']->title = get_string('status3', 'block_monitoring');
	 		$links['status3']->pixpath = "{$CFG->pixpath}/i/return.gif";

	 		$links['status5']->url = "changestatus.php?rid=$rid&amp;nm=$nm&amp;sn=$rkp&amp;status5=5&amp;fid=";
	 		$links['status5']->title = get_string('status5', 'block_monitoring');
	 		$links['status5']->pixpath = "{$CFG->wwwroot}/blocks/monitoring/i/archive.gif";
*/

 		$links['excel']->url = "../school/to_excel.php?level=region&amp;rid=0&amp;sid=0&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;action=excel&amp;fid=";
 		$links['excel']->title = get_string('downloadexcel');
 		$links['excel']->pixpath = "{$CFG->pixpath}/f/xlsx.gif";

	    $strlinkupdate = '';
	    foreach ($links as $key => $link)	{

			$strlinkupdate .= "<a title=\"$link->title\" href=\"$link->url$fid\">";
			$strlinkupdate .= "<img src=\"{$link->pixpath}\" alt=\"$link->title\" /></a>&nbsp;";
	    }

	    $table->data[] = array ($strformrkpu_status, $strformrkpu, $strlinkupdate);
		$table->bgcolor[] = array ($strcolor);
		unset($links);


	}

   	print_color_table($table);

//    print_belgorod_region();

	$options = array();
    $options['fid'] = $fid;
    $options['yid'] = $yid;
    $options['nm'] = $nm;
   	$options['sesskey'] = $USER->sesskey;
   	$options['action'] = 'excel';
   	echo '<div align=center>';
    print_single_button("region.php", $options, get_string("downloadexcel"));
   	echo '</div>';

  	print_simple_box_end();

    print_footer();

?>
