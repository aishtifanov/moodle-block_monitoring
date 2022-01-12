<?PHP // $Id: delrayon.php,v 1.7 2009/02/25 08:23:50 Shtifanov Exp $

//  Lists all the sessions for a course
    require_once("../../../config.php");
    require_once('../lib.php');

    $rid     = required_param('rid', PARAM_INT);                 // Rayon id
	$confirm = optional_param('confirm');

    if (!$site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	if (!$admin_is && !$region_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

	if (isregionviewoperator())  {
        error(get_string('adminaccess', 'block_monitoring'));
	}


    $strrayons = get_string('rayons', 'block_monitoring');
    $strdelrayon = get_string('deletingrayon','block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	$breadcrumbs .= " -> $strdelrayon";
    print_header_mou("$site->shortname: $strdelrayon", $site->fullname, $breadcrumbs);


	if (!$rayon = get_record('monit_rayon', 'id', $rid)) {
        error(get_string('errorrayon', 'block_monitoring'), '..\rayon\rayons.php');
	}

	if (isset($confirm)) {
		$countschool = count_records('monit_school', 'rayonid', $rid);
		if ($countschool == 0)  {
			delete_records('monit_rayon', 'id', $rid);
			// add_to_log(1, 'dean', 'Faculty deleted', 'delfaculty.php', $USER->lastname.' '.$USER->firstname);
		}
		else 	{
			error(get_string('errorindelrayon','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/rayon/rayons.php");
		}
		redirect('rayons.php', get_string('rayondeleted','block_monitoring'), 3);
	}


	print_heading($strdelrayon.' :: ' .$rayon->name);

    $s1 = get_string('deletecheckfull', 'block_monitoring', ' районе &laquo;'. $rayon->name . '&raquo;');

	notice_yesno($s1, "delrayon.php?rid=$rid&amp;confirm=1", "rayons.php");

	print_footer();
?>
