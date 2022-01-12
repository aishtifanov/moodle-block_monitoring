<?PHP // $Id: lib_users.php

require_once('../../mou_school/lib_school.php');
/**
 * Add a operators
 *
 * @param int $userid The id of the user that is being tested against.
 * @return boolean
 */
function add_operator($userid, $levelmonit, $rid, $oid)
{
	global $CFG;
	
    $user = get_record_select('user', "id = $userid", 'id, username, lastname, firstname');

    if ($user) {
      
        $systemcontext = get_context_instance(CONTEXT_SYSTEM);
        $role_student = get_record('role', 'shortname', 'student');
        role_assign($role_student->id, $user->id, 0, $systemcontext->id);
        
        
        $coursecontext = get_context_instance(CONTEXT_COURSE, 1);
        $role_teacher = get_record('role', 'shortname', 'teacher');
        role_assign($role_teacher->id, $user->id, 0, $coursecontext->id);

        $fullname = fullname($user);
	    $monitoperator->userid = $userid;
	    $role_oper = get_record('role', 'shortname', 'operator_mou');
        role_assign($role_oper->id, $user->id, 0, $coursecontext->id);
        
	    $ctx = false;
        $flagexists = false;
		switch ($levelmonit)	{
			case 'region': $monitoperator->regionid = 1;
						   $ctx = get_context_instance(CONTEXT_REGION, 1);
                           if (record_exists('monit_operator_region', 'userid', $userid)) {
                        		notify(get_string('existoperatorregion', 'block_monitoring', $fullname));
                        		$flagexists = true;
                           }
                           
			break;
			case 'rayon':  $monitoperator->rayonid =$rid;
						   $ctx = get_context_instance(CONTEXT_RAYON, $rid);
                           if (record_exists('monit_operator_rayon', 'userid', $userid)) {
    		                  notify(get_string('existoperatorrayon', 'block_monitoring', $fullname));
		                      $flagexists = true;
	                       }

			break;
  			case 'school': $school = get_record_select('monit_school', "id = $oid", 'id, rayonid, uniqueconstcode');
				 		   $monitoperator->schoolid = $school->uniqueconstcode;
                           $curryearid = get_current_edu_year_id();
                           $schooool = get_record_sql("SELECT id, yearid, uniqueconstcode  FROM {$CFG->prefix}monit_school
    							     				   WHERE uniqueconstcode = {$school->uniqueconstcode}  AND yearid = $curryearid"); 
				 		   $ctx = get_context_instance(CONTEXT_SCHOOL, $schooool->id);
                           if (record_exists('monit_operator_school', 'userid', $userid)) {
                        		notify(get_string('existoperatorschool', 'block_monitoring', $fullname));
                        		$flagexists = true;
                           }
                           
			break;
            case 'college':  
  			case 'staff':  $college = get_record_select('monit_college', "id = $oid", 'id, rayonid, uniqueconstcode');
				 		   $monitoperator->collegeid = $college->uniqueconstcode;
				 		   $monitoperator->rayonid = $rid;
				 		   $monitoperator->editall = 1;
                           $ctx = get_context_instance(CONTEXT_COLLEGE, $college->id);
                           if (record_exists('monit_operator_staff', 'userid', $userid)) {
                        		notify(get_string('existoperatorstaff', 'block_monitoring', $fullname));
                        		$flagexists = true;
                           }
                           
			break;
  			case 'udod':   $udod = get_record_select('monit_udod', "id = $oid", 'id, rayonid, uniqueconstcode');
                           $monitoperator->udodid  = $udod->uniqueconstcode;
				 		   $monitoperator->rayonid = $rid;
                           $ctx = get_context_instance(CONTEXT_UDOD, $udod->id);
                           if (record_exists('monit_operator_udod', 'userid', $userid)) {
                        		notify(get_string('existoperatorudod', 'block_monitoring', $fullname));
                        		$flagexists = true;
                           }
                              
			break;
  			case 'dou':    $dou = get_record_select('monit_education', "id = $oid", 'id, rayonid, uniqueconstcode');
                           $monitoperator->douid  = $dou->uniqueconstcode;
				 		   $monitoperator->rayonid = $rid;
                           $ctx = get_context_instance(CONTEXT_DOU, $dou->id);  
                           if (record_exists('monit_operator_dou', 'userid', $userid)) {
                       		   notify(get_string('existoperatordou', 'block_monitoring', fullname($u)));
                        	   $flagexists = true;
                           }
                                       
			break;
  			case 'boss':   $monitoperator->collegeid = 0;
				 		   $monitoperator->rayonid = 0;
				 		   $monitoperator->editall = 0;
				 		   $levelmonit = 'staff';
                           if (record_exists('monit_operator_staff', 'userid', $userid)) {
                        		notify(get_string('existoperatorstaff', 'block_monitoring', $fullname));
                        		$flagexists = true;
                           }
			break;
	    }
	    if ($ctx)	{
  			if (!role_assign_mou($role_oper->id, $userid, $ctx->id))	{
				notify("Not assigned operator $levelmonit: {$monitoperator->userid}.");
			}
		}

        if (!$flagexists)   {
            if ($levelmonit == 'college' || $levelmonit == 'boss') {
                $tblname = 'monit_operator_staff';
            }  else {
                $tblname = 'monit_operator_'.$levelmonit;
            } 
    		if (insert_record($tblname, $monitoperator))	{
    			// add_to_log(1, 'monitoring', 'operator added', '/blocks/monitoring/users/operators.php?level=$levelmonit&amp;sid=$sid&amp;rid=$rid', $USER->lastname.' '.$USER->firstname);
    		} else  {
    		    print_r($monitoperator);  
    			error(get_string('errorinaddingoperators','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/users/operators.php?level=$levelmonit&amp;sid=$oid&amp;rid=$rid");
    		}
        }           
    }

    return true;
}

/**
 * Delete a operators
 *
 * @param int $userid The id of the user that is being tested against.
 * @return boolean
 */
function delete_operator($userid, $levelmonit, $rid, $oid)
{
    $user = get_record_select('user', "id = $userid", 'id, username, lastname, firstname');

    if ($user) {
        $fullname = fullname($user);
	    $monitoperator->userid = $userid;
	    $role_oper = get_record('role', 'shortname', 'operator_mou');
	    $ctx = false;
        $flagexists = false;
        
        switch ($levelmonit)	{
        	case 'region': $ctx = get_context_instance(CONTEXT_REGION, 1);
                           $ret = delete_records('monit_operator_region', 'regionid', 1, 'userid', $userid);
        	break;
        	case 'rayon':  $ctx = get_context_instance(CONTEXT_RAYON, $rid);
                           $ret = delete_records('monit_operator_rayon', 'rayonid', $rid, 'userid', $userid);
        	break;
        	case 'school': $school = get_record_select('monit_school', "id = $oid", 'id, uniqueconstcode');
        	               $ctx = get_context_instance(CONTEXT_SCHOOL, $sid);
                           $ret = delete_records('monit_operator_school', 'schoolid', $school->uniqueconstcode, 'userid', $userid);
        	break;
            case 'college':
        	case 'staff':
                            $college = get_record_select('monit_college', "id = $oid", 'id, uniqueconstcode');
                            $ctx = get_context_instance(CONTEXT_COLLEGE, $college->id);
        					$ret = delete_records('monit_operator_staff', 'userid', $userid);
        
        	break;
  			case 'udod':   $udod = get_record_select('monit_udod', "id = $oid", 'id, uniqueconstcode');
                           $ctx = get_context_instance(CONTEXT_UDOD, $udod->id);
        				   $ret = delete_records('monit_operator_udod', 'userid', $userid);                           
                              
			break;
  			case 'dou':    $dou = get_record_select('monit_education', "id = $oid", 'id, uniqueconstcode');
                           $ctx = get_context_instance(CONTEXT_DOU, $dou->id);  
		                   $ret = delete_records('monit_operator_dou', 'userid', $userid);
                                       
			break;
        }
           
        if ($ctx)	{
    		if (!role_unassign_mou($role_oper->id, $userid, $ctx->id))	{
    			notify("Not assigned operator $levelmonit: $userid.");
    		}
    	}
   }         

   return $ret;  
}

?>