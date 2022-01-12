<?php // $Id: reports.php,v 1.1 2013/06/08 05:43:38 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../../mou_att2/lib_att2.php');
    require_once('../../mou_ege/lib_ege.php');
    require_once('../lib.php');
    require_once('lib_queue.php');

    $rid = optional_param('rid', '0', PARAM_INT);          // Rayon id
    $sid = optional_param('sid', '0', PARAM_INT);       // School id
    $yid = optional_param('yid', '0', PARAM_INT);       // Year id
    $tsid = optional_param('tsid', '0', PARAM_INT);       // Year id
	$action   = optional_param('action', '');
    $tab = optional_param('tab', 'reports');          // Rayon id
    $tab2 = optional_param('tab2', 1);          // Rayon id

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('school', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $curryearid = get_current_edu_year_id();
    if ($yid == 0)	{
    	$yid = $curryearid;
    }

    $strlistrayons = listbox_rayons_att("reports.php?yid=$yid&rid=", $rid);
    $rayon = get_record('monit_rayon', 'id', $rid);
    
    // $nowtime = time();
    $birthdaystart =  $birthdayend = $requeststart = $requestend = time();
    $selectbd = $selectqr = '';
    $bdateoff = $rbdateoff = '';
    if ($frm = data_submitted()) {
        
        if (!isset($frm->bdateoff))  {
            $birthdaystart = make_timestamp($frm->byearstart, $frm->bmonthstart, $frm->bdaystart, 12);
            $birthdayend =   make_timestamp($frm->byearend, $frm->bmonthend, $frm->bdayend, 12);
            $selectbd = " AND c.birthday > $birthdaystart and c.birthday < $birthdayend ";
        } else {
            $bdateoff = 'checked';
        }         

        if (!isset($frm->rbdateoff))  {
            $requeststart = make_timestamp($frm->ryearstart, $frm->rmonthstart, $frm->rdaystart, 12);
            $requestend =   make_timestamp($frm->ryearend, $frm->rmonthend, $frm->rdayend, 12);
            $selectqr = " and qr.timecreated > $requeststart and qr.timecreated < $requestend ";
        } else {
            $rbdateoff = 'checked';
        }                 
    }    
    
//    $sid = optional_param('sid', '');
    if ($action == 'excel') {
        $table = table_reports1($rid);
        print_table_to_excel($table);
        exit();
	}

    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
    if ($admin_is  || $region_operator_is || $rayon_operator_is)  {
 	   $strschools = get_string('schools', 'block_monitoring');
 	} else {
 	   $strschools = get_string('school', 'block_monitoring');
 	}
    
    $strname = get_string("name");
    $strheadname = get_string('directorschool', 'block_monitoring');
	$strphone = get_string('telnum','block_monitoring');
 	$straddress = get_string('realaddress','block_monitoring');
	$straction = get_string("action","block_monitoring");

	$strtitle = get_string('title', 'block_monitoring');
    $strinfo = get_string('infoaboutou', 'block_monitoring');
	$strrequest  = get_string('requestdeclare', 'block_monitoring');
    $strqueue = get_string('queue', 'block_monitoring');
  
    $navlinks = array();
    $navlinks[] = array('name' => $strtitle, 'link' => "$CFG->wwwroot/blocks/monitoring/index.php", 'type' => 'misc');
    $navlinks[] = array('name' => 'Отчеты', 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header($SITE->shortname . ': '. $strtitle, $SITE->fullname, $navigation, "", "", true, "&nbsp;");
    
    include('tabs.php');

   	echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
    echo $strlistrayons;
    echo '</table>';

    if ($admin_is  || $region_operator_is || $rayon_operator_is)  {
        
        $toprow = array();
        $toprow[] = new tabobject(1, "reports.php?tab2=1&rid=$rid&sid=$sid&yid=$yid", 'Состояние очередности в ДОУ');
        $toprow[] = new tabobject(2, "reports.php?tab2=2&rid=$rid&sid=$sid&yid=$yid", 'Подсчет детей по параметрам');
        $tabs = array($toprow);
        print_tabs($tabs, $tab2, NULL, NULL);
        
        
        switch ($tab2)   {
            case 1:
                    $curdate = date('m.Y');
                    $table = table_reports1($rid);
                    $strtitle = "Состояние очередности в дошкольные учреждения <br />по $rayon->name <br />по состоянию на 01.$curdate года";
                    print_heading($strtitle, 'center', 4);
                    print_color_table($table);
            
            	// if ($admin_is || $region_operator_is) 	{
            		?>	<table align="center">
            			<tr>
            			<td>
            			<form name="download" method="post" action="<?php echo "reports.php?action=excel&rid=$rid&yid=$yid" ?>">
            			    <div align="center">
            				<input type="submit" name="downloadexcel" value="<?php print_string("downloadexcel")?>">
            			    </div>
            		  </form>
            			</td>		
            			</tr>
            		  </table>
            		<?php
                // }
            break;
            
            case 2:
            
                    $scriptname = "reports.php";
                    
                    echo '<form name="userreportform" action="'.$scriptname.'" method="post">';
                    echo '<input type="hidden" name="rid" value="'. $rid . '">';
                    echo '<input type="hidden" name="yid" value="'. $yid . '">';
                    echo '<input type="hidden" name="tab" value="'. $tab . '">';
                    echo '<input type="hidden" name="tab2" value="'. $tab2 . '">';
                    
            		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
                    
                      $strtitle = 'Все населенные пункты';
                      $teachermenu = array();
                    
                      $teachermenu[0] = $strtitle;
                      $teachermenu[1] = get_string('typesettlement1', 'block_monitoring');
                      $teachermenu[2] = get_string('typesettlement2', 'block_monitoring');
                    
                      echo '<tr><td>'.get_string('typesettlement', 'block_monitoring').':</td><td>';
                      choose_from_menu($teachermenu, "tsid", $tsid, "", "", "", false);
                      echo '</td></tr>';
                      
					  $strbirthday  = " с " . print_date_selector('bdaystart', 'bmonthstart', 'byearstart', $birthdaystart, true);
                      $strbirthday .= " по " . print_date_selector('bdayend', 'bmonthend', 'byearend', $birthdayend, true);
                      
                    echo '<tr valign="top"><td align="left">Период даты рождения:</td><td align="left">';                      
                    echo $strbirthday;
                    echo '<input name="bdateoff" type="checkbox" value="1" '.$bdateoff.' /><label>Отключить</label></span>'; 
                    echo '</td></tr>';

					  $strbirthday  = " с " . print_date_selector('rdaystart', 'rmonthstart', 'ryearstart', $requeststart, true);
                      $strbirthday .= " по " . print_date_selector('rdayend', 'rmonthend', 'ryearend', $requestend, true);
                      
                    echo '<tr valign="top"><td align="left">Период регистрации заявки:</td><td align="left">';                      
                    echo $strbirthday;
                    echo '<input name="rbdateoff" type="checkbox" value="1" '.$rbdateoff.' /><label>Отключить</label></span>';  // checked="checked"
                    echo '</td></tr>';

                    echo '<tr><td colspan="2" align="center">';
                    echo '<input type="submit" value="Применить" />';
                    echo '</td></tr>';
                    
                    // listbox_typesettlement($scriptname."?tab2=2&rid=$rid&sid=$sid&yid=$yid&tsid=", $tsid);
		            echo '</table>';
                    echo '</form>';
                    
                    $curdate = date('d.m.Y');
                    $table = table_reports2($rid, 14, $tsid, $selectbd, $selectqr);
                    $strtitle = "Список детей, находящихся в очереди <br />по $rayon->name <br />по состоянию на $curdate года";
                    print_heading($strtitle, 'center', 4);
                    $strtitle = 'Всего: '. $table->vsego; //  . ' детей'
                    print_heading($strtitle, 'center', 4); 
                    print_color_table($table);
            
            	// if ($admin_is || $region_operator_is) 	{
            		?>	<table align="center">
            			<tr>
            			<td>
            			<form name="download" method="post" action="<?php echo "reports.php?action=excel&rid=$rid&yid=$yid" ?>">
            			    <div align="center">
            				<input type="submit" name="downloadexcel" value="<?php print_string("downloadexcel")?>">
            			    </div>
            		  </form>
            			</td>		
            			</tr>
            		  </table>
            		<?php
                // }
            
            break;
        }
        
    }
    print_footer();



function table_reports1($rid)
{   
    global $CFG, $rayon;
    
    $numberf = get_string('symbolnumber', 'block_monitoring');;
    
    $curdate       = date('d-m-Y');
    $explode_month = explode('-', $curdate);
    $prev_month    = $explode_month[1] - 1;
    $cur_month     = $explode_month[1];
    
    $strdate1 = "01-0$prev_month-$explode_month[2]";
    $strdate2 = "01-$cur_month-$explode_month[2]";


    $table->head  = array ($numberf, get_string('pokazateli', 'block_mou_ege'), get_string('edizmer', 'block_mou_ege'), 
                            get_string('na1prosh', 'block_mou_ege') . '<br />(' . str_replace('-', '.', $strdate1) . ')', 
                            get_string('na1tekush', 'block_mou_ege'). '<br />(' . str_replace('-', '.', $strdate2). ')');
    $table->align = array ('center', 'left', 'center', 'center', 'center');
	$table->width = '70%';
    $table->size = array ('5%', '40%', '15%', '15%', '15%');
    $table->columnwidth = array (3, 70, 14, 23, 23);
	$table->class = 'moutable';

    $curdate = date('m.Y');

   	$table->titlesrows = array(20, 20, 20);
    $table->titles = array();
    $table->titles[] = "Состояние очередности в дошкольные учреждения"; 
	$table->titles[] = "по $rayon->name"; 
    $table->titles[] = "по состоянию на 01.$curdate года";
    $table->downloadfilename = "report_{$rid}";
    $table->worksheetname = $table->downloadfilename;
    

    $count = $count2 =0;

    
    $timestamp1 = strtotime($strdate1);    
    $timestamp2 = strtotime($strdate2);   
    $timestamp3 = strtotime("01-01-$explode_month[2]");

    if($people = get_records_sql("SELECT id, birthyear, edutypeid FROM {$CFG->prefix}monit_queue_request 
                                    WHERE rayonid=$rid and timecreated<='$timestamp1' and edutypeid=18 and birthyear>=2006 and birthyear<=2010")){
        $count = count($people);
    }

    if($people2 = get_records_sql("SELECT id, birthyear, edutypeid FROM {$CFG->prefix}monit_queue_request 
                                    WHERE rayonid=$rid and timecreated<='$timestamp2' and edutypeid=18 and birthyear>=2006 and birthyear<=2010")){
        $count2 = count($people2);
    }
    
    
	$table->data[] = array (1, get_string('pokazateli_description', 'block_mou_ege'), 
                               get_string('chelovek', 'block_mou_ege'), 
                               $count, 
                               $count2);
    return $table;                               
}	




function table_reports2($rid, $status, $tsid, $selectbd, $selectqr)
{   
    global $CFG, $rayon;
    
   
    if ($tsid == 0 ) {
        $select = '';
    } else if ($tsid == 1 ) {
        $select = " and dou.typesettlement=$tsid";
    } else {
        $select = " and dou.typesettlement >= $tsid";
    }

    $strstatus = get_string('status', 'block_monitoring');
    $table->head  = array ($strstatus, '№', 'ФИО ребенка', 'Д.р. ребенка', get_string('datetimerequest', 'block_monitoring'));
	$table->align = array ('center', 'center', 'left', 'center',  "center");
	$table->columnwidth = array (10, 7, 10, 10, 10);
    $table->class = 'moutable';
   	// $table->width = '95%';
    $table->titles = array();
    $table->titles[] = get_string('queue', 'block_monitoring');
    $table->worksheetname = '';

    $statuses = get_records_select ('monit_status', 'isqueue = 1');

    if ($ostatus = get_record('monit_status', 'id', $status)) {     
        $strstatus = '<b>'.$ostatus->name.'</b>';
        $strcolor =  $ostatus->color;
    }
 
    $strsql = "SELECT c.id, lastname, firstname, secondname, birthday, timecreated, status
                FROM mdl_monit_queue_request qr
                inner join mdl_monit_queue_child c on c.id=qr.childid
                inner join mdl_monit_education dou on dou.id = qr.oid
                where qr.rayonid=$rid and status=$status $select $selectbd $selectqr
                group by childid
                order by lastname"; 
    if($requests = get_records_sql($strsql))  {
        $i = 1;
        foreach($requests as $child) {
            $child_name = $child->lastname.' '.$child->firstname.' '.$child->secondname;
            $birthday = date('d.m.Y', $child->birthday);
            $date       = date('d.m.Y г. h:i', $child->timecreated);
            $table->data[] = array($strstatus, $i++, $child_name, $birthday, $date);
            $table->bgcolor[] = array ($strcolor);
        }
    } 
    
    $table->vsego  = $i-1;
    
    // print_object($table);                   
    
    return $table;                               
}	




function listbox_typesettlement($scriptname, $tsid) 
{
  global $CFG;

  $strtitle = 'Все населенные пункты';
  $teachermenu = array();

  $teachermenu[0] = $strtitle;
  $teachermenu[1] = get_string('typesettlement1', 'block_monitoring');
  $teachermenu[2] = get_string('typesettlement2', 'block_monitoring');

  echo '<tr><td>'.get_string('typesettlement', 'block_monitoring').':</td><td>';
  popup_form($scriptname, $teachermenu, "switchts", $tsid, "", "", "", false);
  echo '</td></tr>';
  return 1;
}

?>