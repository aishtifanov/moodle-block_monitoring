<?php // $Id: sumreports.php,v 1.5 2012/09/24 11:40:51 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
	require_once('../lib_excel.php');
    // require_once('lib_rating.php');
    
    $rid = optional_param('rid', 0, PARAM_INT);            // Rayon id
    $yid = optional_param('yid', 0, PARAM_INT);       		// Year id
    $fid = optional_param('fid', 0, PARAM_INT);       // Form id
    $nm  = optional_param('nm', date('n'), PARAM_INT);       // Month number
    $levelmonit  = optional_param('level', 'rayon');
	
	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('regionzp');
	$rayon_operator_is  = ismonitoperator('rayonzp', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }
		
	$action   = optional_param('action', '');
    if ($action == 'excel') 	{
    	$rkps = get_listnameforms(); // array('zp_d');
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

    $strschool = get_string('school', 'block_monitoring');
    
    if ($levelmonit == 'rayon') {
	   $strtitle = get_string('summaryreportsrayon', 'block_monitoring');
        $strrayon = get_string('rayon', 'block_monitoring');
        $strrayons = get_string('rayons', 'block_monitoring');
    } else {
       $strtitle = get_string('summaryreportsregion', 'block_monitoring'); 
        $strrayon = get_string('region', 'block_monitoring');
    }   
	
    $strschools = get_string('schools', 'block_monitoring');
    $strreports = get_string('reportschool', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> $strtitle";	
    print_header_mou("$SITE->shortname: $strrayon", $SITE->fullname, $breadcrumbs);

    // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);
    $currenttab = $levelmonit;
    include('tabs.php');

    if ($levelmonit == 'rayon') {
        if ($admin_is  || $region_operator_is) {  // || $rayon_operator_is)  {
    		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
    		listbox_rayons("sumreports.php?rid=", $rid);
    		echo '</table>';
    
    	    if ($rid <= 0) {
    		    print_footer();
    		    exit();
    	    }
    
    		if ($rayon_operator_is && $rayon_operator_is != $rid)  {
    			notify(get_string('selectownrayon', 'block_monitoring'));
    		    print_footer();
    			exit();
    		}
        }
    
    	if ($rid <= 0) {
    	    print_footer();
    	 	exit();
    	}
    
    	if ($rayon_operator_is && $rayon_operator_is != $rid)  {
    		notify(get_string('selectownrayon', 'block_monitoring'));
    	    print_footer();
    		exit();
    	}

        $rayon = get_record('monit_rayon', 'id', $rid);
    	print_heading('Сводные отчеты района: '.$rayon->name, "center", 3);
    }    
    
	print_tabs_years($yid, "sumreports.php?rid=$rid&amp;yid=");

	print_tabs_all_months($nm, "sumreports.php?rid=$rid&amp;yid=$yid&amp;nm=");

	$table = table_sumreports($levelmonit, $rid, $yid, $nm);
   	// print_table($table);
   	print_color_table($table);

	$options = array('action'=> 'excel', 'rid' => $rid, 'yid' => $yid, 
					 'fid' => $fid,  'nm' => $nm,  'sesskey' => $USER->sesskey);
   	echo '<center>';
    print_single_button("listforms.php", $options, get_string('downloadexcel'));
    echo '</center>';

    print_footer();


function table_sumreports($levelmonit, $rid, $yid, $nm)	
{
	global $CFG, $admin_is, $region_operator_is, $rayon_operator_is;

    // $rkps = array('rkp_u', 'rkp_prm_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp', 'bkp_kbo');
    $rkps = array();
    for ($i=1; $i<=11; $i++) {
        $rkps[] = 'svodka'.$i;
    }
    
    $strstatus = get_string('status', 'block_monitoring');
	$strtable = get_string('table','block_monitoring');
	$straction = get_string("action","block_monitoring");

    $table->head  = array ($strstatus, $strtable, $straction);
    $table->align = array ("center", "left", "center");
	$table->width = '60%';
    $table->size = array ('15%', '80%', '5%');
	$table->class = 'moutable';

	$links = array();

	$strformrkpu_status = get_string('summaryreport', 'block_monitoring');
	$strcolor = get_string('status5color', 'block_monitoring');
    $mm = get_string('mm_'.$nm,  'block_monitoring');
    foreach($rkps as $rkp)	{

        if ($levelmonit == 'rayon') {
 		     $links['view']->url = "monthreports.php?rid=$rid&nm=$nm&yid=$yid&sn=$rkp&action=excel";
        } else {
             $links['view']->url = "regionreports.php?nm=$nm&yid=$yid&sn=$rkp&action=excel";
        }     
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

        if ($levelmonit == 'rayon') {
		      $strformrkpu = "<b><a href=monthreports.php?rid=$rid&nm=$nm&yid=$yid&sn=$rkp>" . get_string($rkp, "block_monitoring", $mm) . '</a></b>';
        } else {
              // $strformrkpu = "<b><a href=regionreports.php?nm=$nm&yid=$yid&sn=$rkp>" . get_string($rkp, "block_monitoring", $mm) . '</a></b>';     
              $strformrkpu = "<b><a href=monthreports.php?rid=-1&nm=$nm&yid=$yid&sn=$rkp>" . get_string($rkp, "block_monitoring", $mm) . '</a></b>';       
        }      

	    $table->data[] = array ($strformrkpu_status, $strformrkpu, $strlinkupdate);
		$table->bgcolor[] = array ($strcolor);
		unset($links);
	   // add_rkp_to_table($table, $strsql, , $links, $school_operator_is);
	}
	
	return $table;
}

?>