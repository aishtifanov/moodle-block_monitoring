<?php // $Id: lib_auth.php,v 1.15 2012/08/22 08:52:29 shtifanov Exp $




function ismonitoperator($levelmonit, $userid=0, $rid=0, $sid=0, $anywhere=false)
{
    global $USER, $CFG;

    if (empty($USER->id)) {
        return false;
    }

    if (empty($userid))  {
        $userid = $USER->id;
    }

    if (isadmin($userid)) {  // admins can do anything
        return true;
    }

    // ACCESS DENIED !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // return false;
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

	switch ($levelmonit)	{
		case 'region': return record_exists_select_mou('monit_operator_region', "userid = $userid AND ismonitzp=0");
		break;
		case 'regionzp': return record_exists_select_mou('monit_operator_region', "userid = $userid AND ismonitzp=1");
		break;
		case 'rayon':  if ($anywhere == true) {
			               if ($r_oper = get_record_select('monit_operator_rayon', "userid = $userid AND ismonitzp=0" , 'id, rayonid'))  {
			                   return $r_oper->rayonid;
                           } else 	{
							   return false;
						   }
                            
					   } else {
						   if (!empty($rid))  {
								return record_exists_select_mou('monit_operator_rayon', "rayonid=$rid AND userid=$userid AND ismonitzp=0");
						   } else 	{
								return false;
						   }
					   }
		break;
		case 'rayonzp':  if ($anywhere == true) {
			               if ($r_oper = get_record_select('monit_operator_rayon', "userid = $userid AND ismonitzp=1" , 'id, rayonid'))  {
			                   return $r_oper->rayonid;
                           } else 	{
							   return false;
						   }
                            
					   } else {
						   if (!empty($rid))  {
								return record_exists_select_mou('monit_operator_rayon', "rayonid=$rid AND userid=$userid AND ismonitzp=1");
						   } else 	{
								return false;
						   }
					   }
		break;
		case 'school': if ($anywhere == true) {
			               if ($s_oper = get_record_select('monit_operator_school', "userid = $userid", 'id, schoolid')) {
			                 return $s_oper->schoolid;
                           } else {
								return false;
						   }
					   } else {
					       if ($school = get_record_sql ("SELECT id, uniqueconstcode FROM {$CFG->prefix}monit_school WHERE id = $sid"))	{
					     		$uniqueconstcode = $school->uniqueconstcode;
					       } else {
   						   	    $uniqueconstcode = $sid;
					       }
                           // print_r($school);

						   if (!empty($uniqueconstcode))  {
								return record_exists_select_mou('monit_operator_school', "schoolid=$uniqueconstcode AND userid=$userid");
						   } else 	{
								return false;
						   }
					   }
		break;
		case 'staff':  	   return record_exists_select_mou('monit_operator_staff', "userid=$userid AND rayonid=0 AND collegeid=0");
		break;
		case 'staffview':  return record_exists_select_mou('monit_operator_staff', "userid=$userid AND editall=0 AND rayonid=0");
		break;
		case 'college' :   if ($anywhere == true) {
			               		if ($c_oper = get_record_select('monit_operator_staff', "userid=$userid", 'id, collegeid')) {
			               		// print_r ($c_oper);
				                    return $c_oper->collegeid;
                                } else 	{
                                    return false;
						        }
						   } else {
						       if ($college = get_record_sql ("SELECT id, uniqueconstcode FROM {$CFG->prefix}monit_college WHERE id = $sid"))	{
						     		$uniqueconstcode = $college->uniqueconstcode;
						       } else {
   							   	    $uniqueconstcode = $sid;
						       }
	                           // print_r($school);

							   if (!empty($uniqueconstcode))  {
									return record_exists_select_mou('monit_operator_staff', "userid=$userid AND rayonid=$rid AND collegeid=$uniqueconstcode");
							   } else 	{
									return false;
							   }
					 	  }
		break;
		case 'dod_rayon':
							if ($anywhere == true)  {
				               if ($r_oper = get_record_select('monit_operator_udod', "userid=$userid", 'id, udodid, rayonid')) {
    				               if ($r_oper->udodid == 0)	{
    				               	   return $r_oper->rayonid;
    				               } else 	{
    								   return false;
    							   }
                               } else {
                           	       return false;
                               } 
						   } else {

							   if (!empty($rid))  {
									return record_exists_select_mou('monit_operator_udod', "rayonid=$rid AND userid=$userid AND udodid=0");
							   } else 	{
									return false;
							   }
						   }
		break;

		case 'dod_school':
						   if ($anywhere == true) {
			               		if ($s_oper = get_record_select('monit_operator_udod', "userid=$userid", 'id, udodid')) {
				                    return $s_oper->udodid;
                                } else {
                                    return false;
                                }    
						   } else {
						       if ($school = get_record_sql ("SELECT id, uniqueconstcode FROM {$CFG->prefix}monit_udod WHERE id = $sid"))	{
						     		$uniqueconstcode = $school->uniqueconstcode;
						       } else {
	   						   	    $uniqueconstcode = $sid;
						       }
	                           // print_r($school);
	
							   if (!empty($uniqueconstcode))  {
							   		return record_exists_select_mou('monit_operator_udod', "udodid = $uniqueconstcode AND userid=$userid");									
							   } else 	{
									return false;
							   }
						   }		
		break;
		case 'dou':
						   if ($anywhere == true) {
			               		if ($s_oper = get_record_select('monit_operator_dou', "userid = $userid", 'id, douid')) {
				                    return $s_oper->douid;
                                } else {
                                    return false;
                                }    
						   } else {
						       if ($school = get_record_sql ("SELECT id, uniqueconstcode FROM {$CFG->prefix}monit_education WHERE id = $sid"))	{
						     		$uniqueconstcode = $school->uniqueconstcode;
						       } else {
	   						   	    $uniqueconstcode = $sid;
						       }
	                           // print_r($school);
	
							   if (!empty($uniqueconstcode))  {
							   		return record_exists_select_mou('monit_operator_dou', "douid = $uniqueconstcode AND userid = $userid");									
							   } else 	{
									return false;
							   }
						   }		
		break;
    }
}


function isstaffviewoperator($userid=0)
{
    global $USER, $CFG;

    if (empty($USER->id)) {
        return false;
    }

    if (empty($userid))  {
        $userid = $USER->id;
    }

	return record_exists_select_mou('monit_operator_staff', "userid=$userid AND editall=0 AND rayonid=0");
}


function isregionviewoperator($userid=0)
{
    global $USER, $CFG;

    if (empty($USER->id)) {
        return false;
    }

    if (empty($userid))  {
        $userid = $USER->id;
    }

	return record_exists_select_mou('monit_operator_region', "userid=$userid AND editall=0");
}


function israyonviewoperator($userid=0)
{
    global $USER, $CFG;

    if (empty($USER->id)) {
        return false;
    }

    if (empty($userid))  {
        $userid = $USER->id;
    }

	return record_exists_select_mou('monit_operator_rayon', "userid=$userid AND editall=0");
}


function ispupil($userid=0)
{
    global $USER, $CFG;

    if (empty($USER->id)) {
        return false;
    }

    if (empty($userid))  {
        $userid = $USER->id;
    }


	return record_exists_select_mou('monit_school_pupil_card', "userid = $userid");
}


function record_exists_select_mou($table, $select='') {

    global $CFG;

    if ($select) {
        $select = 'WHERE '.$select;
    }

    return record_exists_sql('SELECT id FROM '. $CFG->prefix . $table . ' ' . $select);
}

?>
