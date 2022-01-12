<?php // $Id: recalcdynamic.php,v 1.3 2012/12/06 12:30:25 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
    require_once('lib_rating.php');

    $rid = required_param('rid', PARAM_INT);       // Rayon id
    $yid = required_param('yid', PARAM_INT);       // Year id
    $nm = 9;
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
        $tablename = 'monit_rating_total_ex';
    } else {
        $tablename = 'monit_rating_total';
    }    
    
    $curryearid = get_current_edu_year_id();
    if ($yid == $curryearid)  {
       // $shortnames = array('rating_n', 'rating_o', 'rating_s', 'rating_k');
       // $shortnames = array('rating_9_n', 'rating_9_o', 'rating_9_s', 'rating_9_k');
       $shortnames = get_listnameforms($yid, 'school');
       foreach ($shortnames as $shortname) {
            if ($curryearid == 9)   {
                calculate_prev_year_with_new_criteria($rid, $shortname, $yid, $tablename);
                recalculate_dynamic_2015($rid, $shortname, $yid, $tablename);
            } else {
                recalculate_dynamic($rid, $shortname, $nm, $yid, $tablename);
            }
            
       }     
    }    

    print_footer();
    
    $rid++;
    if ($rid<=26)	{
    	redirect("recalcdynamic.php?yid=$yid&rid=$rid&action=$action", '', 1);
    }

	
    
function recalculate_dynamic($rid, $shortname, $nm, $yid, $tablename)
{
    global $CFG, $shortnames;

    // $shortnames = array('rating_n', 'rating_o', 'rating_s', 'rating_k');
	$datefrom = get_date_from_month_year($nm, $yid);
	// $curryid = get_current_edu_year_id();
    $prevyid = $yid - 1;
    $curryid = $yid;
    $schoolsids = array();
    $schoolsname = array();
    $schoolsmark = array();

    for ($y = $prevyid; $y <= $curryid; $y++)   {
        
        $strsql =  "SELECT r.id as sid, r.rayonid, schoolid, rating_n, rating_o, rating_s, rating_k, s.uniqueconstcode
                    FROM {$CFG->prefix}{$tablename} r INNER JOIN mdl_monit_school s on s.id=r.schoolid
                    where r.rayonid=$rid AND r.yearid=$y and s.isclosing=0
                    order by s.uniqueconstcode, r.schoolid";
        // echo $strsql;             	
    	if ($schools = get_records_sql($strsql))	{
            $schoolsname[$y] = array();
            $schoolsmark[$y] = array();
    	    foreach ($schools as $sa)  {
    	        $schoolsids[$y][$sa->uniqueconstcode] = $sa->schoolid;
    	        // $schoolsname[$y][$sa->uniqueconstcode] = $sa->name;
                foreach ($shortnames as $sn) {
    	           $schoolsmark[$y][$sa->uniqueconstcode]->{$sn} = $sa->{$sn};
                } 
                
                // $schoolsmark[$y][$sa->uniqueconstcode]->{$shortname} = $sa->{$shortname};
                  
    	    }
            
            // print_object($schoolsids);
            // print_object($schoolsname);
            // print_object($schoolsmark);
            
        }
    }
    
    foreach ($schoolsids[$curryid] as $uniqueconstcode => $sid) {
        /*
        $sumprev = $schoolsmark[$curryid][$uniqueconstcode]->{$shortname} +  $schoolsmark[$curryid][$uniqueconstcode]->rating_k;
        $sumcurr = $schoolsmark[$prevyid][$uniqueconstcode]->{$shortname} +  $schoolsmark[$prevyid][$uniqueconstcode]->rating_k;
        $dynamic_value = $sumprev - $sumcurr;
        */
        $dynamic_value = $schoolsmark[$curryid][$uniqueconstcode]->{$shortname} - $schoolsmark[$prevyid][$uniqueconstcode]->{$shortname};
        $strsql = "SELECT id FROM {$CFG->prefix}{$tablename}
                   WHERE schoolid=$sid and yearid=$yid";
       	if ($total = get_record_sql($strsql)) {
       	     if ($shortname == 'rating_k')  {
       	        $dynamic_value = correct_dynamic_value_in_criteriaP115($yid, $uniqueconstcode, $dynamic_value, 517);
       	     }   
             set_field($tablename, 'dynamic_'.$shortname, $dynamic_value, 'id', $total->id);
             echo $dynamic_value.'<hr>';
        }      							  	
    }
        
}    	 


