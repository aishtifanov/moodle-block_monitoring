<?php // $Id: frontpage.php,v 1.15 2008/09/08 09:55:28 Shtifanov Exp $

    require_once('../../config.php');
    require_once('lib.php');

    $yid = required_param('yid', PARAM_INT);          // Year id
   // require_login();


    if (!$site = get_site()) {
        redirect('index.php');
    }

    $strmonit = get_string('frontpagetitle', 'block_monitoring');

    print_header_mou("$site->shortname: $strmonit", $site->fullname, $strmonit);

    print_heading('New educational year.');

	$admin_is = isadmin();

	if ($admin_is)	 {		$schools = get_records('monit_school', 'yearid', $yid-1);

		foreach($schools as $school)	{			$school->uniqueconstcode = $school->id;
			if (update_record('monit_school', $school))	{				notify(get_string('schoolupdate','block_monitoring'), 'green', 'center');			}			if ($school->isclosing == false)	{
			    $school->yearid = $yid;
				if (insert_record('monit_school', $school))	{		    	    notify("School add: {$school->uniqueconstcode}", 'green', 'center');
		    	}
		    }
		}
	}

    print_footer($site);

?>


