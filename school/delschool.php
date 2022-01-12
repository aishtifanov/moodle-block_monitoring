<?PHP // $Id: delschool.php,v 1.12 2009/02/25 08:23:52 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

	define('RECYCLE_BIN', 101);

    $rid     = required_param('rid', PARAM_INT);        // Rayon id
    $sid     = required_param('sid', PARAM_INT);        // School id
    $yid 	 = required_param('yid', PARAM_INT);       		// Year id
	$confirm = optional_param('confirm');

    // $yid = 1;

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	if (!$admin_is && !$region_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

	if (isregionviewoperator() || israyonviewoperator())  {
        error(get_string('adminaccess', 'block_monitoring'));
	}


    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
    $strschool = get_string('school', 'block_monitoring');
    $strschools = get_string('schools', 'block_monitoring');
    $strdelschool = get_string('deletingschool','block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/school/schools.php?rid=$rid&amp;yid=$yid\">$strschools</a>";
	$breadcrumbs .= " -> $strdelschool";
    print_header_mou("$site->shortname: $strdelschool", $site->fullname, $breadcrumbs);


	if (!$school = get_record('monit_school', 'id', $sid)) {	    error(get_string('errorschool', 'block_monitoring'), "schools.php?rid=$rid&amp;yid=$yid");
	}

	if (isset($confirm))  {
		if ($school->isclosing == true)	{			delete_records('monit_school', 'id', $sid);
			// add_to_log(1, 'dean', 'Speciality deleted', 'delspeciality.php', $USER->lastname.' '.$USER->firstname);
			redirect("schools.php?rid=$rid&amp;yid=$yid", get_string('schooldeleted','block_monitoring'), 3);
		} else {			$school->isclosing = true;
			$school->dateclosing = time();
			// $school->dateclosing = make_timestamp(2008, 8, 31, 12);
			if (update_record('monit_school', $school))	{
				 // add_to_log(1, 'dean', 'speciality update', "blocks/dean/speciality/speciality.php?id=$fid", $USER->lastname.' '.$USER->firstname);
				 echo '<div align=center>';
				 notice(get_string('schoolupdate','block_monitoring') . '</center>', "$CFG->wwwroot/blocks/monitoring/school/schools.php?rid=$rid&amp;yid=$yid");
				 echo '</div>';
			} else {
				error(get_string('errorinupdatingschool','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/school/schools.php?rid=$rid&amp;yid=$yid");
			}
		}


/*
	    if ($rayon = get_record('monit_rayon', 'id', $rid))	{	    	if ($rayon->number == RECYCLE_BIN)	{
				delete_records('monit_school', 'id', $sid);
				// add_to_log(1, 'dean', 'Speciality deleted', 'delspeciality.php', $USER->lastname.' '.$USER->firstname);
				redirect("schools.php?rid=$rid", get_string('schooldeleted','block_monitoring'), 3);
			} else {
			    $recycle_rayon = get_record('monit_rayon', 'number', RECYCLE_BIN);
				$school->rayonid = $recycle_rayon->id;
				$school->name = $rayon->name . '. '. $school->name;
				$school->number = 0;				if (update_record('monit_school', $school))	{
					 // add_to_log(1, 'dean', 'speciality update', "blocks/dean/speciality/speciality.php?id=$fid", $USER->lastname.' '.$USER->firstname);
					 echo '<div align=center>';
					 notice(get_string('schoolupdate','block_monitoring') . '</center>', "$CFG->wwwroot/blocks/monitoring/school/schools.php?rid=$rid");
					 echo '</div>';
				} else {
					error(get_string('errorinupdatingschool','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/school/schools.php?rid=$rid");
				}
			}
		}
*/

	}


	print_heading($strdelschool .' :: ' .$school->name);

    $s1 = get_string('deletecheckfull', 'block_monitoring', ' школе &laquo;'. $school->name.'&raquo;');

	notice_yesno($s1, "delschool.php?rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;confirm=1", "schools.php?rid=$rid&amp;yid=$yid");

	print_footer();
?>
