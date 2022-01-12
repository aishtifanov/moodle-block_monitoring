<?PHP // $Id: delcontract.php,v 1.6 2009/02/25 08:23:49 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $frid = required_param('frid', PARAM_INT);       // Finance report id
    $rid  = required_param('rid', PARAM_INT);                 // Rayon id
    $sid  = required_param('sid', PARAM_INT);                 // School id

    $nm  = optional_param('nm', '1', PARAM_INT);       // Month number
    $levelmonit = optional_param('level', 'region');       // Level num
	$confirm = optional_param('confirm');
    $vkladka = optional_param('vkladka', 'finstatus0');       // Vkladka name

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('school', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

	if (isregionviewoperator() || israyonviewoperator())  {
        error(get_string('adminaccess', 'block_monitoring'));
	}

	$strdelcontract = get_string('deletingcontract','block_monitoring');
    $strlevel = get_string($levelmonit, 'block_monitoring');

	switch ($levelmonit)	{
		case 'region':
					if ($admin_is || $region_operator_is) 	{
					    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
						$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/finance/financereport.php?level=$levelmonit&amp;nm=$nm&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka\">$strlevel</a>";
						$breadcrumbs .= " -> $strdelcontract";
					    print_header_mou("$site->shortname: $strdelcontract", $site->fullname, $breadcrumbs);
					}
		break;

		case 'rayon':
					if ($admin_is || $region_operator_is || $rayon_operator_is) 	{
					    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
						$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
						$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/finance/financereport.php?level=$levelmonit&amp;nm=$nm&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka\">$strlevel</a>";
						$breadcrumbs .= " -> $strdelcontract";
					    print_header_mou("$site->shortname: $strdelcontract", $site->fullname, $breadcrumbs);
					    /*
					    $rayon = get_record('monit_rayon', 'id', $rid);
						print_heading($strformname.': '.$rayon->name, "center", 3);
						*/
					}

		break;
		case 'school':
					    $strrayon = get_string('rayon', 'block_monitoring');
					    $strrayons = get_string('rayons', 'block_monitoring');
					    $strschool = get_string('school', 'block_monitoring');
					    $strschools = get_string('schools', 'block_monitoring');
					    $strreports = get_string('reportschool', 'block_monitoring');
					    $strrep = get_string('reports', 'block_monitoring');

					    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
						if ($admin_is || $region_operator_is || $rayon_operator_is) 	{
							$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
							$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/school/schools.php?rid=$rid&amp;sid=$sid\">$strschools</a>";
						}
						$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/finance/financereport.php?level=$levelmonit&amp;nm=$nm&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka\">$strlevel</a>";
						$breadcrumbs .= " -> $strdelcontract";
					    print_header_mou("$site->shortname: $strdelcontract", $site->fullname, $breadcrumbs);
                        /*
					    $school = get_record('monit_school', 'id', $sid);
						print_heading($strformname.': '.$school->name, "center", 3);
						*/

		break;
    }

	if (isset($confirm)) {
		delete_records('monit_form_rkp_f', 'id', $frid);
		delete_records('monit_form_rkp_f_dir', 'rkp_f_id', $frid);
		delete_records('monit_form_rkp_f_pay', 'rkp_f_id', $frid);
		// add_to_log(1, 'dean', 'Speciality deleted', 'delspeciality.php', $USER->lastname.' '.$USER->firstname);
		redirect("financereport.php?level=$levelmonit&amp;nm=$nm&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka",
				 get_string('contractdeleted','block_monitoring'), 3);
	}

	print_heading($strdelcontract); //  .' :: ' .$school->name);

    $s1 = ' конкурсе/контракте ';

	notice_yesno(get_string('deletecheckfull', 'block_monitoring', $s1),
 				"delcontract.php?level=$levelmonit&amp;frid=$frid&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka&amp;confirm=1",
				"financereport.php?level=$levelmonit&amp;nm=$nm&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka");

	print_footer();
?>
