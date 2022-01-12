<?php // $Id: studyyearclass.php,v 1.1 2009/09/10 12:58:30 Shtifanov Exp $

	require_once("../../../config.php");
    require_once('../../monitoring/lib.php');


    $strtitle = get_string('studyyearsclass', 'block_mou_school');

	$breadcrumbs = '<a href="'.$CFG->wwwroot."/blocks/mou_school/index.php?rid=1&amp;yid=0&amp;sid=0\">".get_string('title','block_mou_school').'</a>';
	$breadcrumbs .= "-> $strtitle";
    print_header_mou("$SITE->shortname: $strtitle", $SITE->fullname, $breadcrumbs);

    print_heading($strtitle);

	$admin_is = isadmin();
	if (!$admin_is)	 {
		error('Only admin access this function.', 'studyyear.php');
	}	

	$strcurryear = current_edu_year();
	if (!$year = get_record('monit_years', 'name', $strcurryear)) {	
		error('Current study year not found.', 'studyyear.php');
	}	

	if (record_exists('monit_school_class', 'yearid', $year->id))	{
		error('Class already transfer in new year.', 'studyyear.php');		
	}

	if (!$lastyear = get_record('monit_years', 'id', $year->id - 1)) {
		error('Old year not found.', 'studyyear.php');
	}
		

	$schoolsids = array();
	$schools = get_records('monit_school', 'yearid', $lastyear->id);
	foreach($schools as $school)	{
		$schoolsids[$school->uniqueconstcode]->oldid = $school->id;
	}
	$schools = get_records('monit_school', 'yearid', $year->id);
	foreach($schools as $school)	{
		$schoolsids[$school->uniqueconstcode]->newid = $school->id;
	}
	
	$newschoolsids = array();
	foreach ($schoolsids as $schsid)	{
		$newschoolsids[$schsid->oldid] = $schsid->newid;
	}
	

	$classids = array();
	
	if ($classes = get_records('monit_school_class', 'yearid', $lastyear->id))	{

		foreach ($classes as $class)	{
			// print_r($class); echo '<hr>'; continue;
			$classid = $class->id;
			if ($class->parallelnum < 11)	{
				
				if (isset($newschoolsids[$class->schoolid]) && !empty($newschoolsids[$class->schoolid]))	{
	
					$num = (integer)$class->name;
					if (is_numeric($num))	{
						$contents = preg_replace("|[^а-яА-Я ]|i", NULL, $class->name);
						$newpn = $class->parallelnum + 1;
						$newname = $newpn . $contents;
					} else {
						$newpn 	 = $class->parallelnum;
						$newname = $class->name;
					}
					// echo  "$class->name ==> $newname ($newpn)<br>";
					unset($newclass);				
					$newclass->rayonid = $class->rayonid;
					$newclass->schoolid = $newschoolsids[$class->schoolid];					
					$newclass->yearid = $year->id;
					$newclass->name = $newname;
					$newclass->parallelnum = $newpn;
					$newclass->timecreated = time();
					if ($newid = insert_record('monit_school_class', $newclass))	{
						$classids[$classid] = $newid;
						notify("New class added: $classid -> $newid", 'green', 'center');
						/*
						$newrec = get_record ('monit_school_class', 'yearid', $year->id, 'schoolid', $class->schoolid, 'name', $class->name, 'id');
						$classids[$class->id] = $newrec->id;
						notify("New class added: {$class->id} - $newid", 'blue', 'center');
						*/
						
					}	else	{
						print_r($class);
						error('Error insert monit_school_class.', 'studyyear.php');
					}
				}	
			}		
		}
	}
	 
	// print_r($classids);
	
	if ($pupilcards = get_records('monit_school_pupil_card', 'yearid', $lastyear->id))	{
		foreach ($pupilcards as $pupil)		{
			if (isset($newschoolsids[$pupil->schoolid]) && !empty($newschoolsids[$pupil->schoolid]))	{
				if (isset($classids[$pupil->classid]) && !empty($classids[$pupil->classid]))	{ 
					$pupil->yearid = $year->id;
					$pupil->schoolid = $newschoolsids[$pupil->schoolid];
					$pupil->classid = $classids[$pupil->classid];
					if (insert_record('monit_school_pupil_card', addslashes_object($pupil)))	{
						echo $pupil->id . ' # ' . $pupil->classid . '<br>';
					} else {
						print_r($pupil);
						notify('Error insert monit_school_pupil_card.');
						// error('Error insert monit_school_pupil_card.', 'studyyear.php');
					}
					
				} else {
					echo '!!! ' . $pupil->classid . ' -> ' . $classids[$pupil->classid] . '<br>';
				} 	
			}
		}
		notify("New pupilcardss added", 'green', 'center');
	}

	// redirect('studyyear.php', 'Update complete', 60);	

    print_footer();
    
    // delete FROM `mou`.`mdl_monit_school_class` where yearid=3
    // DELETE FROM `mou`.`mdl_monit_school_pupil_card` where yearid=3
    // ALTER TABLE `mou`.`mdl_monit_school_pupil_card` AUTO_INCREMENT = 25536
    // ALTER TABLE `mou`.`mdl_monit_school_class` AUTO_INCREMENT = 1453

?>


