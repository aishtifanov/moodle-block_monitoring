<?php  // $Id: tabsprofile.php,v 1.2 2012/03/13 07:02:05 shtifanov Exp $
/// This file to be included so we can assume config.php has already been included.
/// We also assume that $user, $course, $currenttab have been set


    if (empty($currenttab) or empty($user)) {
        error('You cannot call this script in that way');
    }

    // print_heading(fullname($user));

    $inactive = NULL;
    $activetwo = NULL;
    $toprow = array();

    $toprow[] = new tabobject('profile', $CFG->wwwroot."/blocks/monitoring/users/editoper.php?rid=$rid&amp;sid=$sid&amp;level=$levelmonit&amp;uid={$user->id}",
                get_string('profileoper', 'block_monitoring'));


    if ($admin_is || ($region_operator_is && $levelmonit == 'school') || $USER->id==51) {
		    $toprow[] = new tabobject('registrationcard', $CFG->wwwroot."/blocks/monitoring/users/registrationcard.php?mode=5&amp;rid=$rid&amp;sid=$sid&amp;level=$levelmonit&amp;uid={$user->id}",
    	            // get_string('registrationcard', 'block_dean'));
					get_string('editprofileoper', 'block_monitoring'));
	}

   $tabs = array($toprow);


/// Print out the tabs and continue!
    print_tabs($tabs, $currenttab, $inactive, $activetwo);

?>
