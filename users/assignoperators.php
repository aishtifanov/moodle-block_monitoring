<?PHP // $Id: assignoperators.php,v 1.7 2009/11/05 07:42:29 Shtifanov Exp $

    require_once('../../../config.php');
    require_once('../lib.php');
    require_once('lib_users.php');
    // require_once('../lib.php');

    define("MAX_USERS_PER_PAGE", 15000);

    $rid = optional_param('rid', 0, PARAM_INT);          // Rayon id
    $sid = optional_param('sid', 0, PARAM_INT);          // School id
    $yid = optional_param('yid', '0', PARAM_INT);       // Year id
    $levelmonit  = optional_param('level', 'region');

	if (!$site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	if (!$admin_is && !$region_operator_is && !$USER->id==51) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $strsearch        = get_string('search');
    $strsearchresults  = get_string('searchresults');
    $strshowall = get_string('showall');

    $stroperators = get_string('operators', 'block_monitoring');
    $strassignoper = get_string('assignoperators', 'block_monitoring');
    $strheadermonit = get_string('assigningoperatorfor'.$levelmonit, 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/users/operators.php?rid=$rid&amp;sid=$sid&amp;level=$levelmonit\">$stroperators</a>";
	$breadcrumbs .= " -> $strassignoper";
    print_header_mou("$site->shortname: $strassignoper", $site->fullname, $breadcrumbs);


    $currenttab = $levelmonit;
    include('tabsoperators.php');


		/// A form was submitted so process the input
		if ($frm = data_submitted())   {
			// print_r($frm);
			if (!empty($frm->add) and !empty($frm->addselect) and confirm_sesskey()) {
				foreach ($frm->addselect as $addoperator) {
	               add_operator($addoperator, $levelmonit, $rid, $sid);
  	            }
			} else if (!empty($frm->remove) and !empty($frm->removeselect) and confirm_sesskey()) {
				foreach ($frm->removeselect as $removeaddoperator) {
	                delete_operator($removeaddoperator, $levelmonit, $rid, $sid);
					// add_to_log(1, 'dean', 'curator deleted', '/blocks/dean/gruppa/curatorsgroups.php', $USER->lastname.' '.$USER->firstname);
				}
			} else if (!empty($frm->showall)) {
				unset($frm->searchtext);
				$frm->previoussearch = 0;
			}

		}

//    	$previoussearch = (!empty($frm) && (!empty($frm->search) or ($frm->previoussearch == 1))) ;

/// Is there a current search?
    $previoussearch = (!empty($frm->search) or ($frm->previoussearch == 1)) ;


/// Get all existing operators
	switch ($levelmonit)	{
		case 'region': $monitoperators  = get_records_sql("SELECT u.id, u.username, u.firstname, u.lastname,
							    						  u.email, u.lastaccess, m.regionid
       								                      FROM {$CFG->prefix}user u,
						                                  {$CFG->prefix}monit_operator_region m
      								                       WHERE m.userid = u.id AND m.regionid = 1
								                           ORDER BY u.lastname ASC");
		break;

		case 'rayon':
					  echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		              listbox_rayons("assignoperators.php?level=rayon&amp;sid=0&amp;rid=", $rid);
					  echo '</table>';
 			  		  if ($rid == 0) exit();

					  $monitoperators  = get_records_sql("SELECT u.id, u.username, u.firstname, u.lastname,
							    						  u.email, u.lastaccess, m.rayonid
       								                      FROM {$CFG->prefix}user u,
						                                  {$CFG->prefix}monit_operator_rayon m
      								                       WHERE m.userid = u.id AND m.rayonid = $rid
								                           ORDER BY u.lastname ASC");
		               if($rayon = get_record('monit_rayon', 'id', $rid))  {
  		                	$strheadermonit .= ' '.$rayon->name;
		               }
		break;

		case 'school':
 						echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
						listbox_rayons("assignoperators.php?level=school&amp;sid=0&amp;rid=", $rid);
						listbox_schools("assignoperators.php?level=school&amp;rid=$rid&amp;yid=$yid&amp;sid=", $rid, $sid, $yid);
						echo '</table>';
                     	if ($rid == 0 ||  $sid == 0) exit();

			            if($school = get_record('monit_school', 'id', $sid))  {
	  		                	$strheadermonit .= ' '.$school->name;
	                       		$uniqueconstcode = $school->uniqueconstcode;
	                    } else {
	                            $uniqueconstcode = 0;
	                    }

					     $monitoperators  = get_records_sql("SELECT u.id, u.username, u.firstname, u.lastname,
								    						  u.email, u.lastaccess, m.schoolid
	       								                      FROM {$CFG->prefix}user u,
							                                  {$CFG->prefix}monit_operator_school m
	      								                       WHERE m.userid = u.id AND m.schoolid = $uniqueconstcode
									                           ORDER BY u.lastname ASC");
		break;
    }

    $operatorsarray = array();
    if ($monitoperators) {
	    foreach ($monitoperators as $operator) {
 	       $operatorsarray[] = $operator->id;
	    }
	}
    $operatorlist = implode(',', $operatorsarray);
    unset($operatorsarray);


/// Get search results excluding any current admins
    if (!empty($frm->searchtext) and $previoussearch) {
        $searchusers = get_users(true, $frm->searchtext, true, $operatorlist, 'lastname ASC',
                                      '', '', 0, 99999, 'id, firstname, lastname, email');
        $usercount = get_users(false, '', true, $operatorlist);
    }

/// If no search results then get potential users excluding current creators
    if (empty($searchusers)) {
    	/*
        if (!$users = get_users(true, '', true, $operatorlist, 'lastname ASC', '', '',
                                0, 99999, 'id, firstname, lastname, email') ) {
            $users = array();
        }*/
        $users = array();
        $usercount = count_records('user');
        // select count(*) from `mou`.`mdl_user`
    }

   $searchtext = (isset($frm->searchtext)) ? $frm->searchtext : "";
   $previoussearch = ($previoussearch) ? '1' : '0';

   print_simple_box_start("center");

   print_heading($strheadermonit, 'center',  4);

   $sesskey = !empty($USER->id) ? $USER->sesskey : '';

   include('assignoperators.html');

   print_simple_box_end();

   print_footer();

?>