<?php // $Id: registrationcard.php,v 1.6 2012/03/13 07:02:05 shtifanov Exp $

    require_once("../../../config.php");
    require_once("$CFG->libdir/gdlib.php");
    require_once('../lib.php');

    $mode = optional_param('mode', 0, PARAM_INT);       // Mode
    $rid = optional_param('rid', 0, PARAM_INT);       // Rayon id
    $sid = optional_param('sid', 0, PARAM_INT);       // School id
    $levelmonit  = optional_param('level', 'region'); // Level
    $uid = optional_param('uid', 0, PARAM_INT);       // User id

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	if (!$admin_is && !$region_operator_is && !$USER->id==51) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $stroperators = get_string('operators', 'block_monitoring');
  	$stroperator = get_string('operator', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/users/operators.php?rid=$rid&amp;sid=$sid&amp;level=$levelmonit\">$stroperators</a>";
	$breadcrumbs .= " -> $stroperator";
    print_header_mou("$site->shortname: $stroperator", $site->fullname, $breadcrumbs);


	    if (!$user = get_record("user", "id", $uid) ) {
	        error("User ID was incorrect");
	    }

        if (isadmin($uid) || record_exists_select_mou('monit_operator_region', "userid = $uid")) {             // Current user is an admin
            print_error('adminprimarynoedit');
            
        /*
            if ($mainadmin = get_admin()) {
                if ($user->id == $mainadmin->id) {  // Can't edit primary admin
                    print_error('adminprimarynoedit');
                }
            }
         */   
        }

	    if (isguest()) {
	        error("The guest user cannot edit their profile.");
    	}

	    if (isguest($user->id)) {
    	    error("Sorry, the guest user cannot be edited.");
	    }

    	// load the relevant auth libraries
		$auth = "manual";    // Can't find auth module, default to internal

/// If data submitted, then process and store.

    if ($usernew = data_submitted()) {

        if (($USER->id <> $usernew->id) && !isadmin() && !$USER->id==51) {
            error("You can only edit your own information");
        }

        if (isset($usernew->password)) {
            unset($usernew->password);
        }

        // data cleanup
        // username is validated in find_regform_errors
        $usernew->country = 'RU';
        $usernew->lang    = 'ru_utf8';
        $usernew->url     = clean_param($usernew->url,     PARAM_URL);
        $usernew->icq     = clean_param($usernew->icq,     PARAM_INT);
        if (!$usernew->icq) {
            $usernew->icq = '';
        }
        $usernew->skype   = '';
        $usernew->yahoo   = '';
        $usernew->aim   = clean_param($usernew->aim,   PARAM_CLEAN);
        $usernew->msn   = '';

        $usernew->maildisplay   = 1;
        $usernew->mailformat    = 1;
        $usernew->maildigest    = 0;
        $usernew->autosubscribe = 1;
        $usernew->htmleditor    = 1;
        $usernew->emailstop     = 0;
        $usernew->trackforums   = 1;

        if (isset($usernew->timezone)) {
            if ($CFG->forcetimezone != 99) { // Don't allow changing this in any way
                unset($usernew->timezone);
            } else { // Clean up the data a bit, just in case of injections
                $usernew->timezone = str_replace(';', '',  $usernew->timezone);
                $usernew->timezone = str_replace('\'', '', $usernew->timezone);
            }
        }

        foreach ($usernew as $key => $data) {
            $usernew->$key = addslashes(clean_text(stripslashes(trim($usernew->$key)), FORMAT_MOODLE));
        }

        $usernew->lastname  = strip_tags($usernew->lastname);
        $usernew->firstname = strip_tags($usernew->firstname);
        $usernew->secondname  = strip_tags($usernew->secondname);

		if (!get_magic_quotes_gpc()) {
	        foreach ($usernew as $key => $data) {
	            $usernew->$key = addslashes(clean_text(stripslashes(trim($usernew->$key)), FORMAT_MOODLE));
	        }
	    } else {
	        foreach ($usernew as $key => $data) {
	            $usernew->$key = clean_text(trim($usernew->$key), FORMAT_MOODLE);
	        }
	    }

        require_once($CFG->dirroot.'/lib/uploadlib.php');
        $um = new upload_manager('imagefile',false,false,null,false,0,true,true);

        // override locked values
        if (!isadmin()) {
            $fields = get_user_fieldnames();
            $authconfig = get_config( 'auth/' . $user->auth );
            foreach ($fields as $field) {
                $configvariable = 'field_lock_' . $field;
                if ( $authconfig->{$configvariable} === 'locked'
                     || ($authconfig->{$configvariable} === 'unlockedifempty' && !empty($user->$field)) ) {
                    if (!empty( $user->$field)) {
                        $usernew->$field = $user->$field;
                    }
                }
            }
            unset($fields);
            unset($field);
            unset($configvariable);
        }
        if (find_regform_errors($user, $usernew, $err, $um)) {
            if (empty($err['imagefile']) && $usernew->picture = save_profile_image($user->id, $um)) {
                set_field('user', 'picture', $usernew->picture, 'id', $user->id);  /// Note picture in DB
            } else {
                if (!empty($usernew->deletepicture)) {
                    set_field('user', 'picture', 0, 'id', $user->id);  /// Delete picture
                    $usernew->picture = 0;
                }
            }

            $usernew->auth = $user->auth;
            $usernew->deleted = $user->deleted;
            $user = $usernew;

        } else {
            $timenow = time();

            if (!$usernew->picture = save_profile_image($user->id,$um)) {
                if (!empty($usernew->deletepicture)) {
                    set_field('user', 'picture', 0, 'id', $user->id);  /// Delete picture
                    $usernew->picture = 0;
                } else {
                    $usernew->picture = $user->picture;
                }
            }

            $usernew->timemodified = time();

            // if (isadmin()) {
                if (!empty($usernew->newpassword)) {
                    $usernew->password = md5($usernew->newpassword);
                    // update external passwords
                    if (!empty($CFG->{'auth_'. $user->auth.'_stdchangepassword'})) {
                        if (function_exists('auth_user_update_password')){
                            if (!auth_user_update_password($user->username, $usernew->newpassword)){
                                error('Failed to update password on external auth: ' . $user->auth .
                                        '. See the server logs for more details.');
                            }
                        } else {
                            error('Your external authentication module is misconfigued!');
                        }
                    }
                }
                // store forcepasswordchange in user's preferences
                if (!empty($usernew->forcepasswordchange)){
                    set_user_preference('auth_forcepasswordchange', 1, $user->id);
                } else {
                    unset_user_preference('auth_forcepasswordchange', $user->id);
                }
            /*} else {
                if (isset($usernew->newpassword)) {
                    error("You can not change the password like that");
                }
            }
            */
            if ($usernew->url and !(substr($usernew->url, 0, 4) == "http")) {
                $usernew->url = "http://".$usernew->url;
            }

            $userold = get_record('user','id',$usernew->id);
            //$usernew->secondname = '';
            $xplode_fstnm = explode(' ', $usernew->firstname);
            $xplode_fstnm[1] = $usernew->secondname;
          //  print_r($xplode_fstnm);
            $usernew->firstname  = $xplode_fstnm[0]. ' ' . $usernew->secondname;
            
           // print_object($usernew);
           // break;
            if (update_record("user", $usernew)) {
                if (function_exists("auth_user_update")){
                    // pass a true $userold here
                    auth_user_update($userold, $usernew);
                };

                 if ($userold->email != $usernew->email) {
                    set_bounce_count($usernew,true);
                    set_send_count($usernew,true);
                }

                add_to_log(1, 'monitoring', "user update", 'registrationcard.php', $USER->lastname.' '.$USER->firstname);

                if ($user->id == $USER->id) {
                    // Copy data into $USER session variable
                    $usernew = (array)$usernew;
                    foreach ($usernew as $variable => $value) {
                        $USER->$variable = stripslashes($value);
                    }
                    if (isset($USER->newadminuser)) {
                        unset($USER->newadminuser);
                        redirect("$CFG->wwwroot/", get_string('changessaved'));
                    }
                    if (!empty($SESSION->wantsurl)) {  // User may have been forced to edit account, so let's
                                                       // send them to where they wanted to go originally
                        $wantsurl = $SESSION->wantsurl;
                        $SESSION->wantsurl = '';       // In case unset doesn't work as expected
                        unset($SESSION->wantsurl);
                        redirect($wantsurl, get_string('changessaved'));
                    } else {
                        redirect("$CFG->wwwroot/blocks/monitoring/users/editoper.php?rid=$rid&amp;sid=$sid&amp;level=$levelmonit&amp;uid={$user->id}", get_string("changessaved"));
                    }
                } else {
                    redirect("$CFG->wwwroot/blocks/monitoring/users/editoper.php?rid=$rid&amp;sid=$sid&amp;level=$levelmonit&amp;uid={$user->id}", get_string("changessaved"));
                }
            } else {
                error("Could not update the user record ($user->id)");
            }
        }
    }

		/// Otherwise fill and print the form.

       	$fullname = fullname($user);
	    $personalprofile = get_string("personalprofile");
	    $participants = get_string("participants");

	    if ($user->deleted) {
	        print_heading(get_string("userdeleted"));
	    }

	/// Print tabs at top
	/// This same call is made in:
	///     ????????
	    $currenttab = 'registrationcard';
	    include('tabsprofile.php');


	    $streditmyprofile = get_string("editmyprofile");
	    $strparticipants = get_string("participants");
	    $strnewuser = get_string("newuser");

	    print_simple_box_start("center", '70%', 'white');

	    if (!empty($err)) {
    	    echo "<center>";
        	notify(get_string("someerrorswerefound"));
	        echo "</center>";
	    }

	    include("regcardedit.html");

	    // if (!isadmin()) {      /// Lock all the locked fields using Javascript
	        $fields = get_user_fieldnames();

    	    echo '<script type="text/javascript">'."\n";
	        echo '<!--'."\n";

	        $authconfig = get_config( 'auth/' . $user->auth );
	        foreach ($fields as $field) {
	            $configvariable = 'field_lock_' . $field;
	            if ( $authconfig->{$configvariable} === 'locked'
	                 || ($authconfig->{$configvariable} === 'unlockedifempty' && !empty($user->$field)) ) {
	                echo "eval('document.form.$field.disabled=true');\n";
	            }
	        }

    	    echo '-->'."\n";
        	echo '</script>'."\n";
	    // }

    	print_simple_box_end();

    print_footer();

   exit;

	/// FUNCTIONS ////////////////////

function find_regform_errors(&$user, &$usernew, &$err, &$um) {
    global $CFG;

    if (isadmin()) {
        if (empty($usernew->username)) {
            $err["username"] = get_string("missingusername");

        } else if (record_exists("user", "username", $usernew->username) and $user->username == "changeme") {
            $err["username"] = get_string("usernameexists");

        } else {
            if (empty($CFG->extendedusernamechars)) {
                $string = eregi_replace("[^(-\.[:alnum:])]", "", $usernew->username);
                if (strcmp($usernew->username, $string)) {
                    $err["username"] = get_string("alphanumerical");
                }
            }
        }

        if (empty($usernew->newpassword) and empty($user->password) and is_internal_auth() )
            $err["newpassword"] = get_string("missingpassword");

        if (($usernew->newpassword == "admin") or ($user->password == md5("admin") and empty($usernew->newpassword)) ) {
            $err["newpassword"] = get_string("unsafepassword");
        }
    }

    if (empty($usernew->email))
        $err["email"] = get_string("missingemail");

    if (over_bounce_threshold($user) && $user->email == $usernew->email)
        $err['email'] = get_string('toomanybounces');

    if (empty($usernew->description) and !isadmin())
        $err["description"] = get_string("missingdescription");

    if (empty($usernew->city))
        $err["city"] = get_string("missingcity");

    if (empty($usernew->firstname))
        $err["firstname"] = get_string("missingfirstname");

    if (empty($usernew->lastname))
        $err["lastname"] = get_string("missinglastname");

    if (empty($usernew->country))
        $err["country"] = get_string("missingcountry");

    if (!validate_email($usernew->email)) {
        $err["email"] = get_string("invalidemail");

    } else if ($otherusers = get_records("user", "email", $usernew->email)) {
    	if (count($otherusers)>1)  {
            $err["email"] = get_string("emailexists");
    	} else {
    	   foreach ($otherusers as $otheruser) 	{
		        if ($otheruser->id <> $user->id) {
  			          $err["email"] = get_string("emailexists");
		        }
		   }
		}
    }

    if (empty($err["email"]) and !isadmin()) {
        if ($error = email_is_not_allowed($usernew->email)) {
            $err["email"] = $error;
        }
    }

    if (!$um->preprocess_files()) {
        $err['imagefile'] = $um->notify;
    }

    $user->email = $usernew->email;

    return count($err);
}

?>