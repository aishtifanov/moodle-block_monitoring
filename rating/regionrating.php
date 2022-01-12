<?php  // $Id: regionrating.php,v 1.5 2012/10/18 10:40:41 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
    require_once('regionrating_form.php');
	require_once($CFG->libdir.'/formslib.php');    
    require_once('lib_rating.php');
    
	define("NUMBER_OF_STATUS", 7);
	
    $rid = optional_param('rid', 0, PARAM_INT);
    $sid = optional_param('sid', 0, PARAM_INT);            // School id

    require_login();

    if (isguest()) {
        error("No guests here!");
    }

	// $nm  = optional_param('nm', date('n'), PARAM_INT);       // Month number
    $yid = optional_param('yid', 0, PARAM_INT);       // Year id
    $nm = 9;    

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }

    init_rating_parameters($yid, $shortname, $select, $order);
    
	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	if (!$admin_is && !$region_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'), "$CFG->wwwroot/login/index.php");
	}

	if (isregionviewoperator() || israyonviewoperator())  {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $strrayons = get_string('rayons', 'block_monitoring');
	$strreportregion = get_string('regionrating', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> $strreportregion";
    print_header_mou("$SITE->shortname: $strreportregion", $SITE->fullname, $breadcrumbs);

	$redirlink = 'regionrating.php?yid='.$yid;
	
    $currenttab = 'reports';
    include('tabs.php');

    $currenttab2 = 'regionrating';
    include('tabs2.php');
	
	// print_tabs_years($yid, "regionrating.php?yid=", true);
    print_tabs_years_rating("regionrating.php?a=0", $rid, $sid, $yid);
	
    $regionrating_form = new regionrating_form('regionrating.php');

	$year = get_record('monit_years', 'id', $yid);
	$ayears = explode("/", $year->name);
	$plugin = 'rating'.$ayears[0];
    

	$arrindicators = array();	
	if ($indicators = get_records('config_plugins', 'plugin', $plugin))	{
	    // echo '<pre>'; print_r($indicators); echo '</pre>';     
		foreach ($indicators as $indicator)	{
			$name = trim($indicator->name);
			$parts = explode('#', $indicator->value);
	        $arrindicators[$name] = trim($parts[0]);
	    }    
	}				
	
    if (!empty($arrindicators)) {
        $regionrating_form->set_data($arrindicators);
    }

	
	if ($regionrating_form->is_cancelled())	{
            redirect($redirlink , '', 0);
    } else if ($data = $regionrating_form->get_data()) 	{
            // print_object($data); echo  '<hr>';
	    	if (!empty($arrindicators))	 {

				foreach ($indicators as $indicator)	{
					$name = trim($indicator->name);
					$parts = explode('#', $indicator->value);
					$value = $data->{$name} . '#'. trim($parts[1]);
					set_field('config_plugins', 'value', $value, 'plugin', $plugin, 'name', $name);
			    }    
	    		
	            redirect($redirlink, get_string('succesavedata', 'block_monitoring'), 0);
		    } 
    } else {
        $regionrating_form->display();
    }

    print_footer();


?>
