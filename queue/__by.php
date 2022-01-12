<?php // $Id: accreditation.php,v 1.12 2008/10/08 06:54:09 Shtifanov Exp $

    require_once("../../../config.php");

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

    $strsql = "SELECT id, childid FROM {$CFG->prefix}monit_queue_request "; 
    if($requests = get_records_sql($strsql))  {
         foreach($requests as $request){
            if($child = get_record('monit_queue_child', 'id', $request->childid)) {
                // $birthyear = date('Y', $child->birthday);
                $birthyear =  get_birthyear_child($child->birthday);
                set_field('monit_queue_request', 'birthyear', $birthyear, 'id', $request->id);  
            }    
         }
    }
    
    echo 'Complete.';        

	print_footer();
    
function get_birthyear_child($birthday)
{
   // $ret = date('Y'); 
   $birthyear = date('Y-m-d', $birthday);
   if ($by = get_record_select('monit_queue_birthyear', "datestart < '$birthyear' AND '$birthyear' < dateend"))  {
       $ret = $by->id; 
   } else {
       $ret = date('Y', $birthday);   
   }
   
   return $ret; 
}    
  			
?>


