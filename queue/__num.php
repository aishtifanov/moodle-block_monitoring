<?php // $Id: accreditation.php,v 1.12 2008/10/08 06:54:09 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../../monitoring/lib.php');
    require_once('../../mou_att2/lib_att2.php');
    require_once('lib_queue.php');

    $rid = optional_param('rid', 4, PARAM_INT);       // Rayon id
    $oid = optional_param('oid', 4442, PARAM_INT);       // OU id

	$strtitle = ' __ Set birthyear ';
    $strinfo = get_string('infoaboutou', 'block_monitoring');
	$strrequest  = get_string('requestdeclare', 'block_monitoring');
    $strqueue = get_string('queue', 'block_monitoring');
  
    $navlinks = array();
    $navlinks[] = array('name' => $strtitle, 'link' => "$CFG->wwwroot/blocks/monitoring/index.php", 'type' => 'misc');
    $navlinks[] = array('name' => $strrequest, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header($SITE->shortname . ': '. $strtitle, $SITE->fullname, $navigation, "", "", true, "&nbsp;");


	$admin_is = isadmin();
	if (!$admin_is) {
        error(get_string('staffaccess', 'block_mou_att'));
	}

/*
    for ($yy=2006; $yy<=2012; $yy++)    {
        $strsql = "SELECT id, childid FROM {$CFG->prefix}monit_queue_request 
                   WHERE rayonid = $rid AND oid = $oid AND birthyear=$yy 
                   ORDER BY number";
         
        if($requests = get_records_sql($strsql))  {
             $i=1;   
             foreach($requests as $request) {
                set_field('monit_queue_request', 'numberinyear', $i, 'id', $request->id);
                $i++;  
             }
        }     
    }
*/

        $strsql = "SELECT id, timemodified FROM {$CFG->prefix}monit_queue_request 
                   WHERE rayonid = $rid AND oid = $oid 
                   ORDER BY timemodified";
         
        if($requests = get_records_sql($strsql))  {
             $i=1;   
             foreach($requests as $request) {
                change_status (STATUS_PUTINTOQUEUE, $request->id);
                $i++;  
                echo $i . '<br>';
             }
        }     

    
        
    echo 'Complete.';        

	print_footer();
 			
?>


