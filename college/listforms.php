<?php // $Id: listforms.php,v 1.3 2009/02/25 08:23:49 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
	require_once('../lib_excel.php');

    $rid = required_param('rid', PARAM_INT);            // Rayon id
    $sid = required_param('sid', PARAM_INT);            // School id
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $fid = optional_param('fid', '0', PARAM_INT);       // Form id
    $nm  = optional_param('nm', date('n'), PARAM_INT);  // Month number

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('college', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $rkps = array('college');

	$action   = optional_param('action', '');
    if ($action == 'excel') {
        print_excel_header('college_'.$sid.'_'.$nm);
		create_excel_woorbook();
	    foreach($rkps as $rkp)	{
			// print_excel_form('rkp_prr_ro', $datefrom);
			print_excel_form($rkp, $datefrom, 'college', $rid, $sid);
		}
		close_excel_woorbook();
        exit();
	}


    if ($sid != 0)	{    	$school = get_record('monit_college', 'id', $sid);
   	    $strschool = $school->name;
    }	else  {   	    $strschool = get_string('college', 'block_monitoring');    }

    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');

    $strschools = get_string('colleges', 'block_monitoring');
    $strreports = get_string('reportschool', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	if ($admin_is  || $region_operator_is || $rayon_operator_is)  {
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/college/college.php?rid=$rid\">$strschools</a>";
	}
	$breadcrumbs .= " -> $strschool";
    print_header_mou("$site->shortname: $strschool", $site->fullname, $breadcrumbs);

    // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);

	if ($admin_is  || $region_operator_is || $rayon_operator_is) {
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		listbox_rayons("listforms.php?sid=0&amp;yid=$yid&amp;rid=", $rid);
		listbox_colleges("listforms.php?rid=$rid&amp;yid=$yid&amp;sid=", $rid, $sid, $yid);
		echo '</table>';
	}

	if ($rid == 0 ||  $sid == 0) {	    print_footer();
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
    } else {    	$yid = 1;
    	$eduyear = get_record('monit_years', 'id', $yid);    }

    $datefrom = get_date_from_month_year($nm, $yid);

    $school = get_record('monit_college', 'id', $sid);

    $str1 = $strreports.': '.$school->name . get_string('zauchyear', 'block_monitoring', $eduyear->name);
	print_heading($str1, "center", 3);

//	print_tabs_typeforms('school', 'monthreport', $nm, $yid, $rid, $sid);

//	print_tabs_all_months($nm, "listforms.php?rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;nm=");

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

	$title = get_string('editcollege','block_monitoring');
	$links = array();

    $sqls = get_records_sql("SELECT * FROM {$CFG->prefix}monit_form WHERE levelmonit='college'");

    foreach($sqls as $sql)	{		$rzds = get_records_sql("SELECT * FROM {$CFG->prefix}monit_razdel WHERE formid=$sql->id");
//		print_r($sql_s);
		foreach($rzds as $rzd)	{			$collegestatus = get_record_sql("select status from {$CFG->prefix}monit_college_listforms where (collegeid=$sid) and (datemodified=$datefrom) and (shortname=$rzd->id)");
			    if($collegestatus){
					$status = get_record('monit_status', 'id', $collegestatus->status);
					$color = $status->color;
					$currstatus = $status->id;
					$status = $status->name;
			    } else {
					$status = get_record('monit_status', 'id', 1);
					$color = $status->color;
					$status = $status->name;
					$currstatus = 1;
			    }

			$table->bgcolor[] = array($color);

			$strlinkupdate = "<a title=\"$title\" href=\"forms.php?sid=$sid&amp;rzd={$rzd->id}&amp;fid=$fid&amp;nm=$nm&amp;yid=$yid&amp;rid=$rid&amp;first=1&amp;reported={$rzd->reported}\">";
			$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/edit.gif\" alt=\"$title\" /></a>&nbsp;";


			$table->data[]=array($status, $rzd->name, $strlinkupdate);

		}
   }

//	print_r($sql);

/*
    foreach($rkps as $rkp)	{
	    $strsql = "SELECT * FROM {$CFG->prefix}monit_college_listforms
 		   		   WHERE (collegeid=$sid) and (shortname='$rkp') and (datemodified=$datefrom)";

 		if ($recsss = get_records_sql($strsql)) 	{ 			// print_r($recsss);
 		    if (count($recsss) > 1) { 		    	notify (get_string('errorinduplicatedform', "block_monitoring"));
//	 		    print_r($recsss);
                echo '<hr>';
	 		}
	 		unset ($recsss);
 		}

		$rec = get_record_sql($strsql);

/*
	    if ($rec = get_record_sql($strsql))	{
	    	$fid = $rec->id;
			$strform_status = get_string('status'.$rec->status, "block_monitoring");
			$strcolor = get_string('status'.$rec->status.'color',"block_monitoring");
			$strform = $rec->shortrusname;// get_string($rec->name,"block_monitoring");
			$currstatus = $rec->status;
		} else {
	      	$fid = 0;
	    	$strform_status = get_string("status1","block_monitoring");
	    	$strcolor = get_string("status1color","block_monitoring");
			$strform = get_string('name_'.$rkp, "block_monitoring");
			$currstatus = 1;
	    }
*/

/*
		if ($currstatus < 4 || ($admin_is  || $region_operator_is || $rayon_operator_is))  {       //
            if ($curryearid == $yid || $admin_is)	{
		 		$links['edit']->url = "htmlforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;fid=";
	 			$links['edit']->title = get_string('editschool','block_monitoring');
		 		$links['edit']->pixpath = "{$CFG->pixpath}/i/edit.gif";
	 		}
	 	}

		if ($currstatus != 1 && $currstatus < 4)  {
	 		$links['status4']->url = "changestatus.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status=4&amp;fid=";
	 		$links['status4']->title = get_string('sendtocoordination', 'block_monitoring');
	 		$links['status4']->pixpath = "{$CFG->pixpath}/s/yes.gif";
        }

		if ($currstatus > 1 && ($admin_is  || $region_operator_is || $rayon_operator_is)) {
	 		$links['status6']->url = "changestatus.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status6=6&amp;fid=";
	 		$links['status6']->title = get_string('status6', 'block_monitoring');
	 		$links['status6']->pixpath = "{$CFG->pixpath}/i/tick_green_big.gif";

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

        if ($currstatus >= 6 && $rayon_operator_is && !$admin_is  && !$region_operator_is)  {        	unset($links);
	 		$links['excel']->url = "to_excel.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;action=excel&amp;fid=";
	 		$links['excel']->title = get_string('downloadexcel');
	 		$links['excel']->pixpath = "{$CFG->pixpath}/f/xlsx.gif";
        }

	    $strlinkupdate = '';
	    foreach ($links as $key => $link)	{

			$strlinkupdate .= "<a title=\"$link->title\" href=\"$link->url$fid\">";
			$strlinkupdate .= "<img src=\"{$link->pixpath}\" alt=\"$link->title\" /></a>&nbsp;";
	    }

		if (isset($links['edit']))  {			 $link = $links['edit'];
        	 $strformrkpu = "<b><a title=\"$link->title\" href=\"$link->url$fid\">$strformrkpu</a></b>";
        }

	    $table->data[] = array ($strform_status, $strform, $strlinkupdate);
		$table->bgcolor[] = array ($strcolor);
		unset($links);
	   // add_rkp_to_table($table, $strsql, , $links, $school_operator_is);
	}
*/
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
	    $options['fid'] = $fid;
	    $options['nm'] = $nm;
	    $options['yid'] = $yid;
	   	$options['sesskey'] = $USER->sesskey;
	    print_single_button("listforms.php", $options, get_string('downloadexcel'));
     ?>
    </td>
</tr>
</table>
<?php
    print_footer();

?>


