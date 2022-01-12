<?php // $Id: importopers.php,v 1.12 2009/12/16 10:48:41 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
	require_once($CFG->dirroot.'/lib/uploadlib.php');
    require_once('../../mou_ege/lib_ege.php');	
    require_once('lib_users.php');

    $rid = optional_param('rid', 0, PARAM_INT);          // Rayon id
    $sid = optional_param('sid', 0, PARAM_INT);          // School id
    $level = optional_param('level', 'school');          // Level: school, staff, udod, boss, dou

	define('ROLE_OPERATOR_EMOU', 8);


	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	if (!$admin_is && !$region_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

	if (isregionviewoperator())  {
        error(get_string('adminaccess', 'block_monitoring'));
	}

	$struser = get_string('user');
    $stroperators = get_string('operators', 'block_monitoring') . " ($level)";

	if ($level == 'school')	{
 	   $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
 	} else {
	   $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/mou_att/index.php">'.get_string('title','block_mou_att').'</a>';
 	}
	$breadcrumbs .= " -> $stroperators";
    print_header_mou("$SITE->shortname: $stroperators", $SITE->fullname, $breadcrumbs);

    // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);



    if ($level == 'school')	{
    	$currenttab = 'import';
	    include('tabsoperators.php');
	} else {
	    $lid = 0;
	    $currenttab = 'import'.$level;
	    // include($CFG->wwwroot.'/blocks/mou_att/users/tabsoperators.php');
	   $toprow = array();

	   $toprow[] = new tabobject('region', $CFG->wwwroot."/blocks/mou_att/users/operators.php?rid=$rid&amp;lid=$lid&amp;level=region",
	                get_string('regionopers', 'block_monitoring'));
	   $toprow[] = new tabobject('rayon', $CFG->wwwroot."/blocks/mou_att/users/operators.php?rid=$rid&amp;lid=$lid&amp;level=rayon",
	                get_string('rayonopers', 'block_monitoring'));
	   $toprow[] = new tabobject('college', $CFG->wwwroot."/blocks/mou_att/users/operators.php?rid=$rid&amp;lid=$lid&amp;level=college",
	                get_string('collegeopers', 'block_mou_att'));
	   $toprow[] = new tabobject('udod', $CFG->wwwroot."/blocks/mou_att/users/operators.php?rid=$rid&amp;did=$lid&amp;level=udod",
                	get_string('udodopers', 'block_mou_att'));

	   if ($admin_is || $region_operator_is)	 {
	   		$toprow[] = new tabobject('importstaff', $CFG->wwwroot."/blocks/monitoring/users/importopers.php?level=staff",
	    	            get_string('importoperscollege', 'block_monitoring'));
	   		$toprow[] = new tabobject('importudod', $CFG->wwwroot."/blocks/monitoring/users/importopers.php?level=udod",
	    	            get_string('importopersudod', 'block_monitoring'));
	   		$toprow[] = new tabobject('importboss', $CFG->wwwroot."/blocks/monitoring/users/importopers.php?level=boss",
	    	            get_string('importopersboss', 'block_monitoring'));
	   		$toprow[] = new tabobject('importdou', $CFG->wwwroot."/blocks/monitoring/users/importopers.php?level=dou",
	    	            get_string('importopersudou', 'block_monitoring'));

	   }

	   $tabs = array($toprow);
	   print_tabs($tabs, $currenttab, NULL, NULL);

	}

		
    $csv_delimiter = ';';
    $usersnew = 0;
	$userserrors  = 0;
    $linenum = 2; // since header is line 1

	/// If a file has been uploaded, then process it

//	if (!empty($frm) ) {
		$um = new upload_manager('userfile',false,false,null,false,0);
		$f = 0;
		if ($um->preprocess_files()) {
			$filename = $um->files['userfile']['tmp_name'];

		    @set_time_limit(0);
		    @raise_memory_limit("192M");
		    if (function_exists('apache_child_terminate')) {
		        @apache_child_terminate();
		    }


			$text = file($filename);
			if($text == FALSE){
				error(get_string('errorfile', 'block_monitoring'), "$CFG->wwwroot/blocks/monitoring/users/importopers.php");
			}
			$size = sizeof($text);

			$textlib = textlib_get_instance();
  			for($i=0; $i < $size; $i++)  {
				$text[$i] = $textlib->convert($text[$i], 'win1251');
            }
            unset ($textlib);

		    $required = array( "lastname" => 1, "firstname" => 1, "email" => 1,
							    'phone1' => 1, 'city' => 1, 'description' => 1, 'idschool' => 1);

 		    if ($level == 'udod' || $level == 'dou')	{
	 		    $required['idrayon'] = 1;
	 		}

            // --- get and check header (field names) ---
            $header = split($csv_delimiter, $text[0]);
            // check for valid field names
            foreach ($header as $i => $h) {
                $h = trim($h);
                $header[$i] = $h;
                if (!isset($required[$h])) {
                    error(get_string('invalidfieldname', 'error', $h), "$CFG->wwwroot/blocks/monitoring/users/importopers.php");
                }
                if (isset($required[$h])) {
                    $required[$h] = 0;
                }
            }

			echo 'login;password;lastname;firstname;email<br>';

  			for($i=1; $i < $size; $i++)  {
	            $line = split($csv_delimiter, $text[$i]);
 	  	        foreach ($line as $key => $value) {
  	                $record[$header[$key]] = trim($value);
   	 	        }

                // print_r($record);
                // add fields to object $user
                foreach ($record as $name => $value) {
                    // check for required values
                   if ($level == 'udod' || $level == 'dou')	{
	                    $user->{$name} = addslashes($value);
                    } else {
	                    if (isset($required[$name]) and !$value) {
		                        error(get_string('missingfield', 'error', $name). " ".
		                              get_string('erroronline', 'error', $linenum),
		                              'importopers.php');
	                    }
	                    // normal entry
	                    else {
	                        $user->{$name} = addslashes($value);
	                    }
	                }
                }

                // print_r($user); echo '<hr>'; continue;


				$aemail = explode('@', $user->email);
				$user->username = $aemail[0];

				 if($olduser = get_record("user", "username", $user->username))		{
				      if ($olduser->email == $user->email)	{
                           //Record not added - user is already registered
                           //In this case, output userid from previous registration
                           //This can be used to obtain a list of userids for existing users
                           notify("$olduser->id ".get_string('usernotaddedregistered', 'error', $user->username . ' '. $user->lastname. ' '.  $user->firstname));
                           $userserrors++;
                           continue;
                      }
                 }


                $j = 1;
				while (record_exists('user', 'username', $user->username))  {
					$user->username = $aemail[0].'-'.$j;
					if ($j++ > 10) break;
				}

                $user->mnethostid = $CFG->mnet_localhost_id;
                $pswtxt = generate_password2(6);
                $user->password = hash_internal_user_password($pswtxt);
                $user->confirmed = 1;
                $user->timemodified = time();
                $user->country = 'RU';
                $user->lang = 'ru_utf8';
                $description = get_string('operator', 'block_monitoring') . ' '.$user->description . ' ('. $user->city . ')';
                $user->description = $description;
                // echo '<hr>';
                // print_r($user);

                if ($user->id = insert_record("user", $user)) {
                    echo "$user->username; $pswtxt; $user->lastname; $user->firstname; $user->email<br>";
                    $usersnew++;
                } else {
                    // Record not added -- possibly some other error
                    notify(get_string('usernotaddederror', 'error', $user->username));
                    $userserrors++;
                    continue;
	            }

                $coursecontext = get_context_instance(CONTEXT_COURSE, 1);
                if (!user_can_assign($coursecontext, ROLE_OPERATOR_EMOU)) {
                    notify("--> Can not assign role: $user->id = $user->username ($user->lastname $user->firstname)"); //TODO: localize
                }
                $ret = role_assign(ROLE_OPERATOR_EMOU, $user->id, 0, $coursecontext->id);

				$idrayon = 0;
				if ($level == 'udod' || $level == 'dou')	{
					$idrayon = $user->idrayon;
				}

                if (!add_operator($user->id, $level, $idrayon, $user->idschool)) {
                    notify("--> Can not add school <b>operator</b>: $user->id = $user->username ($user->lastname $user->firstname)"); //TODO: localize
                }

                $linenum++;
                unset($user);
            }
		    $strusersnew = get_string("usersnew");
    	    notify("$strusersnew: $usersnew", 'green', 'center');
            notify(get_string('errors', 'admin') . ": $userserrors");
	        echo '<hr />';
       }

//   }


/// Print the form
    $struploadusers = get_string("uploadusers", "block_monitoring")  . " ($level)";
    print_heading_with_help($struploadusers, 'importgroup');

    $maxuploadsize = get_max_upload_file_size();
	$strchoose = ''; // get_string("choose"). ':';
    echo '<center>';
    echo '<form method="post" enctype="multipart/form-data" action="importopers.php">'.
         $strchoose.'<input type="hidden" name="MAX_FILE_SIZE" value="'.$maxuploadsize.'">'.
         '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'">'.
         '<input type="hidden" name="rid" value="'.$rid.'">'.
         '<input type="hidden" name="sid" value="'.$sid.'">'.
         '<input type="hidden" name="level" value="'.$level.'">'.
         '<input type="file" name="userfile" size="50">'.
         '<br><input type="submit" value="'.$struploadusers.'">'.
         '</form>';
    echo '</center>';

    print_footer();


?>

