<?php // $Id: studyyear.php,v 1.1 2009/09/07 14:22:18 Shtifanov Exp $

	require_once("../../../config.php");
    require_once('../../monitoring/lib.php');

	$lastyid = optional_param('yid', 0, PARAM_INT);       // Year id
	$action = optional_param('action', '-');
	$lastyear = 0;

    $strtitle = get_string('studyyears', 'block_mou_school');

	$breadcrumbs = '<a href="'.$CFG->wwwroot."/blocks/mou_school/index.php?rid=1&amp;yid=0&amp;sid=0\">".get_string('title','block_mou_school').'</a>';
	$breadcrumbs .= "-> $strtitle";
    print_header_mou("$SITE->shortname: $strtitle", $SITE->fullname, $breadcrumbs);

    print_heading($strtitle);

	$admin_is = isadmin();

	if ($admin_is)	 {
		
		if ($action == 'create')	{
			if ($lastyid != 0)	{
				$strcurryear = current_edu_year();
				if ($year = get_record('monit_years', 'name', $strcurryear)) {
					error('New year already was created.', 'studyyear.php');
				} else {
					$rec->name = $strcurryear;
					$rec->datestart = date("Y") . '-09-01'; 
					$rec->dateend = date("Y")+1 . '-09-01';
					if ($yid = insert_record('monit_years', $rec))	{
						notify("New year add: {$rec->name}", 'green', 'center');
						// create schools
						$schoolsids = array();
						$schools = get_records('monit_school', 'yearid', $lastyid);
						foreach($schools as $school)	{
							if ($school->isclosing == false)	{
							    $school->yearid = $yid;
							    $newschool = addslashes_object($school);
								if ($newid = insert_record('monit_school', $newschool))	{
									$schoolsids[$school->id] = $newid;  
						    	    // notify("School add: {$school->uniqueconstcode}", 'green', 'left');
						    	}
						    }
						}
						notify("Schools added");
						
						unset($schools);
						$staffs = get_records_sql("SELECT id, schoolid FROM mdl_monit_staff 
												  WHERE schoolid <> 0");
						foreach($staffs as $staff)	{
							if (isset($schoolsids[$staff->schoolid]))	{
								set_field('monit_staff', 'schoolid', $schoolsids[$staff->schoolid], 'id', $staff->id);
							}
						}
						unset($staffs);						  
						notify('Staff school changed.');

						$accreds = get_records_sql("SELECT DISTINCT schoolid FROM mdl_monit_accreditation");
						if ($accreds) {
							foreach ($accreds as $accred)	{
								$db->Execute('UPDATE mdl_monit_accreditation SET schoolid = '. $schoolsids[$accred->schoolid] . ' WHERE schoolid = ' . $accred->schoolid);
							}
						}
						notify('Accreditation school changed.');

						
						$colleges = get_records('monit_college', 'yearid', $lastyid);
						$collegesids = array();
						foreach($colleges as $college)	{
							if ($college->isclosing == false)	{
							    $college->yearid = $yid;
							    $newcollege = addslashes_object($college);
								if ($newid = insert_record('monit_college', $newcollege))	{
									$collegesids[$college->id] = $newid;
						    	    // notify("College add: {$college->uniqueconstcode}", 'green', 'left');
						    	}
						    }
						}
						unset($colleges);
						notify("College added");
						
						$staffs = get_records_sql("SELECT id, collegeid FROM mdl_monit_staff 
												  WHERE collegeid <> 0");
						foreach($staffs as $staff)	{
							if (isset($collegesids[$staff->collegeid]))	{
								set_field('monit_staff', 'collegeid', $collegesids[$staff->collegeid], 'id', $staff->id);
							}
						}
						unset($staffs);						  
						notify('Staff college changed.');


						$udods = get_records('monit_udod', 'yearid', $lastyid);
						$udodsids = array();
						foreach($udods as $udod)	{
							if ($udod->isclosing == false)	{
							    $udod->yearid = $yid;
							    $newudod = addslashes_object($udod);
								if (insert_record('monit_udod', $newudod))	{
									$udodsids[$udod->id] = $newid;
						    	    // notify("UDOD add: {$udod->uniqueconstcode}", 'green', 'left');
						    	}
						    }
						}
						notify("UDODs added");
						unset($udods);
						$staffs = get_records_sql("SELECT id, udodid FROM mdl_monit_staff 
												  WHERE udodid <> 0");
						foreach($staffs as $staff)	{
							if (isset($udodsids[$staff->udodid]))	{
								set_field('monit_staff', 'udodid', $udodids[$staff->udodid], 'id', $staff->id);
							}
						}
						unset($staffs);						  
						notify('Staff udod changed.');
					}
				}
			}
		}
		$years = get_records('monit_years');
		
		if ($years )	{
			$table->head  = array (	get_string('name', 'block_mou_school'), get_string('timestart', 'block_mou_school'),
									get_string('timeend', 'block_mou_school'), get_string('action', 'block_mou_school'));
		    $table->align = array ("center", "center", "center", "center");
	 	    $table->size = array('10%', '10%', '10%', '5%');
		   	$table->width = '60%';
	        $table->class = 'moutable';
	       	// $table->align = array ("left", "left", "left");
	       	
			
			foreach ($years as $year) {
					$lastyear = $year->id;
					$title = get_string('editstudyear','block_mou_school');
					$strlinkupdate = "<a title=\"$title\" href=\"editstudyear.php?mode=edit&amp;id={$year->id}\">";
					$strlinkupdate = $strlinkupdate . "<img src=\"{$CFG->pixpath}/i/edit.gif\" alt=\"$title\" /></a>&nbsp;";
					$strdiscipline = $year->name;
					$table->data[] = array ($strdiscipline, convert_date($year->datestart, 'en', 'ru'),
											convert_date($year->dateend, 'en', 'ru'), $strlinkupdate);
			}
			print_color_table($table);
		}	else {
			notify(get_string('notfoundholiday', 'block_mou_school'));
		}
	}	

	$options = array('yid' => $lastyear, 'action' => 'create');
	echo '<table align="center" border=0><tr><td>';
    print_single_button("studyyear.php", $options, get_string('createnewyear','block_mou_school'));
	echo '</td></tr></table>';

	$options = array('yid' => $lastyear, 'action' => 'create');
	echo '<table align="center" border=0><tr><td>';
    print_single_button("studyyearclass.php", $options, get_string('createnewyearforclasses','block_mou_school'));
	echo '</td></tr></table>';
		

    print_footer();

?>


