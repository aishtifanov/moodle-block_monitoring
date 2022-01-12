<?php // $Id: listforms.php,v 1.16 2012/11/14 10:58:53 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
	require_once('../../mou_ege/lib_ege.php');
    require_once('lib_rating.php');	
    
    
    $rid = required_param('rid', PARAM_INT);            // Rayon id
    $sid = optional_param('sid', 0, PARAM_INT);            // School id    
    $yid = optional_param('yid', 0, PARAM_INT);       		// Year id
    $action   = optional_param('action', '');
    $rzid = optional_param('rzid', -1, PARAM_INT);       
    $statusid = optional_param('statusid', 0, PARAM_INT);       
    $nm = 9;
    
    $scriptname = 'changestatusgroup.php';

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
   	if (!$admin_is && !$region_operator_is) { 
	// if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $curryearid = get_current_edu_year_id();
    
    if ($yid != 0)	{
    	$eduyear = get_record('monit_years', 'id', $yid);
    } else {
    	$yid = $curryearid;
    	$eduyear = get_record('monit_years', 'id', $yid);
    }

   	$strtitle = get_string('title','block_monitoring');
	$strscript = 'Изменение статуса таблиц исходных данных';

    $navlinks = array();
    $navlinks[] = array('name' => $strtitle, 'link' => "$CFG->wwwroot/blocks/monitoring/index.php?rid=$rid&yid=$yid", 'type' => 'misc');
    $navlinks[] = array('name' => $strscript, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    print_header($SITE->shortname . ': '. $strscript, $SITE->fullname, $navigation, "", "", true, "&nbsp;"); // , navmenu($course)

    $currenttab = 'reports';
    include('tabs.php');

    $currenttab2 = 'changestatusgroup';
    include('tabs2.php');    

    print_tabs_years_rating("$scriptname?a=0", $rid, $sid, $yid);

	if ($admin_is  || $region_operator_is ) {
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		listbox_rayons("$scriptname?yid=$yid&rid=", $rid);
        listbox_razdel_school("$scriptname?yid=$yid&rid=$rid&statusid=$statusid&rzid=", $yid, $rzid);
        listbox_status_school("$scriptname?yid=$yid&rid=$rid&rzid=$rzid&statusid=", $statusid);
		echo '</table>';
	}

	if ($rid == 0) {
	    print_footer();
	 	exit();
	}

    if ($action == 'change' && $statusid > 0 && $rid > 0) 	{
        $selectstatusid = '';
        if ($statusid > 2) {
            // $selectstatusid = ' status <> 2 AND ';
        }
        
        if ($rzid > 0)  {
            $shortname = get_field_select('monit_razdel', 'shortname', "id=$rzid");
            $select = " shortname = '$shortname' AND "; 
        } else {
            $shortnames = get_listnameforms($yid, 'school');
            $formlists = implode("','", $shortnames);
            $select = " shortname in ('$formlists') AND ";
        }
        
        $sql = "update mdl_monit_rating_listforms
                set status=$statusid
                where $select $selectstatusid schoolid in (SELECT id FROM mdl_monit_school where yearid=$yid and rayonid=$rid)";
        execute_sql($sql, false);
        // print $sql . '<br>';
        notify('Изменение статуса выполнено.', 'green');
	} else 	if ($rid != 0 && $statusid != 0)   {
       
        	$options = array('action'=> 'change', 'rid' => $rid, 'sid' => $sid, 'yid' => $yid, 
        					 'rzid' => $rzid, 'statusid' => $statusid,  'sesskey' => $USER->sesskey);
           	echo '<center>';
            print_single_button($scriptname, $options, 'Установить статус у выбранной таблиц(ы) для всех ОУ выбранного района');
            echo '</center>';
    }
    print_footer();


function listbox_razdel_school($scriptname, $yid, $rzid)
{
    global $CFG;

    $shortnames = get_listnameforms($yid, 'school');
    $formlists = implode("','", $shortnames);
    $select = "shortname in ('$formlists')";
    // print $select;
    
    $menu = get_records_select_menu('monit_razdel', $select, 'id', 'id, name');
    $menu[-1] = 'ВСЕ ТАБЛИЦЫ';
    $menu[0] = 'Выберите категорию показателей ...';
    
    echo '<tr><td>Таблица данных:</td><td>';
    popup_form($scriptname, $menu, 'switchtbldata', $rzid, '', '', '', false);
    echo '</td></tr>';
    return 1;
}


function listbox_status_school($scriptname, $statusid)
{
    global $CFG;
    

    $menu = array();
    $menu[0] = 'Выберите статус ...';
    $menu[2] = 'В работе';
    $menu[3] = 'Доработать';
    $menu[4] = 'На согласовании';
    
    echo '<tr><td>Статус:</td><td>';
    popup_form($scriptname, $menu, 'switchstatus', $statusid, '', '', '', false);
    echo '</td></tr>';
    return 1;
}

?>