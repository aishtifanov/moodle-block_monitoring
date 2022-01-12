<?php // $Id: setnumbers.php,v 1.1 2010/01/20 10:44:37 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $rid = required_param('rid', PARAM_INT);          // Rayon id
    $sid = optional_param('sid', '0', PARAM_INT);       // School id
    $yid = optional_param('yid', '0', PARAM_INT);       // Year id
	$action   = optional_param('action', '');

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
	$strschools = get_string('schools', 'block_monitoring');
	
    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	$breadcrumbs .= " -> $strschools";
    print_header_mou("$SITE->shortname: $strschools", $SITE->fullname, $breadcrumbs);

    $curryearid = get_current_edu_year_id();
    if ($yid == 0)	{
    	$yid = $curryearid;
    }

	// $arr_schools =  get_records('monit_school', 'rayonid', $rayon->id, 'isclosing', false);
	$arr_schools =  get_records_sql("SELECT id, number FROM {$CFG->prefix}monit_school
				     				WHERE rayonid = $rid AND isclosing=0 AND yearid=$yid
				     				ORDER BY number");
	$num = 1; 			     				
	foreach ($arr_schools as $school) {
		set_field('monit_school', 'number', $num, 'id', $school->id);
		$num++;
	}	 
 
	redirect ("schools.php?rid=$rid&sid=$sid&yid=$yid", '', 0); 
	print_footer();
?>


