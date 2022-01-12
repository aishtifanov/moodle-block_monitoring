<?php // $Id: include_bkp.php

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $rayon = get_record('monit_rayon', 'id', $rid);

    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
    $strrep = get_string('reports', 'block_monitoring');
    $strreports = get_string('integralreport', 'block_monitoring');
    $strsumreports = get_string('sumreportsrayon', 'block_monitoring');
	$strformname = get_string('name_'.$shortname,'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/sumreports.php?rid=$rid&amp;yid=$yid\">$strsumreports</a>";
	$breadcrumbs .= " -> $strformname";
    print_header_mou("$site->shortname: $strformname", $site->fullname, $breadcrumbs);

	if ($rid == 0) {
	    print_footer();
	 	exit();
	}

	$datefromcurr = get_date_from_month_year($nm, $yid);
	$formslist = '';

	$strsql = "SELECT *  FROM {$CFG->prefix}monit_school
			   WHERE rayonid = $rid AND isclosing=0 AND yearid=$yid
			   ORDER BY number";
 	if ($schools = get_records_sql($strsql))	{

        $schoolsarray = array();
	    foreach ($schools as $sa)  {
	        $schoolsarray[] = $sa->id;
	    }
	    $schoolslist = implode(',', $schoolsarray);

		$strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
		 		   WHERE (schoolid in ($schoolslist)) and (shortname='$shortname') and (datemodified=$datefromcurr)";
	    if ($listforms = get_records_sql($strsql)) 	{
	        $formsarray = array();
		    foreach ($listforms as $lf)  {
		        $formsarray[] = $lf->id;
		    }
		    $formslist = implode(',', $formsarray);
		}
	}

    $strnamemonth = get_string('nm_'.$nm, 'block_monitoring');
	print_heading($strreports.': '.$rayon->name, "center", 3);
	print_heading($strformname.': '.$strnamemonth, "center", 4);

    print_simple_box_start('center');
?>
