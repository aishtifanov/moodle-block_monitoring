<?php // $Id: listforms.php,v 1.32 2013/02/25 06:17:19 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
	require_once('../lib_excel.php');

    $rid = required_param('rid', PARAM_INT);            // Rayon id
    $sid = required_param('sid', PARAM_INT);            // School id
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $fid = optional_param('fid', '0', PARAM_INT);       // Form id
    $nm  = optional_param('nm', date('n'), PARAM_INT);  // Month number

    if ($nm  >= 1 && $nm <= 5) {
        $nm = 12;
    } else if ($nm  >= 7 && $nm  <= 8) {
        $nm  = 6;
    } else if ($nm  >= 10 && $nm  <= 11) {
        $nm  = 9;
    }

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

    // $rkps = array('rkp_u', 'rkp_du', 'rkp_prm_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp');
	// $rkps = array('rkp_u', 'rkp_prm_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp', 'bkp_kbo');
	// $rkps = array('rkp_u', 'rkp_prm_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp');
    $rkps = array('rkp_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp');
		
	$action   = optional_param('action', '');
    if ($action == 'excel') 	{
        $datefrom = get_date_from_month_year($nm, $yid);
        print_excel_header('school_'.$sid.'_'.$nm.'_all');
		create_excel_workbook();
	    foreach($rkps as $rkp)	{
			// print_excel_form('rkp_prr_ro', $datefrom);
			print_excel_form($rkp, $datefrom, 'school', $rid, $sid);
		}
		close_excel_workbook();
        exit();
	}


    if ($sid != 0)	{
    	$school = get_record('monit_school', 'id', $sid);
   	    $strschool = $school->name;
    }	else  {
   	    $strschool = get_string('school', 'block_monitoring');
    }

    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');

    $strschools = get_string('schools', 'block_monitoring');
    $strreports = get_string('reportschool', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	if ($admin_is  || $region_operator_is || $rayon_operator_is)  {
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/school/schools.php?rid=$rid\">$strschools</a>";
	}
	$breadcrumbs .= " -> $strschool";
    print_header_mou("$site->shortname: $strschool", $site->fullname, $breadcrumbs);

    // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);

	if ($admin_is  || $region_operator_is || $rayon_operator_is) {
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		listbox_rayons("listforms.php?sid=0&amp;yid=$yid&amp;rid=", $rid);
		listbox_schools("listforms.php?rid=$rid&amp;yid=$yid&amp;sid=", $rid, $sid, $yid);
		echo '</table>';
	}

	if ($rid == 0 ||  $sid == 0) {
	    print_footer();
	 	exit();
	}

	if ($rayon_operator_is && $rayon_operator_is != $rid)  {
		notify(get_string('selectownrayon', 'block_monitoring'));
	    print_footer();
		exit();
	}

    $curryearid = get_current_edu_year_id();
    if ($yid != 0)	{
    	$eduyear = get_record('monit_years', 'id', $yid);
    } else {
    	$yid = 1;
    	$eduyear = get_record('monit_years', 'id', $yid);
    }

    $datefrom = get_date_from_month_year($nm, $yid);

    $school = get_record('monit_school', 'id', $sid);

    $str1 = $strreports.': '.$school->name . get_string('zauchyear', 'block_monitoring', $eduyear->name);
	print_heading($str1, "center", 3);

	// print_tabs_years($yid, );
	print_tabs_years_link("listforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=", $rid, $sid, $yid);
	
    // print_tabs_typeforms('school', 'monthreport', $nm, $yid, $rid, $sid);
        echo '<p><em><strong>Замечание</strong></em>: В целях упорядочения ведения отчетности электронного мониторинга 
    образовательных учреждений Белгородской области, оптимизации работы муниципальных и школьных операторов 
    принято решение об изменении регламента заполнения ежемесячных отчетов образовательными учреждениями. 
    Отчеты РКП-у (ООУ), РКП-пр.м (ООУ), БКП-пред.(ООУ), БКП-сотр.(ООУ), БКП-фин. (ООУ), БКП-зп (ООУ)
     операторами образовательных учреждений будут заполняться только в <strong>декабре, июне и сентябре</strong>.';


	print_tabs_all_months($nm, "listforms.php?rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;nm=");

	/*
	print_tabs_quarters(1, '#');
    print_tabs_months(1, 1, '#');
    */

    $strstatus = get_string('status', 'block_monitoring');
    // $strname = get_string('territory', 'block_monitoring');
	$strtable = get_string('table','block_monitoring');
 	$strperiod = get_string('period','block_monitoring');
	$straction = get_string("action","block_monitoring");

    $table->head  = array ($strstatus, $strtable, $straction);
    $table->align = array ("center", "center", "center");
	$table->width = '60%';
    $table->size = array ('7%', '10%', '5%');
	$table->class = 'moutable';

    foreach($rkps as $rkp)	{
		$links = array();

	    $strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
 		   		   WHERE (schoolid=$sid) and (shortname='$rkp') and (datemodified=$datefrom)";

 		if ($recsss = get_records_sql($strsql)) 	{
 			// print_r($recsss);
 		    if (count($recsss) > 1) {
 		    	notify (get_string('errorinduplicatedform', "block_monitoring"));
	 		    print_r($recsss);
                echo '<hr>';
	 		}
	 		unset ($recsss);
 		}

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

		if ($currstatus < 4 || ($admin_is  || $region_operator_is || $rayon_operator_is))  {       //
           // if ($curryearid == $yid || $admin_is)	{
		 		$links['edit']->url = "htmlforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;fid=";
	 			$links['edit']->title = get_string('editschool','block_monitoring');
		 		$links['edit']->pixpath = "{$CFG->pixpath}/i/edit.gif";
	 		// }
	 	}

		if ($currstatus != 1 && $currstatus < 4)  {
	 		$links['status4']->url = "changestatus.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status=4&amp;fid=";
	 		$links['status4']->title = get_string('sendtocoordination', 'block_monitoring');
	 		$links['status4']->pixpath = "{$CFG->pixpath}/s/yes.gif";
        }

		if ($currstatus > 1 && ($admin_is  || $region_operator_is)) {
	 		$links['status6']->url = "changestatus.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status6=6&amp;fid=";
	 		$links['status6']->title = get_string('status6', 'block_monitoring');
	 		$links['status6']->pixpath = "{$CFG->pixpath}/i/tick_green_big.gif";
        }    
		if ($currstatus > 1 && ($admin_is  || $region_operator_is || $rayon_operator_is)) {
	 		$links['status3']->url = "changestatus.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status3=3&amp;fid=";
	 		$links['status3']->title = get_string('status3', 'block_monitoring');
	 		$links['status3']->pixpath = "{$CFG->pixpath}/i/return.gif";
	 	}

		if ($currstatus >= 6 && ($admin_is  || $region_operator_is)) {
	 		$links['status5']->url = "changestatus.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status5=5&amp;fid=";
	 		$links['status5']->title = get_string('status5', 'block_monitoring');
	 		$links['status5']->pixpath = "{$CFG->wwwroot}/blocks/monitoring/i/archive.gif";
	 	}

 		$links['excel']->url = "to_excel.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;action=excel&amp;fid=";
 		$links['excel']->title = get_string('downloadexcel');
 		$links['excel']->pixpath = "{$CFG->pixpath}/f/xlsx.gif";

        if ($currstatus >= 6 && $rayon_operator_is && !$admin_is  && !$region_operator_is)  {
        	unset($links);
	 		$links['excel']->url = "to_excel.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;action=excel&amp;fid=";
	 		$links['excel']->title = get_string('downloadexcel');
	 		$links['excel']->pixpath = "{$CFG->pixpath}/f/xlsx.gif";
        }

	    $strlinkupdate = '';
	    foreach ($links as $key => $link)	{

			$strlinkupdate .= "<a title=\"$link->title\" href=\"$link->url$fid\">";
			$strlinkupdate .= "<img src=\"{$link->pixpath}\" alt=\"$link->title\" /></a>&nbsp;";
	    }

		if (isset($links['edit']))  {
			 $link = $links['edit'];
        	 $strformrkpu = "<b><a title=\"$link->title\" href=\"$link->url$fid\">$strformrkpu</a></b>";
        }

	    $table->data[] = array ($strformrkpu_status, $strformrkpu, $strlinkupdate);
		$table->bgcolor[] = array ($strcolor);
		unset($links);
	   // add_rkp_to_table($table, $strsql, , $links, $school_operator_is);
	}

   	// print_table($table);
   	print_color_table($table);

?>
<form name="indices" method="post" target=blank action="../indices/indices.php">
<input type="hidden" name="rid" value="<?php echo $rid ?>" />
<input type="hidden" name="sid" value="<?php echo $sid ?>" />
<input type="hidden" name="yid" value="<?php echo $yid ?>" />
<input type="hidden" name="level" value="school" />
<input type="hidden" name="report" value="8" />
<input type="hidden" name="sesskey" value="<?php echo $USER->sesskey ?>">
<table border="0" align=center>
<tr valign="top">
    <td align="center"><input type="submit"  value="<?php print_string('indices', 'block_monitoring') ?>" />
    </td>
    </form>
    <td align="center">
    <?php
		$options = array();
	   	$options['action'] = 'excel';
	    $options['rid'] = $rid;
	    $options['sid'] = $sid;
	    $options['yid'] = $yid;
	    $options['fid'] = $fid;
	    $options['nm'] = $nm;
	   	$options['sesskey'] = $USER->sesskey;
	    print_single_button("listforms.php", $options, get_string('downloadexcel'));
     ?>
    </td>
</tr>
</table>
<?php

	// print_string('remarkyear', 'block_monitoring');
    print_footer();

?>


