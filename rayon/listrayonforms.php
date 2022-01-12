<?php // $Id: listrayonforms.php,v 1.21 2010/01/21 08:19:14 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
	require_once('../lib_excel.php');

    $rid = required_param('rid', PARAM_INT);          // Rayon id
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $nm  = optional_param('nm', date('n'), PARAM_INT);       // Month number
    $fid = optional_param('fid', '0', PARAM_INT);       // Form id

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $datefrom = get_date_from_month_year($nm, $yid);

    // $rkps = array('rkp_prm_mo', 'rkp_prm_eks');
    $rkps = array();

	$action   = optional_param('action', '');
    if ($action == 'excel') {
        print_excel_header('rayon_'.$rid.'_'.$nm);
		create_excel_workbook();
	    foreach($rkps as $rkp)	{
			// print_excel_form('rkp_prr_ro', $datefrom);
			print_excel_form($rkp, $datefrom, 'rayon', $rid);
		}
		close_excel_workbook();
        exit();
	}


    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
    $strreports = get_string('reportrayon', 'block_monitoring');


    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	$breadcrumbs .= " -> $strrayon";
    print_header_mou("$site->shortname: $strrayon", $site->fullname, $breadcrumbs);

    // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);

    if ($admin_is  || $region_operator_is) {  // || $rayon_operator_is)  {
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		listbox_rayons("listrayonforms.php?yid=$yid&amp;rid=", $rid);
		echo '</table>';
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

    $curryearid = get_current_edu_year_id();

    if ($yid != 0)	{
    	$eduyear = get_record('monit_years', 'id', $yid);
    } else {
    	$yid = 1;
    	$eduyear = get_record('monit_years', 'id', $yid);
    }

    $str1 = $strreports.': '.$rayon->name . get_string('zauchyear', 'block_monitoring', $eduyear->name);
	print_heading($str1, "center", 3);

	print_tabs_years($yid, "listrayonforms.php?rid=$rid&amp;yid=");

	print_tabs_typeforms('rayon', 'monthreport', $nm, $yid, $rid);

	print_tabs_all_months($nm, "listrayonforms.php?rid=$rid&amp;yid=$yid&amp;nm=");
/*
	print_tabs_quarters(1, '#');
    print_tabs_months(1, 1, '#');
*/

    $strstatus = get_string('status', 'block_monitoring');
	$strtable = get_string('table','block_monitoring');
 	$strperiod = get_string('period','block_monitoring');
	$straction = get_string("action","block_monitoring");

    $table->head  = array ($strstatus, $strtable, $straction);
    $table->align = array ("center", "center",  "center");
	$table->width = '60%';
    $table->size = array ('7%', '10%', '5%');
    $table->class = 'feedbackbox';

    if (!empty($rkps)) {
      foreach($rkps as $rkp)	{
	    $strsql = "SELECT * FROM {$CFG->prefix}monit_rayon_listforms
 		   		   WHERE (rayonid=$rid) and (shortname='$rkp') and (datemodified=$datefrom)";

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

		if ($currstatus < 4 || ($admin_is  || $region_operator_is))  {
            if ($curryearid == $yid)	{
		 		$links['edit']->url = "htmlrayonforms.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;fid=";
	 			$links['edit']->title = get_string('editschool','block_monitoring');
		 		$links['edit']->pixpath = "{$CFG->pixpath}/i/edit.gif";
			}
	 	}

		if ($currstatus != 1 && $currstatus < 4 || ($admin_is  || $region_operator_is))  {
	 		$links['status4']->url = "changestatus.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status=4&amp;fid=";
	 		$links['status4']->title = get_string('sendtocoordination', 'block_monitoring');
	 		$links['status4']->pixpath = "{$CFG->pixpath}/s/yes.gif";
        }

		if ($admin_is  || $region_operator_is) {
	 		$links['status6']->url = "changestatus.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status6=6&amp;fid=";
	 		$links['status6']->title = get_string('status6', 'block_monitoring');
	 		$links['status6']->pixpath = "{$CFG->pixpath}/i/tick_green_big.gif";

	 		$links['status3']->url = "changestatus.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status3=3&amp;fid=";
	 		$links['status3']->title = get_string('status3', 'block_monitoring');
	 		$links['status3']->pixpath = "{$CFG->pixpath}/i/return.gif";

	 		$links['status5']->url = "changestatus.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status5=5&amp;fid=";
	 		$links['status5']->title = get_string('status5', 'block_monitoring');
	 		$links['status5']->pixpath = "{$CFG->wwwroot}/blocks/monitoring/i/archive.gif";
	 	}

 		$links['excel']->url = "../school/to_excel.php?level=rayon&amp;rid=$rid&amp;sid=0&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;action=excel&amp;fid=";
 		$links['excel']->title = get_string('downloadexcel');
 		$links['excel']->pixpath = "{$CFG->pixpath}/f/xlsx.gif";

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
	  }
   	  print_color_table($table);
   	} else {
   		notify(get_string('reportsnotfound', 'block_monitoring'));
   	}  

?>
<form name="indices" method="post" target=blank action="../indices/indices.php">
<input type="hidden" name="rid" value="<?php echo $rid ?>" />
<input type="hidden" name="sid" value="<?php echo $sid ?>" />
<input type="hidden" name="yid" value="<?php echo $yid ?>" />
<input type="hidden" name="level" value="rayon" />
<input type="hidden" name="report" value="14" />
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
	    $options['fid'] = $fid;
	    $options['yid'] = $yid;
	    $options['nm'] = $nm;
	   	$options['sesskey'] = $USER->sesskey;
	    print_single_button("listrayonforms.php", $options, get_string("downloadexcel"));
     ?>
    </td>
</tr>
</table>
<?php
    print_footer();

?>


