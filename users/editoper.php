<?php // $Id: editoper.php,v 1.5 2009/02/25 08:23:53 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $rid = optional_param('rid', 0, PARAM_INT);       // Rayon id
    $sid = optional_param('sid', 0, PARAM_INT);       // School id
    $levelmonit  = optional_param('level', 'region'); // Level
    $uid = optional_param('uid', 0, PARAM_INT);       // User id

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

    $stroperators = get_string('operators', 'block_monitoring');
  	$stroperator = get_string('operator', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/users/operators.php?rid=$rid&amp;sid=$sid&amp;level=$levelmonit\">$stroperators</a>";
	$breadcrumbs .= " -> $stroperator";
    print_header_mou("$site->shortname: $stroperator", $site->fullname, $breadcrumbs);

	    if (!$user = get_record("user", "id", $uid) ) {
	        error("No such user in this course");
	    }

    	$fullname = fullname($user);
	    $personalprofile = get_string("personalprofile");
	    $participants = get_string("participants");

	    if ($user->deleted) {
	        print_heading(get_string("userdeleted"));
	    }

		if ($admin_is || $region_operator_is || ($USER->id==51 && $levelmonit=='school'))	 {
		    $currenttab = 'profile';
		    include('tabsprofile.php');
		}

    	echo "<table width=\"80%\" align=\"center\" border=\"0\" cellspacing=\"0\" class=\"userinfobox\">";
	    echo "<tr>";
	    echo "<td width=\"100\" valign=\"top\" class=\"side\">";
    	print_user_picture($user->id, 1, $user->picture, true, false, false);
	    echo "</td><td width=\"100%\" class=\"content\">";

    	// Print the description

    	if ($user->description) {
        	echo format_text($user->description, FORMAT_MOODLE)."<hr />";
	    }

    	// Print all the little details in a list

	    echo '<table border="0" cellpadding="0" cellspacing="0" class="list">';

    	print_row(get_string('fio', 'block_monitoring').':', $fullname);


	    if ($user->institution) {
	    	$institution = $user->institution;
    	} else  {
	    	$institution = '-';
    	}
    	print_row(get_string('organization', 'block_monitoring').':', $institution);

	    if ($user->department) {
	    	$department = $user->department;
    	} else  {
	    	$department = '-';
    	}
    	print_row(get_string('department').':', $department);


    	print_row(get_string('role').':', get_string($levelmonit.'oper', 'block_monitoring'));

    	print_row("E-mail:", obfuscate_mailto($user->email, '', $user->emailstop));

	    if ($user->phone1) {
	    	$phone1 = $user->phone1;
    	} else  {
	    	$phone1 = '-';
    	}
        print_row(get_string("phone").":", $phone1);

	    if ($user->phone2) {
	    	$phone2 = $user->phone2;
    	} else  {
	    	$phone2 = '-';
    	}
        print_row(get_string('mobilephone', 'block_monitoring').":", $phone2);

    	if ($user->icq) {
	       	print_row(get_string('icqnumber').':',"<a href=\"http://web.icq.com/wwp?uin=$user->icq\">$user->icq<img src=\"http://web.icq.com/whitepages/online?icq=$user->icq&amp;img=5\" width=\"18\" height=\"18\" border=\"0\" alt=\"\" /></a>");

	    } else {
	       	print_row(get_string('icqnumber').':',"-");
	    }


    	if ($user->aim) {
    		$aim = $user->aim;
    	} else {
    		$aim = '-';
	    }
      	print_row(get_string('aimid').':', '<a href="aim:goim?screenname='.s($aim).'">'.s($aim).'</a>');

	    if ($user->url) {
    	    print_row(get_string('webpage', 'block_monitoring').":", "<a href=\"$user->url\">$user->url</a>");
	    } else {
		    print_row(get_string('webpage', 'block_monitoring').":", "-");
	    }


		$stradress = "";
	    if ($user->city or $user->country) {
	        $countries = get_list_of_countries();
			$stradress .= $countries["$user->country"].", $user->city";
	    }
    	if ($user->address) {
			$stradress .= ", $user->address";
	    }
        print_row(get_string("address").":", $stradress);

/*
    	if ($user->skype) {
        	print_row(get_string('skypeid').':','<a href="callto:'.urlencode($user->skype).'">'.s($user->skype).
            	' <img src="http://mystatus.skype.com/smallicon/'.urlencode($user->skype).'" alt="status" '.
	           ' height="16" width="16" /></a>');
	    }
*/
	    if ($mycourses = get_my_courses($user->id)) {
    	   $courselisting = '';
    	   print_row('<hr>', '<hr>');
	       print_row(get_string('courses').':', '('.count($mycourses).')');
	       foreach ($mycourses as $mycourse) {
		       if ($mycourse->visible and $mycourse->category) {
    		       $courselisting = "<a href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$mycourse->id\">$mycourse->fullname</a>";
       			}
		       print_row('-', $courselisting);
		    }
		}

    echo "</table>";
    echo "</td></tr></table>";

    print_footer();

/// Functions ///////

function print_row($left, $right) {
    echo "\n<tr><td nowrap=\"nowrap\" valign=\"top\" class=\"label c0\" align=\"right\">$left</td><td align=\"left\" valign=\"top\" class=\"info c1\">$right</td></tr>\n";
}

?>


