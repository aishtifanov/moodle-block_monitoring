<?php // $Id: sumreports.php,v 1.7 2010/09/24 12:35:04 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
	require_once('../lib_excel.php');

    $rid = required_param('rid', PARAM_INT);            // Rayon id
    $yid = optional_param('yid', '0', PARAM_INT);       // Year id
    $nm  = optional_param('nm', date('n'), PARAM_INT);  // Month number
    $fid = optional_param('fid', '0', PARAM_INT);       // Form id

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $datefrom = get_date_from_month_year($nm, $yid);

    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
    $strreports = get_string('sumreportsrayon', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	$breadcrumbs .= " -> $strrayon";
    print_header_mou("$SITE->shortname: $strrayon", $SITE->fullname, $breadcrumbs);

    // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);

    $curryearid = get_current_edu_year_id();
    if ($yid == 0)	{
    	$yid = $curryearid;
    }

    if ($admin_is  || $region_operator_is) {  // || $rayon_operator_is)  {
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		listbox_rayons("sumreports.php?rid=", $rid);
		echo '</table>';

	    if ($rid == 0) {
		    print_footer();
		    exit();
	    }

		if ($rayon_operator_is && $rayon_operator_is != $rid)  {
			notify(get_string('selectownrayon', 'block_monitoring'));
		    print_footer();
			exit();
		}
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

    $rayon = get_record('monit_rayon', 'id', $rid);
	print_heading($strreports.': '.$rayon->name, "center", 3);


	print_tabs_years($yid, "sumreports.php?rid=$rid&amp;yid=");

	print_tabs_all_months($nm, "sumreports.php?rid=$rid&amp;yid=$yid&amp;nm=");

	print_heading( get_string('unrolledreports', 'block_monitoring'), "center", 3);

    unset($table);

    // $rkps = array('rkp_u', 'rkp_prm_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp', 'bkp_kbo');
    // $rkps = array('rkp_u', 'rkp_prm_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp');
    $rkps = array('rkp_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp');
    $strstatus = get_string('status', 'block_monitoring');
	$strtable = get_string('table','block_monitoring');
	$straction = get_string("action","block_monitoring");

    $table->head  = array ($strstatus, $strtable, $straction);
    $table->align = array ("center", "center", "center");
	$table->width = '60%';
    $table->size = array ('7%', '10%', '5%');
	$table->class = 'moutable';

	$links = array();

	$strformrkpu_status = get_string('summaryreport', 'block_monitoring');
	$strcolor = get_string('status5color', 'block_monitoring');

    foreach($rkps as $rkp)	{

 		$links['view']->url = "bkp_all_ex.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp";
		$links['view']->title = get_string('summaryreport','block_monitoring');
 		$links['view']->pixpath = "{$CFG->pixpath}/f/xlsx.gif";

/*
 		$links['excel']->url = "to_excel.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;sn=$rkp&amp;action=excel&amp;fid=";
 		$links['excel']->title = get_string('downloadexcel');
 		$links['excel']->pixpath = "{$CFG->pixpath}/f/xlsx.gif";
*/
	    $strlinkupdate = '';
	    foreach ($links as $key => $link)	{

			$strlinkupdate .= "<a title=\"$link->title\" href=\"$link->url\">";
			$strlinkupdate .= "<img src=\"{$link->pixpath}\" alt=\"$link->title\" /></a>&nbsp;";
	    }

		$strformrkpu = "<b><a href=bkp_all_ex.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp>" . get_string('name_'.$rkp, "block_monitoring") . '</a></b>';

	    $table->data[] = array ($strformrkpu_status, $strformrkpu, $strlinkupdate);
		$table->bgcolor[] = array ($strcolor);
		unset($links);
	   // add_rkp_to_table($table, $strsql, , $links, $school_operator_is);
	}

   	// print_table($table);
   	print_color_table($table);

	echo '<table border="0" align=center>
		  <tr valign="top">
 		  <td align="center">';

	$options = array();
   	$options['mode'] = 'all';
    $options['rid'] = $rid;
    $options['yid'] = $yid;
    $options['nm'] = $nm;
    $options['sn'] = '-';
   	$options['sesskey'] = $USER->sesskey;
    print_single_button("bkp_all_ex.php", $options, get_string('downloadexcel'));

    echo '</td></tr></table>';

	echo '<hr>';

	print_heading( get_string('integralreports', 'block_monitoring'), "center", 3);

    unset($table);

    // $rkps = array('bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp', 'bkp_kbo');
    $rkps = array('bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp');
    $strstatus = get_string('status', 'block_monitoring');
	$strtable = get_string('table','block_monitoring');
	$straction = get_string("action","block_monitoring");

    $table->head  = array ($strstatus, $strtable, $straction);
    $table->align = array ("center", "center", "center");
	$table->width = '60%';
    $table->size = array ('7%', '10%', '5%');
	$table->class = 'moutable';

	$links = array();

	$strformrkpu_status = get_string('integralreportone', 'block_monitoring');
	$strcolor = get_string('status5color', 'block_monitoring');

    foreach($rkps as $rkp)	{

 		$links['view']->url = "{$rkp}.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp";
		$links['view']->title = get_string('integralreportone','block_monitoring');
 		$links['view']->pixpath = "{$CFG->pixpath}/i/grades.gif";

/*
 		$links['excel']->url = "to_excel.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;sn=$rkp&amp;action=excel&amp;fid=";
 		$links['excel']->title = get_string('downloadexcel');
 		$links['excel']->pixpath = "{$CFG->pixpath}/f/xlsx.gif";
*/
	    $strlinkupdate = '';
	    foreach ($links as $key => $link)	{

			$strlinkupdate .= "<a title=\"$link->title\" href=\"$link->url\">";
			$strlinkupdate .= "<img src=\"{$link->pixpath}\" alt=\"$link->title\" /></a>&nbsp;";
	    }

		$strformrkpu = "<b><a href={$rkp}.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp>" . get_string('name_'.$rkp, "block_monitoring") . '</a></b>';

	    $table->data[] = array ($strformrkpu_status, $strformrkpu, $strlinkupdate);
		$table->bgcolor[] = array ($strcolor);
		unset($links);
	   // add_rkp_to_table($table, $strsql, , $links, $school_operator_is);
	}

   	// print_table($table);
   	print_color_table($table);





    print_footer();

?>


