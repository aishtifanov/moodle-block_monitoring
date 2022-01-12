<?php // $Id: operators.php,v 1.13 2011/09/21 08:22:28 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
    require_once($CFG->libdir.'/tablelib.php');

    $rid = optional_param('rid', 0, PARAM_INT);          // Rayon id
    $sid = optional_param('sid', 0, PARAM_INT);          // School id
    $levelmonit  = optional_param('level', 'region');
    $all  = optional_param('all', 0, PARAM_INT);
    $yid = optional_param('yid', '0', PARAM_INT);       // Year id
    $uid = optional_param('uid', 0, PARAM_INT);       // User id    
	$action = optional_param('action', '');       // action

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }
/*
	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	if (!$admin_is && !$region_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}
*/
	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}


    if ($sid != 0)	{
    	$school = get_record('monit_school', 'id', $sid);
   	    $strschool = $school->name;
    }	else  {
   	    $strschool = get_string('school', 'block_monitoring');
    }

    $curryearid = get_current_edu_year_id();
    if ($yid == 0)	{
    	$yid = $curryearid;
    }


/*
    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
    $strschools = get_string('schools', 'block_monitoring');
    $strreports = get_string('reportschool', 'block_monitoring');
  */
    $stroperators = get_string('operators', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> $stroperators";
    print_header_mou("$site->shortname: $stroperators", $site->fullname, $breadcrumbs);

    // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);
/*
*/
	if (!$admin_is  && !$region_operator_is && $rayon_operator_is && $rayon_operator_is != $rid)  {
		notify(get_string('selectownrayon', 'block_monitoring'));
	    print_footer();
		exit();
	}

    $currenttab = $levelmonit;
    include('tabsoperators.php');

//if ($rid == 0 ||  $sid == 0) exit();

	switch ($levelmonit)	{
		case 'region':
    				    $studentsql = "SELECT u.id, u.username, u.firstname, u.lastname, u.email, u.maildisplay,
							  u.city, u.country, u.lastlogin, u.picture, u.lang, u.timezone,
                              u.lastaccess, t.regionid, t.editall
                            FROM {$CFG->prefix}user u
                       RIGHT JOIN {$CFG->prefix}monit_operator_region t ON t.userid = u.id
                       WHERE t.regionid=1 AND u.deleted = 0 AND u.confirmed = 1";
					    if ($action == 'ok')	{
					       	  set_field('monit_operator_region', 'editall', 1, 'userid', $uid);
					    } else if ($action == 'break')	{
					       	  set_field('monit_operator_region', 'editall', 0, 'userid', $uid);
					    }
                       
		break;

		case 'rayon':
						if ($admin_is || $region_operator_is)	 {
							echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
							listbox_rayons("operators.php?level=rayon&amp;sid=0&amp;rid=", $rid);
							echo '</table>';
						}

					    if ($action == 'ok')	{
					       	  set_field('monit_operator_rayon', 'editall', 1, 'userid', $uid);
					    } else if ($action == 'break')	{
					       	  set_field('monit_operator_rayon', 'editall', 0, 'userid', $uid);
					    }

				        $studentsql = "SELECT u.id, u.username, u.firstname, u.lastname, u.email, u.maildisplay,
								  u.city, u.country, u.lastlogin, u.picture, u.lang, u.timezone,
	                              u.lastaccess, t.rayonid, t.editall
		                          FROM {$CFG->prefix}user u
			                      RIGHT JOIN {$CFG->prefix}monit_operator_rayon t ON t.userid = u.id ";

                        if ($all) {
	                       $studentsql .= "WHERE u.deleted = 0 AND u.confirmed = 1";
                        } else {
	                       $studentsql .= "WHERE t.rayonid=$rid AND u.deleted = 0 AND u.confirmed = 1";
	                    }
		break;

		case 'school':
						echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
						if ($admin_is || $region_operator_is)	 {
							listbox_rayons("operators.php?level=school&amp;sid=0&amp;rid=", $rid);
						}
						listbox_schools("operators.php?level=school&amp;rid=$rid&amp;yid=$yid&amp;sid=", $rid, $sid, $yid);
						echo '</table>';

				        $studentsql = "SELECT u.id, u.username, u.firstname, u.lastname, u.email, u.maildisplay,
									   u.city, u.country, u.lastlogin, u.picture, u.lang, u.timezone,
  			                           u.lastaccess, t.schoolid
     			                       FROM {$CFG->prefix}user u
        				               RIGHT JOIN {$CFG->prefix}monit_operator_school t ON t.userid = u.id ";

                        if ($all) {
							if ($schools =  get_records_sql("SELECT *  FROM {$CFG->prefix}monit_school
					     									WHERE rayonid = $rid AND isclosing=0 AND yearid=$yid
										     				ORDER BY number"))		{

						        $schoolsarray = array();
							    foreach ($schools as $sa)  {
							        $schoolsarray[] = $sa->uniqueconstcode;
							    }
							    $schoolslist = implode(',', $schoolsarray);
		                        $studentsql .= " WHERE (t.schoolid in ($schoolslist)) and u.deleted = 0 AND u.confirmed = 1";
		                    }
                        } else {
                           if ($sid != 0 )	{
                           		// $school = get_record('monit_school', 'id', $sid, 'yearid', $yid);
                           		$uniqueconstcode = $school->uniqueconstcode;
                           } else {
                                $uniqueconstcode = 0;
                           }
	                       $studentsql .= " WHERE t.schoolid=$uniqueconstcode AND u.deleted = 0 AND u.confirmed = 1";
	                    }
		break;
    }

	    $strnever = get_string('never');

		// $strcity = get_string('city') . ' (' . get_string('rayon', 'block_monitoring') . ')';
		$strcity = get_string('territory', 'block_monitoring');

        $tablecolumns = array('picture', 'fullname', 'username', 'email', 'city',  'lastaccess', 'editall');
        $tableheaders = array('', get_string('fullname'), get_string('username'), get_string('email'),
								  $strcity, get_string('lastaccess'), get_string('accessrights', 'block_monitoring'));

	    $baseurl = $CFG->wwwroot."/blocks/monitoring/users/operators.php?rid=$rid&amp;sid=$sid&amp;level=$levelmonit";


        $table = new flexible_table("user-index-$levelmonit");

	    $table->define_columns($tablecolumns);
        $table->define_headers($tableheaders);
		// $table->column_style_all('align', 'left');

        $table->define_baseurl($baseurl);

        $table->sortable(true, 'lastname');
		// $table->sortable(true, 'lastaccess', SORT_DESC);

        $table->set_attribute('cellspacing', '0');
		// $table->set_attribute('align', 'left');
        $table->set_attribute('id', 'students');
        $table->set_attribute('class', 'generaltable generalbox');
        // $table->set_attribute('bordercolor', 'black');

        $table->setup();

        if($sortclause = $table->get_sql_sort()) {
            $studentsql .= ' ORDER BY '.$sortclause;
        }

		 // print_r($studentsql);
        $students = get_records_sql($studentsql);


        if(!empty($students)) {

            foreach ($students as $student) {

                if ($student->lastaccess) {
                    $lastaccess = format_time(time() - $student->lastaccess);
                } else {
                    $lastaccess = $strnever;
                }
                $studentcity = $student->city;

                if ($levelmonit == 'school' && $all) {
               		$rec = get_record_sql("SELECT id, name FROM {$CFG->prefix}monit_school
										   WHERE id = {$student->schoolid}");

	                $studentcity = $rec->name;
                }

                $studentusername = '-';
                $strlinkupdate = '';
                $straccess = get_string('editrights', 'block_monitoring');

           		if ($admin_is || $region_operator_is || ($rayon_operator_is && $levelmonit == 'school'))	 {
           			$studentusername = $student->username;
			
                    if (isset($student->editall))	{
                        if ($student->editall == 1)	{
    	                	$straccess = get_string('editrights', 'block_monitoring');
    						$title = get_string('changeeditrights', 'block_mou_att');
    						$strlinkupdate .= " [<a title=\"$title\" href=\"operators.php?action=break&amp;level=$levelmonit&amp;rid=$rid&amp;yid=$yid&amp;uid={$student->id}\">";
    						$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/minus.gif\" alt=\"$title\" /></a>&nbsp;";
    	                } else {
    	                	$straccess = get_string('viewrights', 'block_monitoring');
    						$title = get_string('changeeditrights', 'block_mou_att');
    						$strlinkupdate .= " [<a title=\"$title\" href=\"operators.php?action=ok&amp;level=$levelmonit&amp;rid=$rid&amp;yid=$yid&amp;uid={$student->id}\">";
    						$strlinkupdate .=  "<img src=\"{$CFG->pixpath}/i/tick_green_big.gif\" alt=\"$title\" /></a>&nbsp;";
    	                }
    	                $strlinkupdate .= ']';
                    } 
    		    }
				
                $table->add_data(array (print_user_picture($student->id, 1, $student->picture, false, true),
				    "<div align=left><strong><a href=\"{$CFG->wwwroot}/blocks/monitoring/users/editoper.php?rid=$rid&amp;sid=$sid&amp;level=$levelmonit&amp;uid={$student->id}\">".fullname($student)."</a></strong></div>",
                                "<strong>$studentusername</strong>",
                                $student->email,
                                "<i>$studentcity</i>",
					"<center><small>$lastaccess</small></center>",
								$straccess . $strlinkupdate));
            }

	    	echo '<div align=center>';
			$table->print_html();
        	echo '</div>';

/*
		    if ($admin_is || $creator_is ) {
				$options = array();
			    $options['rid'] = $rid;
			    $options['sid'] = $sid;
			   	$options['sesskey'] = $USER->sesskey;
			    $options['action'] = 'excel';
				echo '<table align="center"><tr>';
			    echo '<td align="center">';
			    print_single_button("operators.php", $options, get_string("downloadexcel"));
			    echo '</td></tr>';
			    echo '</table>';
			}
*/
		}


	$options = array();
    $options['rid'] = $rid;
    $options['sid'] = $sid;
    $options['yid'] = $yid;
    $options['level'] = $levelmonit;
    $options['all'] = 1;
   	$options['sesskey'] = $USER->sesskey;

	echo '<table align="center"><tr>';
    echo '<td align="center">';
    if 	($levelmonit != 'region' && $rid != 0 && !$all)  {
	    print_single_button("operators.php", $options, get_string('alloperators', 'block_monitoring'));
 	    echo '</td>';
 	} else {
 	   echo '</td>';
 	}

	if 	($levelmonit == 'region' || ($levelmonit == 'rayon' && $rid != 0) || ($levelmonit == 'school' && $rid != 0 && $sid !=0))	{
		if (($admin_is || $region_operator_is || ($USER->id==51 && $levelmonit=='school')) && (!isregionviewoperator() && !israyonviewoperator()))	 {
		    echo '<td align="center">';
		    print_single_button("assignoperators.php", $options, get_string('assignoperators', 'block_monitoring'));
	 	    echo '</td>';
 		}
    }

    echo '</tr></table>';

    print_footer();

?>


