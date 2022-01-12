<?php // $Id: info.php,v 1.4 2012/03/27 11:20:40 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../../monitoring/lib.php');
    require_once('../../mou_att2/lib_att2.php');
    require_once('lib_queue.php');
    
    require_login();

    $rid = optional_param('rid', 0, PARAM_INT);       // Rayon id
    $oid = optional_param('oid', 0, PARAM_INT);       // OU id
    $yid = optional_param('yid', 0, PARAM_INT);       // Year id
    $typeou = optional_param('typeou', '-');       // Type OU
	$action   = optional_param('action', '');
    $tab = optional_param('tab', 'info');          // Rayon id

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }


	$strtitle = get_string('title', 'block_monitoring');
    $strinfo = get_string('infoaboutou', 'block_monitoring');
	$strrequest  = get_string('requestdeclare', 'block_monitoring');
    $strqueue = get_string('queue', 'block_monitoring');
  
    $navlinks = array();
    $navlinks[] = array('name' => $strtitle, 'link' => "$CFG->wwwroot/blocks/monitoring/index.php", 'type' => 'misc');
    $navlinks[] = array('name' => $strinfo, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header($SITE->shortname . ': '. $strtitle, $SITE->fullname, $navigation, "", "", true, "&nbsp;");
    
    // $strnever = get_string('never');
    include('tabs.php');

    echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
	listbox_rayons("info.php?yid=$yid&rid=", $rid);
    listbox_typeou("info.php?yid=$yid&rid=$rid&typeou=", $rid, $typeou);
    if ($typeou != '-')	{
	   listbox_ous("info.php?yid=$yid&rid=$rid&typeou=$typeou&oid=", $rid, $typeou, $oid, $yid);
    }   
	echo '</table>';

    if ($oid > 0)   {
       view_ou_card($typeou, $oid);
       
    }       
    
    print_footer();


function view_ou_card($typeou, $oid)
{
    global $ou;
    
    $outype = get_config_typeou($typeou);

    $ou = get_record ($outype->tblname, 'id', $oid); 
    $card = new editou_form_view();
    $card->display();
    notify ("Замечание: скан-копии лицензий и свидетельство об аккредитации образовательного учреждения можно посмотреть на сайте ОУ: <a href=\"" . $ou->www . "\"> $ou->www </a>", 'green');
     
}
?>
