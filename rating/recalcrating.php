<?php // $Id: recalcrating.php,v 1.8 2012/12/06 12:30:26 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
    require_once('lib_rating.php');

    $rid = required_param('rid', PARAM_INT);       // Rayon id
    $yid = required_param('yid', PARAM_INT);       // Year id
    $trunc = optional_param('trunc', '');       // Make truncate table
    $del = optional_param('del', '');       // Make delete records in thid year
    $action = optional_param('action', '');       // Action
    
    // $rid=2;
    
	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	if (!$admin_is && !$region_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'), "$CFG->wwwroot/login/index.php");
	}

	if (isregionviewoperator() || israyonviewoperator())  {
        error(get_string('adminaccess', 'block_monitoring'));
	}

	$strtitle = get_string('recalcrating', 'block_monitoring');
    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> $strtitle";
    print_header("$SITE->shortname: $strtitle ", $SITE->fullname, $breadcrumbs);

    ignore_user_abort(false); // see bug report 5352. This should kill this thread as soon as user aborts.
    @set_time_limit(0);
    @ob_implicit_flush(true);
    @ob_end_flush();
	@raise_memory_limit("512M");
 	if (function_exists('apache_child_terminate')) {
	    @apache_child_terminate();
	}    

	if ($rid<=25)	{
		$rayon = get_record_select('monit_rayon', "id = $rid", 'id, name');
		print_heading($rayon->name, 'center', 3);
	} else {
		print_heading(get_string('finishrecalcrating', 'block_monitoring'), 'center', 3);
		print_footer();
		exit(1);
	}	
	
    if ($action == 'exclude')   {
        if ($yid >= NEW_CRITERIA_YEARID)  {
            $exclude = "AND number not in ('П-23', 'П-58', 'П-103')"; 
            recalculate_rating_update($rid,$yid-1, $exclude);
            recalculate_rating_update($rid, $yid, $exclude);
        }
    }	else {
        if ($yid < NEW_CRITERIA_YEARID)  {
            recalculate_rating_with_truncate($rid, $trunc);
        } else {
            recalculate_rating_update($rid,$yid-1);
            recalculate_rating_update($rid, $yid);
        }
    }        

    print_footer();
    
    $rid++;
    if ($rid<=26)	{
    	redirect("recalcrating.php?yid=$yid&rid=$rid&action=$action", '', 5);
    }
    	


	
    
function recalculate_rating_with_truncate($rid, $trunc)
{
    global $CFG;
        
    if ($trunc == 'yes')	{
		// $db->Execute('TRUNCATE TABLE mdl_monit_rating_school');
		// $db->Execute('TRUNCATE TABLE mdl_monit_rating_total');
		// echo 'TRUNCATE TABLE mdl_monit_rating_school<br>';
		// echo 'TRUNCATE TABLE mdl_monit_rating_total<br>';
	}	



   	$years = get_records('monit_years');

	$strsql = "SELECT id, number, formula, edizm, indicator FROM {$CFG->prefix}monit_rating_criteria
    		   WHERE number LIKE '1%' AND edizm <> 'null'
			   ORDER BY number";
	$criterias[1] = get_records_sql($strsql);

	$strsql = "SELECT id, number, formula, edizm, indicator FROM {$CFG->prefix}monit_rating_criteria
    		   WHERE number LIKE '2%' AND edizm <> 'null'
			   ORDER BY number";
	$criterias[2] = get_records_sql($strsql);
   	
   	foreach ($years as $year)	{
   		
   		$yid = $year->id;
   		$nm = 9;
   		$datefrom = get_date_from_month_year($nm, $yid);
   		
		init_region_criteria($yid);
	
		print_r($REGIONCRITERIA); echo '<hr>'; 
	
		$itogmark = 0;
        
		$shortname[1] = 'rating_1';
		$shortname[2] = 'rating_2';
        for ($i=1; $i<=2; $i++) {
            $shortname = $shortnames[$i];
            $criteria  = $criterias[$i];
    	    $strsql = "SELECT id, rayonid, schoolid, shortname FROM mdl_monit_rating_listforms
    				   where rayonid=$rid AND shortname = '$shortname' AND (datemodified=$datefrom)";
    	   	// print $strsql; echo '<hr>';
    	    if ($listforms = get_records_sql($strsql))	{
    	    	foreach ($listforms as $listform)	{
    			   $totalmark = calculate_school_mark($yid, $listform->rayonid, $listform->schoolid, 
    			   									   $listform->id, $listform->shortname, $criteria);
                   update_rating_total($yid, $rid, $sid, $shortname, $totalmark);                                                   
    			   echo $totalmark.'<hr>';
    			}
    		}	   
        }    
	}
}    	 


function recalculate_rating_update($rid, $yid, $exclude='')
{
    global $CFG;
    
   	$itogmark = 0;
	$nm = 9;
	$datefrom = get_date_from_month_year($nm, $yid);
    // $shortnames = array('rating_n', 'rating_o', 'rating_s', 'rating_k');
    $shortnames = get_listnameforms($yid, 'school');
    $criterias = array();
    foreach ($shortnames as $i => $shortname) {
        $select = '';
        init_rating_parameters($yid, $shortname, $select, $order, '');
   
        $select .=  " AND edizm <> 'null'";
        $strsql = "SELECT id, number, formula, edizm, indicator, ordering FROM {$CFG->prefix}monit_rating_criteria
        		   WHERE $select $exclude
    			   ORDER BY $order";
    	$criterias[$i] = get_records_sql($strsql);
        // echo $i . $strsql . '<br>';
        // print_object($criterias[$i]);
    }
    // print_object($criterias);
    
    foreach ($shortnames as $i => $shortname) {
        $criteria  = $criterias[$i];
	    $strsql = "SELECT id, rayonid, schoolid, shortname FROM mdl_monit_rating_listforms
				   where (rayonid=$rid) AND (shortname = '$shortname') AND (datemodified=$datefrom)";
	   	// echo $strsql; echo '<hr>';
        print_string('name_'.$shortname, 'block_monitoring'); echo '<hr>';
        $totalmark = 0;
        // echo $shortname; echo '<hr>';
	    if ($listforms = get_records_sql($strsql))	{
	    	foreach ($listforms as $listform)	{
              	$arr_df = array();
                $strsql = "SELECT * FROM {$CFG->prefix}monit_form_$shortname WHERE listformid=$listform->id";
                // echo $strsql . '<br>'; 
	            if ($df = get_record_sql($strsql))	{
		              $arr_df = (array)$df;
		              // print_r($arr_df); echo '<hr>';   	
	            }
               $totalmark = calculating_rating_school($yid, $listform->rayonid, $listform->schoolid, 
			   									      $shortname, $arr_df, $criteria);
               update_rating_total($yid, $listform->rayonid, $listform->schoolid, $shortname, $totalmark, $exclude);                                                   
			   echo $totalmark.'<hr>';
			}
		}	   
    }    
}    	 

?>