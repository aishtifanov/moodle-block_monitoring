<?php // $Id: listforms.php,v 1.16 2012/11/14 10:58:53 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
	require_once('../lib_excel.php');
    require_once('lib_rating.php');
    
    $rid = required_param('rid', PARAM_INT);            // Rayon id
    $sid = optional_param('sid', 0, PARAM_INT);            // School id
    $yid = optional_param('yid', 0, PARAM_INT);       		// Year id
    $fid = optional_param('fid', 0, PARAM_INT);       // Form id
    // $nm  = optional_param('nm', 9, PARAM_INT);  // Month number
    $nm = 9;
	
	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('school', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $curryearid = get_current_edu_year_id();
    if ($yid != 0)	{
    	$eduyear = get_record('monit_years', 'id', $yid);
    } else {
    	$yid = $curryearid;
    	$eduyear = get_record('monit_years', 'id', $yid);
    }

		
	$action   = optional_param('action', '');
    if ($action == 'excel') 	{
        /*
        if ($yid < NEW_CRITERIA_YEARID)   {
    	   $rkps = array('rating_1' , 'rating_2');
        } else {   
           $rkps = array('rating_n', 'rating_o', 'rating_s', 'rating_k');
        } 
        */   
        $rkps = get_listnameforms($yid, 'school'); // array('zp_d');
        
        $datefrom = get_date_from_month_year($nm, $yid);
        print_excel_header('rating_'.$sid.'_'.$nm.'_all');
		create_excel_workbook();
	    foreach($rkps as $rkp)	{
			// print_excel_form('rkp_prr_ro', $datefrom);
			print_excel_form($rkp, $datefrom, 'rating', $rid, $sid, $yid);
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

	$strtitle = get_string('begindata', 'block_monitoring');
	
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
	$breadcrumbs .= " -> $strtitle";	
    print_header_mou("$SITE->shortname: $strschool", $SITE->fullname, $breadcrumbs);

    // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);
    $currenttab = 'listforms';
    include('tabs.php');
    

	if ($admin_is  || $region_operator_is || $rayon_operator_is) {
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		listbox_rayons("listforms.php?sid=0&amp;yid=$yid&amp;rid=", $rid);
		// listbox_schools_lastyear("listforms.php?rid=$rid&amp;yid=$yid&amp;sid=", $rid, $sid, $yid);
        listbox_schools("listforms.php?rid=$rid&amp;yid=$yid&amp;sid=", $rid, $sid, $yid);
		echo '</table>';
	}

	if ($rid == 0 ||  $sid == 0) {
		/*if ($admin_is  || $region_operator_is)	{
			echo '<br><br><center><a href="'. $CFG->wwwroot .'/blocks/monitoring/region/regionrating.php?yid=' . $yid . '">' . get_string('regionrating', 'block_monitoring') . '</a></center>'; 
		}*/
	    print_footer();
	 	exit();
	}

	if ($rayon_operator_is && $rayon_operator_is != $rid)  {
		notify(get_string('selectownrayon', 'block_monitoring'));
	    print_footer();
		exit();
	}


	$REGIONCRITERIA = new stdClass();
	init_region_criteria($yid);
	$timedenied = time();
	if (!$admin_is && !$region_operator_is)	{ 
		if ($timedenied > $REGIONCRITERIA->timeaccessdenied)	{  
			//    $str1 = $strreports.': '.$school->name . get_string('zauchyear', 'block_monitoring', $eduyear->name);
			notice(get_string('accessdenied', 'block_monitoring'), $CFG->wwwroot.'/blocks/monitoring/index.php');
		}
	}	


    print_tabs_years_rating("listforms.php?nm=$nm", $rid, $sid, $yid);    

	$table = table_begindata($rid, $sid, $yid, $nm);
   	// print_table($table);
   	print_color_table($table);

	$options = array('action'=> 'excel', 'rid' => $rid, 'sid' => $sid, 'yid' => $yid, 
					 'fid' => $fid,  'nm' => $nm,  'sesskey' => $USER->sesskey);
   	echo '<center>';
    print_single_button("listforms.php", $options, get_string('downloadexcel'));
    echo '</center>';

	// print_string('remarkyear', 'block_monitoring');
/*	if ($admin_is  || $region_operator_is)	{
		echo '<br><br><center><a href="'. $CFG->wwwroot .'/blocks/monitoring/region/regionrating.php?yid=' . $yid . '">' . get_string('regionrating', 'block_monitoring') . '</a></center>'; 
	}
*/	
    if ($yid >= NEW_CRITERIA_YEARID)  {
        $strtimeclose = date('d.m.Y\г\. \в H:i', $REGIONCRITERIA->timeaccessdenied);
    	notify("<b><i>Внимание! $strtimeclose доступ к исходным данным системы рейтингования ОУ будет закрыт!</i></b>");
    }     
    print_footer();


function table_begindata($rid, $sid, $yid, $nm)	
{
	global $CFG, $admin_is, $region_operator_is, $rayon_operator_is;
    
    /*
    if ($yid < NEW_CRITERIA_YEARID)   {
	   $rkps = array('rating_1', 'rating_2');
    }  else {   
       $rkps = array('rating_n', 'rating_o', 'rating_s', 'rating_k');
    } 
    */
    $rkps = get_listnameforms($yid, 'school'); // array('zp_d');       
	
	$datefrom = get_date_from_month_year($nm, $yid);
	
    $strstatus = get_string('status', 'block_monitoring');
    // $strname = get_string('territory', 'block_monitoring');
	$strtable = get_string('table','block_monitoring');
 	$strperiod = get_string('period','block_monitoring');
	$straction = get_string("action","block_monitoring");

    $table = new stdClass();
    $table->head  = array ($strstatus, $strtable, $straction);
    $table->align = array ("center", "center", "center");
	$table->width = '60%';
    $table->size = array ('10%', '30%', '5%');
	$table->class = 'moutable';

    foreach($rkps as $rkp)	{
		$links = array();
        
        $razdel = get_record_select('monit_razdel', "shortname = '$rkp'", 'id, name');

	    $strsql = "SELECT * FROM {$CFG->prefix}monit_rating_listforms
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
			//$strformrkpu = $rec->shortrusname;
			$strformrkpu = $razdel->name;// get_string('name_'.$rkp, "block_monitoring");
			$currstatus = $rec->status;
		} else {
	      	$fid = 0;
	    	$strformrkpu_status = get_string("status1","block_monitoring");
	    	$strcolor = get_string("status1color","block_monitoring");
			$strformrkpu = $razdel->name;// get_string('name_'.$rkp, "block_monitoring");
			$currstatus = 1;
	    }

		if ($currstatus < 4 || ($admin_is  || $region_operator_is || $rayon_operator_is))  {       //
           // if ($curryearid == $yid || $admin_is)	{
                $links['edit'] = new stdClass();
		 		$links['edit']->url = "htmlforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;fid=";
	 			$links['edit']->title = get_string('editschool','block_monitoring');
		 		$links['edit']->pixpath = "{$CFG->pixpath}/i/edit.gif";
	 		// }
	 	}

		if ($currstatus != 1 && $currstatus < 4)  {
		    $links['status4'] = new stdClass();
	 		$links['status4']->url = "changestatus.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status=4&amp;fid=";
	 		$links['status4']->title = get_string('sendtocoordination', 'block_monitoring');
	 		$links['status4']->pixpath = "{$CFG->pixpath}/s/yes.gif";
        }

		if ($currstatus > 1 && ($admin_is  || $region_operator_is)) { //  || $rayon_operator_is)) {
		    $links['status6'] = new stdClass();
	 		$links['status6']->url = "changestatus.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status6=6&amp;fid=";
	 		$links['status6']->title = get_string('status6', 'block_monitoring');
	 		$links['status6']->pixpath = "{$CFG->pixpath}/i/tick_green_big.gif";

            $links['status3'] = new stdClass();
	 		$links['status3']->url = "changestatus.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status3=3&amp;fid=";
	 		$links['status3']->title = get_string('status3', 'block_monitoring');
	 		$links['status3']->pixpath = "{$CFG->pixpath}/i/return.gif";
	 	}

		if ($currstatus >= 6 && ($admin_is  || $region_operator_is)) {
		    $links['status5'] = new stdClass();
	 		$links['status5']->url = "changestatus.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status5=5&amp;fid=";
	 		$links['status5']->title = get_string('status5', 'block_monitoring');
	 		$links['status5']->pixpath = "{$CFG->wwwroot}/blocks/monitoring/i/archive.gif";
	 	}

        $links['excel'] = new stdClass();
 		$links['excel']->url = "../school/to_excel.php?level=rating&amp;rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;action=excel&amp;fid=";
 		$links['excel']->title = get_string('downloadexcel');
 		$links['excel']->pixpath = "{$CFG->pixpath}/f/xlsx.gif";

        if ($currstatus >= 6 && $rayon_operator_is && !$admin_is  && !$region_operator_is)  {
        	unset($links);
	 		$links['excel']->url = "../school/to_excel.php?level=rating&amp;rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;action=excel&amp;fid=";
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
	
	return $table;
}

?>