function calculate_prev_year_with_new_criteria($rid, $shortname, $yid, $tablename)
{
    global $CFG;
    
    $prevyid = $yid - 1;
    $curryid = $yid;
    
    $a = explode('_', $shortname);
    $gl = end($a);
   
    $sql = "SELECT group_concat(id) as ids FROM mdl_monit_rating_criteria
            where yearid=6 and number in (SELECT number  FROM mdl_monit_rating_criteria
            where yearid=$curryid and gradelevel = '$gl' and formula <> 'null')";
    // echo $sql . '<br>';          
    $oldcriteriaids = get_field_sql($sql);
    
    $strsql =  "SELECT id FROM mdl_monit_school
                where rayonid=$rid AND yearid=$prevyid and isclosing=0
                order by number";
    // echo $strsql . '<br>';             	
   	if ($schools = get_records_sql($strsql))	{
   	    foreach ($schools as $sa)  {
   	        $sql = "SELECT sum(mark) as summark FROM mdl_monit_rating_school
                    where yearid=$prevyid and schoolid=$sa->id and criteriaid in ($oldcriteriaids)";                   
            $summark = get_field_sql($sql);
            
            $strsql = "SELECT id FROM {$CFG->prefix}{$tablename}  WHERE yearid=$prevyid and schoolid=$sa->id";
           	if ($total = get_record_sql($strsql)) {
                 set_field($tablename, $shortname, $summark, 'id', $total->id);
                 echo $summark.'<hr>';
            }      					        
        }
   }     
  
}

function recalculate_dynamic_2015($rid, $shortname, $yid, $tablename)
{
    global $CFG, $shortnames;

    $dynamicnames = array('rating_9_n' => 'dynamic_rating_n', 
    'rating_9_o' => 'dynamic_rating_o',
    'rating_9_s' => 'dynamic_rating_s',
    'rating_9_k' => 'dynamic_rating_k');


    $prevyid = $yid - 1;
    $curryid = $yid;
    $schoolsids = array();
    $schoolsname = array();
    $schoolsmark = array();

    for ($y = $prevyid; $y <= $curryid; $y++)   {
        
        $strsql =  "SELECT rt.id, rt.rayonid, rt.schoolid, rt.rating_9_n, rt.rating_9_o,  rt.rating_9_s, rt.rating_9_k, s.uniqueconstcode
                    FROM {$CFG->prefix}{$tablename} rt INNER JOIN mdl_monit_school s on s.id=rt.schoolid
                    where rt.rayonid=$rid AND rt.yearid=$y and s.isclosing=0
                    order by s.uniqueconstcode, rt.schoolid";
        // echo $strsql;             	
    	if ($schools = get_records_sql($strsql))	{
            $schoolsname[$y] = array();
            $schoolsmark[$y] = array();
    	    foreach ($schools as $sa)  {
    	        $schoolsids[$y][$sa->uniqueconstcode] = $sa->schoolid;
    	        // $schoolsname[$y][$sa->uniqueconstcode] = $sa->name;
                 $schoolsmark[$y][$sa->uniqueconstcode] = new stdClass();
                foreach ($shortnames as $sn) {
    	           $schoolsmark[$y][$sa->uniqueconstcode]->{$sn} = $sa->{$sn};
                } 
                
                // $schoolsmark[$y][$sa->uniqueconstcode]->{$shortname} = $sa->{$shortname};
                  
    	    }
            
            // print_object($schoolsids);
            // print_object($schoolsname);
            // print_object($schoolsmark);
            
        }
    }
    
    foreach ($schoolsids[$curryid] as $uniqueconstcode => $sid) {
        $dynamic_value = $schoolsmark[$curryid][$uniqueconstcode]->{$shortname} - $schoolsmark[$prevyid][$uniqueconstcode]->{$shortname};
        $strsql = "SELECT id FROM {$CFG->prefix}{$tablename} WHERE schoolid=$sid and yearid=$curryid";
       	if ($total = get_record_sql($strsql)) {
             set_field($tablename, $dynamicnames[$shortname], $dynamic_value, 'id', $total->id);
             echo $sid . ': ' . $dynamic_value.'<hr>';
        }      							  	
    }       
}    	 
?>